@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Rooms</h1>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary mb-3">Add Room</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Capacity</th>
                    <th>Equipments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td>{{ $room->name }}</td>
                        <td>{{ $room->description }}</td>
                        <td>{{ $room->capacity }}</td>
                        <td>
                            @if ($room->equipments->isNotEmpty())
                                <ul>
                                    @foreach ($room->equipments as $equipment)
                                        <li>{{ $equipment->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span>No Equipments</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-info">View</a>
                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display: inline;">
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
