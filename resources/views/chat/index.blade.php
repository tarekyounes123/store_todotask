@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h5 class="mb-0">{{ __('Chats') }}</h5>
                    <a href="{{ route('chat.create') }}" class="btn btn-primary mb-2 mb-md-0">{{ __('Start New Chat') }}</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($chats->count())
                        <div class="list-group">
                            @foreach($chats as $chat)
                                <div class="list-group-item list-group-item-action">
                                    <a href="{{ route('chat.show', $chat->id) }}" class="text-decoration-none d-block">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-truncate">
                                                @foreach($chat->users as $user)
                                                    @if($user->id !== Auth::id())
                                                        {{ $user->name }}@if(!$loop->last), @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center mb-0">{{ __('You have no chats yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
