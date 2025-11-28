@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5 class="card-title">{{ __('Welcome!') }}</h5>
                    <p class="card-text">{{ __('You are logged in!') }}</p>

                    <div class="mt-4 d-grid d-md-block">
                        <a href="{{ route('tasks.index') }}" class="btn btn-primary me-2 mb-2 mb-md-0">{{ __('View Tasks') }}</a>
                        <a href="{{ route('chat.index') }}" class="btn btn-outline-primary">{{ __('View Chats') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
