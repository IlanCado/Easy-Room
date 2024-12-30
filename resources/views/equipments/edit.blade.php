@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Equipment</h1>
        <form action="{{ route('equipments.update', $equipment) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $equipment->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
