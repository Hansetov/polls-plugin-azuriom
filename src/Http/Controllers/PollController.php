<?php

namespace Azuriom\Plugin\Polls\Http\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Polls\Models\Poll;
use Azuriom\Plugin\Polls\Models\PollOption;
use Azuriom\Plugin\Polls\Models\PollVote;
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

        return view('polls::index', ['polls' => $polls]);
    }

    public function show(Poll $poll): View
    {
        $poll->load('options');

        $user = request()->user();

        $hasVoted = $poll->hasVoted($user);

        $userOptionIds = [];

        if ($hasVoted) {
            $userOptionIds = PollVote::query()
                ->where('poll_id', $poll->id)
                ->where('user_id', $user->id)
                ->pluck('poll_option_id')
                ->all();
        }

        return view('polls::show', [
            'poll' => $poll,
            'hasVoted' => $hasVoted,
            'userOptionIds' => $userOptionIds,
            'totalVotes' => $poll->totalVotes(),
        ]);
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        if (! $poll->isOpen()) {
            return redirect()->route('polls.show', $poll)
                ->with('error', trans('polls::messages.closed'));
        }

        if ($poll->hasVoted($request->user())) {
            return redirect()->route('polls.show', $poll)
                ->with('error', trans('polls::messages.already_voted'));
        }

        if ($poll->multiple_choice) {
            $request->validate([
                'options' => ['required', 'array', 'min:1'],
                'options.*' => ['integer', 'exists:polls_poll_options,id'],
            ]);

            $optionIds = $request->input('options');
        } else {
            $request->validate([
                'option' => ['required', 'integer', 'exists:polls_poll_options,id'],
            ]);

            $optionIds = [$request->input('option')];
        }

        $options = PollOption::query()
            ->where('poll_id', $poll->id)
            ->whereIn('id', $optionIds)
            ->get();

        if ($options->count() !== count($optionIds) || $options->isEmpty()) {
            return redirect()->route('polls.show', $poll)
                ->with('error', trans('polls::messages.invalid_option'));
        }

        foreach ($options as $option) {
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $option->id,
                'user_id' => $request->user()->id,
            ]);

            $option->increment('votes_count');
        }

        return redirect()->route('polls.show', $poll)
            ->with('success', trans('polls::messages.vote_success'));
    }
}
