@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Modifier l'équipement</h1>

    <form action="{{ route('equipments.update', $equipment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nom de l'équipement</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $equipment->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
