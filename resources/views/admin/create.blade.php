@extends('admin.layouts.admin')

@section('title', trans('polls::messages.admin.create'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="h4 mb-0">{{ trans('polls::messages.admin.create') }}</h1>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('polls.admin.store') }}" method="POST" id="poll-form">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">{{ trans('polls::messages.title') }}</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ trans('polls::messages.admin.description') }}</label>
                    <textarea name="description" id="description" class="form-control" rows="3" maxlength="2000">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="multiple_choice" id="multiple_choice" class="form-check-input" value="1" {{ old('multiple_choice') ? 'checked' : '' }}>
                    <label for="multiple_choice" class="form-check-label">{{ trans('polls::messages.admin.multiple_choice') }}</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ trans('polls::messages.admin.options') }}</label>

                    <div id="options-container">
                        @php $oldOptions = old('options', ['', '']); @endphp

                        @foreach ($oldOptions as $index => $value)
                            <div class="input-group mb-2 option-row">
                                <input type="text" name="options[]" class="form-control" value="{{ $value }}" placeholder="{{ trans('polls::messages.admin.option') }} {{ $index + 1 }}" maxlength="255" required>
                                <button type="button" class="btn btn-outline-danger remove-option" {{ count($oldOptions) <= 2 ? 'disabled' : '' }}>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-option" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="fas fa-plus"></i> {{ trans('polls::messages.admin.add_option') }}
                    </button>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('polls::messages.admin.save') }}</button>
                <a href="{{ route('polls.admin.index') }}" class="btn btn-secondary">{{ trans('polls::messages.admin.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('options-container');
            const addButton = document.getElementById('add-option');

            function refreshRemoveButtons() {
                const rows = container.querySelectorAll('.option-row');
                rows.forEach(function (row) {
                    const removeButton = row.querySelector('.remove-option');
                    removeButton.disabled = rows.length <= 2;
                });
            }

            addButton.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'input-group mb-2 option-row';
                row.innerHTML = '<input type="text" name="options[]" class="form-control" placeholder="{{ trans("polls::messages.admin.option") }}" maxlength="255" required>'
                    + '<button type="button" class="btn btn-outline-danger remove-option"><i class="fas fa-times"></i></button>';

                container.appendChild(row);
                refreshRemoveButtons();
            });

            container.addEventListener('click', function (event) {
                const button = event.target.closest('.remove-option');

                if (!button) {
                    return;
                }

                const rows = container.querySelectorAll('.option-row');

                if (rows.length > 2) {
                    button.closest('.option-row').remove();
                    refreshRemoveButtons();
                }
            });

            refreshRemoveButtons();
        });
    </script>
@endsection
