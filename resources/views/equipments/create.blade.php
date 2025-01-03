@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Ajouter un équipement</h1>

    <form action="{{ route('equipments.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom de l'équipement</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>
@endsection
