@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Error</h5>
                </div>
                <div class="card-body text-center">
                    <h3>Something went wrong</h3>
                    <p class="text-muted">We're sorry, but something went wrong. Please try again or contact support if the problem persists.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection