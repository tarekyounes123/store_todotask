@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('Start New Chat') }}</div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('chat.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="participants" class="form-label">{{ __('Select Users') }}</label>
                            <select name="participants[]" id="participants" class="form-control" multiple required style="min-height: 150px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Hold Ctrl (or Cmd on Mac) to select multiple users.') }}</div>
                        </div>

                        <div class="d-grid d-md-block">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Start Chat') }}</button>
                            <a href="{{ route('chat.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
