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

                    <div id="options-container">
                        @foreach ($poll->options as $option)
                            <div class="input-group mb-2" data-option-id="{{ $option->id }}">
                                <span class="input-group-text">{{ trans_count('polls::messages.votes', $option->votes_count) }}</span>
                                <input type="text" name="options[{{ $option->id }}]" class="form-control" value="{{ old('options.'.$option->id, $option->label) }}" maxlength="255">

                                <button type="submit" form="delete-option-{{ $option->id }}" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="input-group">
                        <input type="text" id="new-option-input" class="form-control" placeholder="{{ trans('polls::messages.admin.new_option') }}" maxlength="255">

                        <button type="button" id="add-option" class="btn btn-outline-secondary">
                            <i class="bi bi-plus-lg"></i> {{ trans('polls::messages.admin.add_option') }}
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('polls::messages.admin.save') }}</button>
                <a href="{{ route('polls.admin.index') }}" class="btn btn-secondary">{{ trans('polls::messages.admin.cancel') }}</a>
            </form>

            <div id="delete-forms">
                @foreach ($poll->options as $option)
                    <form id="delete-option-{{ $option->id }}" action="{{ route('polls.admin.options.destroy', [$poll, $option]) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const optionsContainer = document.getElementById('options-container');
            const deleteForms = document.getElementById('delete-forms');
            const newOptionInput = document.getElementById('new-option-input');
            const addButton = document.getElementById('add-option');

            const storeUrl = '{{ route('polls.admin.options.store', $poll) }}';
            const csrfToken = '{{ csrf_token() }}';
            const zeroVotesLabel = '{{ trans_count('polls::messages.votes', 0) }}';
            const confirmDeleteText = '{{ trans('polls::messages.admin.confirm_delete_option') }}';
            const addOptionErrorText = '{{ trans('polls::messages.admin.add_option_error') }}';

            function appendOption(option) {
                const row = document.createElement('div');
                row.className = 'input-group mb-2';
                row.dataset.optionId = option.id;

                row.innerHTML = '<span class="input-group-text">' + zeroVotesLabel + '</span>'
                    + '<input type="text" name="options[' + option.id + ']" class="form-control" value="' + option.label.replace(/"/g, '&quot;') + '" maxlength="255">'
                    + '<button type="submit" form="delete-option-' + option.id + '" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>';

                row.querySelector('button').addEventListener('click', function (event) {
                    if (!confirm(confirmDeleteText)) {
                        event.preventDefault();
                    }
                });

                optionsContainer.appendChild(row);

                const form = document.createElement('form');
                form.id = 'delete-option-' + option.id;
                form.action = option.delete_url;
                form.method = 'POST';
                form.className = 'd-none';
                form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">'
                    + '<input type="hidden" name="_method" value="DELETE">';

                deleteForms.appendChild(form);
            }

            addButton.addEventListener('click', function () {
                const label = newOptionInput.value.trim();

                if (label === '') {
                    return;
                }

                addButton.disabled = true;

                fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ label: label }),
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        return response.json();
                    })
                    .then(function (option) {
                        appendOption(option);
                        newOptionInput.value = '';
                        newOptionInput.focus();
                    })
                    .catch(function () {
                        alert(addOptionErrorText);
                    })
                    .finally(function () {
                        addButton.disabled = false;
                    });
            });

            newOptionInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addButton.click();
                }
            });

            document.querySelectorAll('#options-container button').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (!confirm(confirmDeleteText)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
