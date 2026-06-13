<?php

namespace Azuriom\Plugin\Polls\Http\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Polls\Models\Poll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(): View
    {
        $polls = Poll::query()
            ->withCount('options')
            ->withSum('options', 'votes_count')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('polls::admin.index', ['polls' => $polls]);
    }

    public function create(): View
    {
        return view('polls::admin.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePoll($request);

        $poll = Poll::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'multiple_choice' => $data['multiple_choice'],
            'status' => Poll::STATUS_OPEN,
        ]);

        foreach ($data['options'] as $label) {
            $poll->options()->create(['label' => $label]);
        }

        return to_route('polls.admin.index')
            ->with('success', trans('polls::messages.admin.created'));
    }

    public function edit(Poll $poll): View
    {
        $poll->load('options');

        return view('polls::admin.edit', ['poll' => $poll]);
    }

    public function update(Request $request, Poll $poll): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'multiple_choice' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'new_options' => ['nullable', 'array'],
            'new_options.*' => ['nullable', 'string', 'max:255'],
        ]);

        $poll->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'multiple_choice' => $request->boolean('multiple_choice'),
        ]);

        foreach ($request->input('options', []) as $optionId => $label) {
            if (trim((string) $label) === '') {
                continue;
            }

            $poll->options()->where('id', $optionId)->update(['label' => $label]);
        }

        foreach ($request->input('new_options', []) as $label) {
            if (trim((string) $label) === '') {
                continue;
            }

            $poll->options()->create(['label' => $label]);
        }

        return to_route('polls.admin.edit', $poll)
            ->with('success', trans('polls::messages.admin.updated'));
    }

    public function toggleStatus(Poll $poll): RedirectResponse
    {
        $poll->update([
            'status' => $poll->isOpen() ? Poll::STATUS_CLOSED : Poll::STATUS_OPEN,
        ]);

        return to_route('polls.admin.index')
            ->with('success', trans('polls::messages.admin.status_updated'));
    }

    public function destroy(Poll $poll): RedirectResponse
    {
        $poll->delete();

        return to_route('polls.admin.index')
            ->with('success', trans('polls::messages.admin.deleted'));
    }

    public function destroyOption(Poll $poll, int $option): RedirectResponse
    {
        if ($poll->options()->count() <= 2) {
            return to_route('polls.admin.edit', $poll)
                ->with('error', trans('polls::messages.admin.min_options'));
        }

        $poll->options()->where('id', $option)->delete();

        return to_route('polls.admin.edit', $poll)
            ->with('success', trans('polls::messages.admin.option_deleted'));
    }

    private function validatePoll(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'multiple_choice' => ['nullable', 'boolean'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:255'],
        ]) + ['multiple_choice' => $request->boolean('multiple_choice')];
    }
}
