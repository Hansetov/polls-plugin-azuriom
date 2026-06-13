@extends('layouts.app')

@section('title', $poll->title)

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('polls.index') }}" class="btn btn-link px-0 mb-2">
                <i class="fas fa-arrow-left"></i> {{ trans('polls::messages.back') }}
            </a>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0">{{ $poll->title }}</h1>

                    <span class="badge {{ $poll->isOpen() ? 'bg-success' : 'bg-secondary' }}">
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
                        <p>{{ $poll->description }}</p>
                    @endif

                    @if ($poll->isOpen() && ! $hasVoted)
                        <form action="{{ route('polls.vote', $poll) }}" method="POST">
                            @csrf

                            @if ($poll->multiple_choice)
                                <p class="text-muted small">{{ trans('polls::messages.multiple_choice_hint') }}</p>
                            @endif

                            @foreach ($poll->options as $option)
                                <div class="form-check mb-2">
                                    @if ($poll->multiple_choice)
                                        <input class="form-check-input" type="checkbox" name="options[]" value="{{ $option->id }}" id="option-{{ $option->id }}">
                                    @else
                                        <input class="form-check-input" type="radio" name="option" value="{{ $option->id }}" id="option-{{ $option->id }}" required>
                                    @endif

                                    <label class="form-check-label" for="option-{{ $option->id }}">
                                        {{ $option->label }}
                                    </label>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary mt-2">
                                {{ trans('polls::messages.vote') }}
                            </button>
                        </form>
                    @else
                        <h2 class="h6 text-muted">{{ trans('polls::messages.results') }}</h2>

                        @foreach ($poll->options as $option)
                            @php $percentage = $option->percentage($totalVotes); @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        {{ $option->label }}
                                        @if (in_array($option->id, $userOptionIds))
                                            <i class="fas fa-check-circle text-success" title="{{ trans('polls::messages.vote') }}"></i>
                                        @endif
                                    </span>
                                    <span>{{ $percentage }}% ({{ $option->votes_count }} {{ trans('polls::messages.votes') }})</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach

                        <p class="text-muted small mb-0">
                            {{ trans('polls::messages.total_votes') }}: {{ $totalVotes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
