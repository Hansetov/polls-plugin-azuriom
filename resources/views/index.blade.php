@extends('layouts.app')

@section('title', trans('polls::messages.title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">{{ trans('polls::messages.title') }}</h1>
                </div>

                <div class="card-body">
                    @if ($polls->isEmpty())
                        <p class="text-center text-muted mb-0">
                            {{ trans('polls::messages.no_polls') }}
                        </p>
                    @else
                        <div class="list-group">
                            @foreach ($polls as $poll)
                                <a href="{{ route('polls.show', $poll) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $poll->title }}</h5>
                                        @if ($poll->description)
                                            <p class="mb-1 text-muted">{{ Str::limit($poll->description, 120) }}</p>
                                        @endif
                                    </div>

                                    <span class="badge {{ $poll->isOpen() ? 'bg-success' : 'bg-secondary' }}">
                                        {{ trans('polls::messages.status.'.$poll->status) }}
                                    </span>
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
