@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $room->name }}</h1>
        <p><strong>Description:</strong> {{ $room->description }}</p>
        <p><strong>Capacity:</strong> {{ $room->capacity }}</p>

        <h3>Equipments</h3>
        @if ($room->equipments->isNotEmpty())
            <ul>
                @foreach ($room->equipments as $equipment)
                    <li>{{ $equipment->name }}</li>
                @endforeach
            </ul>
        @else
            <p>No Equipments Assigned</p>
        @endif

        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
@endsection
