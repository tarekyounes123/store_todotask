@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Server Error</h5>
                </div>
                <div class="card-body text-center">
                    <h1>500</h1>
                    <h3>Something went wrong</h3>
                    <p class="text-muted">We're sorry, but something went wrong on our server. Our team has been notified and is working to fix the issue.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection