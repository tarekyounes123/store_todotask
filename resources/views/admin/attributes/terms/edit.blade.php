@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Edit Term for Attribute: {{ $attribute->name }}</h1>
        <form action="{{ route('admin.attributes.terms.update', [$attribute, $term]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="value" class="form-label">Term Value</label>
                <input type="text" class="form-control" id="value" name="value" value="{{ old('value', $term->value) }}" required>
                @error('value')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update Term</button>
            <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
