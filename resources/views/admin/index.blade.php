@extends('admin.layouts.admin')

@section('title', trans('polls::messages.admin.title'))

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ trans('polls::messages.admin.title') }}</h1>

            <a href="{{ route('polls.admin.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> {{ trans('polls::messages.admin.create') }}
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($polls->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">{{ trans('polls::messages.admin.no_polls') }}</p>
                    <a href="{{ route('polls.admin.create') }}" class="btn btn-primary">
                        {{ trans('polls::messages.admin.create_first') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle" style="table-layout: fixed;">
                        <colgroup>
                            <col>
                            <col style="width: 100px;">
                            <col style="width: 110px;">
                            <col style="width: 110px;">
                            <col style="width: 180px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>{{ trans('polls::messages.title') }}</th>
                                <th>{{ trans('polls::messages.admin.options') }}</th>
                                <th>{{ trans('polls::messages.admin.status') }}</th>
                                <th>{{ trans('polls::messages.total_votes') }}</th>
                                <th class="text-end">{{ trans('polls::messages.admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($polls as $poll)
                                <tr>
                                    <td style="word-break: break-word;">{{ $poll->title }}</td>
                                    <td>{{ $poll->options_count }}</td>
                                    <td>
                                        <span class="badge {{ $poll->isOpen() ? 'bg-success' : 'bg-secondary' }}">
                                            {{ trans('polls::messages.status.'.$poll->status) }}
                                        </span>
                                    </td>
                                    <td>{{ (int) ($poll->options_sum_votes_count ?? 0) }}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                            <a href="{{ route('polls.show', $poll) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="{{ trans('polls::messages.title') }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a href="{{ route('polls.admin.edit', $poll) }}" class="btn btn-sm btn-outline-primary" title="{{ trans('polls::messages.admin.edit_action') }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <form action="{{ route('polls.admin.toggle', $poll) }}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ $poll->isOpen() ? trans('polls::messages.admin.close') : trans('polls::messages.admin.open') }}">
                                                    <i class="bi {{ $poll->isOpen() ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('polls.admin.destroy', $poll) }}" method="POST" onsubmit="return confirm('{{ trans('polls::messages.admin.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('polls::messages.admin.delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $polls->links() }}
            @endif
        </div>
    </div>
@endsection
