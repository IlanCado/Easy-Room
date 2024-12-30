@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Equipments</h1>
        <a href="{{ route('equipments.create') }}" class="btn btn-primary mb-3">Add Equipment</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipments as $equipment)
                    <tr>
                        <td>{{ $equipment->name }}</td>
                        <td>
                            <a href="{{ route('equipments.edit', $equipment) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('equipments.destroy', $equipment) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
