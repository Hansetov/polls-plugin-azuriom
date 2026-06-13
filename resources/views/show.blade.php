@extends('layouts.app')

@section('title', $poll->title)

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('polls.index') }}" class="btn btn-link px-0 mb-2">
                <i class="bi bi-arrow-left"></i> {{ trans('polls::messages.back') }}
            </a>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0 fw-bold">{{ $poll->title }}</h1>

                    <span class="badge rounded-pill {{ $poll->isOpen() ? 'bg-success' : 'bg-secondary' }}">
                        <i class="bi {{ $poll->isOpen() ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                        {{ trans('polls::messages.status.'.$poll->status) }}
                    </span>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($poll->description)
                        <p class="text-muted">{{ $poll->description }}</p>
                    @endif

                    @if ($poll->isOpen() && ! $hasVoted)
                        <form action="{{ route('polls.vote', $poll) }}" method="POST">
                            @csrf

                            @if ($poll->multiple_choice)
                                <p class="text-muted small">
                                    <i class="bi bi-info-circle"></i>
                                    {{ trans('polls::messages.multiple_choice_hint') }}
                                </p>
                            @endif

                            <div class="d-flex flex-column gap-2">
                                @foreach ($poll->options as $option)
                                    <label class="poll-option d-flex align-items-center gap-2 p-3 border rounded-3" for="option-{{ $option->id }}">
                                        @if ($poll->multiple_choice)
                                            <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="options[]" value="{{ $option->id }}" id="option-{{ $option->id }}">
                                        @else
                                            <input class="form-check-input m-0 flex-shrink-0" type="radio" name="option" value="{{ $option->id }}" id="option-{{ $option->id }}" required>
                                        @endif

                                        <span>{{ $option->label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-check2-circle"></i> {{ trans('polls::messages.vote') }}
                            </button>
                        </form>
                    @else
                        <h2 class="h6 text-muted text-uppercase mb-3">{{ trans('polls::messages.results') }}</h2>

                        @php $maxVotes = $poll->options->max('votes_count'); @endphp

                        <div class="d-flex flex-column gap-3">
                            @foreach ($poll->options as $option)
                                @php
                                    $percentage = $option->percentage($totalVotes);
                                    $isLeading = $totalVotes > 0 && $option->votes_count === $maxVotes;
                                @endphp

                                <div class="poll-result p-3 border rounded-3 {{ $isLeading ? 'border-success' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">
                                            {{ $option->label }}

                                            @if (in_array($option->id, $userOptionIds))
                                                <i class="bi bi-check-circle-fill text-success ms-1" title="{{ trans('polls::messages.vote') }}"></i>
                                            @endif

                                            @if ($isLeading)
                                                <i class="bi bi-trophy-fill text-warning ms-1"></i>
                                            @endif
                                        </span>

                                        <span class="text-muted small">
                                            {{ $percentage }}% &middot; {{ $option->votes_count }} {{ trans('polls::messages.votes') }}
                                        </span>
                                    </div>

                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar {{ $isLeading ? 'bg-success' : '' }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-muted small mt-3 mb-0">
                            <i class="bi bi-people-fill"></i>
                            {{ trans('polls::messages.total_votes') }}: {{ $totalVotes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .poll-option {
            cursor: pointer;
            transition: border-color .15s ease-in-out, background-color .15s ease-in-out;
        }

        .poll-option:hover {
            border-color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), .05);
        }

        .poll-option:has(input:checked) {
            border-color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), .08);
        }
    </style>
@endsection
