@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Attribute: {{ $attribute->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Attribute Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update Attribute</button>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a>
        </form>

        <h2 class="mt-4">Attribute Terms</h2>
        <a href="{{ route('admin.attributes.terms.create', $attribute) }}" class="btn btn-success mb-3">Add New Term</a>

        @if ($attribute->terms->isEmpty())
            <p>No terms defined for this attribute.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Value</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attribute->terms as $term)
                        <tr>
                            <td>{{ $term->value }}</td>
                            <td>{{ $term->slug }}</td>
                            <td>
                                <a href="{{ route('admin.attributes.terms.edit', [$attribute, $term]) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.attributes.terms.destroy', [$attribute, $term]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection