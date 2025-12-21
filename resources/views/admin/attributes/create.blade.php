@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Attribute</h1>
        <form action="{{ route('admin.attributes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Attribute Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection