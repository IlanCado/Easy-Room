@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Room</h1>
        <form action="{{ route('rooms.update', $room) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $room->name }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ $room->description }}</textarea>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" name="capacity" id="capacity" class="form-control" value="{{ $room->capacity }}" required>
            </div><div class="mb-3">

              <label for="equipments" class="form-label">Equipments</label>
    <select name="equipments[]" id="equipments" class="form-control" multiple>
        @foreach (\App\Models\Equipment::all() as $equipment)
            <option value="{{ $equipment->id }}" 
                @if(isset($room) && $room->equipments->contains($equipment->id)) selected @endif>
                {{ $equipment->name }}
            </option>
        @endforeach
    </select>
</div>
         <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
