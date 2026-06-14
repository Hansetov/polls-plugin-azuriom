@extends('layouts.app')

@section('title', trans('polls::messages.title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <h1 class="h4 mb-0 fw-bold">{{ trans('polls::messages.title') }}</h1>
                </div>

                <div class="card-body">
                    @if ($polls->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inboxes display-4 d-block mb-3"></i>
                            <p class="mb-0">{{ trans('polls::messages.no_polls') }}</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($polls as $poll)
                                <a href="{{ route('polls.show', $poll) }}" class="list-group-item list-group-item-action py-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $poll->title }}</h5>

                                            @if ($poll->description)
                                                <p class="mb-1 text-muted">{{ Str::limit($poll->description, 120) }}</p>
                                            @endif

                                            <small class="text-muted">
                                                <i class="bi bi-people-fill"></i>
                                                {{ trans_count('polls::messages.votes', (int) ($poll->options_sum_votes_count ?? 0)) }}
                                            </small>
                                        </div>

                                        <span class="badge rounded-pill {{ $poll->isOpen() ? 'bg-success' : 'bg-secondary' }}">
                                            <i class="bi {{ $poll->isOpen() ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                                            {{ trans('polls::messages.status.'.$poll->status) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $polls->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
