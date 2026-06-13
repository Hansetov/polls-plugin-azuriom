@extends('admin.layouts.admin')

@section('title', trans('polls::messages.admin.edit'))

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">{{ trans('polls::messages.admin.edit') }}</h1>

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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('polls.admin.update', $poll) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">{{ trans('polls::messages.title') }}</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $poll->title) }}" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ trans('polls::messages.admin.description') }}</label>
                    <textarea name="description" id="description" class="form-control" rows="3" maxlength="2000">{{ old('description', $poll->description) }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="multiple_choice" id="multiple_choice" class="form-check-input" value="1" {{ old('multiple_choice', $poll->multiple_choice) ? 'checked' : '' }}>
                    <label for="multiple_choice" class="form-check-label">{{ trans('polls::messages.admin.multiple_choice') }}</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ trans('polls::messages.admin.options') }}</label>

                    @foreach ($poll->options as $option)
                        <div class="input-group mb-2">
                            <span class="input-group-text">{{ $option->votes_count }} {{ trans('polls::messages.votes') }}</span>
                            <input type="text" name="options[{{ $option->id }}]" class="form-control" value="{{ old('options.'.$option->id, $option->label) }}" maxlength="255">

                            <button type="submit" form="delete-option-{{ $option->id }}" class="btn btn-outline-danger" onclick="return confirm('{{ trans('polls::messages.admin.confirm_delete_option') }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach

                    <hr>

                    <label class="form-label">{{ trans('polls::messages.admin.new_option') }}</label>
                    <div id="new-options-container">
                        <div class="input-group mb-2">
                            <input type="text" name="new_options[]" class="form-control" placeholder="{{ trans('polls::messages.admin.new_option') }}" maxlength="255">
                        </div>
                    </div>

                    <button type="button" id="add-option" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="fas fa-plus"></i> {{ trans('polls::messages.admin.add_option') }}
                    </button>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('polls::messages.admin.save') }}</button>
                <a href="{{ route('polls.admin.index') }}" class="btn btn-secondary">{{ trans('polls::messages.admin.cancel') }}</a>
            </form>

            @foreach ($poll->options as $option)
                <form id="delete-option-{{ $option->id }}" action="{{ route('polls.admin.options.destroy', [$poll, $option]) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('new-options-container');
            const addButton = document.getElementById('add-option');

            addButton.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'input-group mb-2';
                row.innerHTML = '<input type="text" name="new_options[]" class="form-control" placeholder="{{ trans("polls::messages.admin.new_option") }}" maxlength="255">';

                container.appendChild(row);
            });
        });
    </script>
@endsection
