@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Ajouter une salle</h1>

        <!-- Affichage des erreurs -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Nom -->
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <!-- Capacité -->
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacité</label>
                <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity') }}" required>
            </div>

            <!-- Équipements -->
            <div class="mb-3">
                <label class="form-label">Équipements</label>
                <div class="form-check">
                    @foreach (\App\Models\Equipment::all() as $equipment)
                        <div class="mb-2">
                            <input type="checkbox" 
                                   name="equipments[]" 
                                   value="{{ $equipment->id }}" 
                                   id="equipment-{{ $equipment->id }}" 
                                   class="form-check-input"
                                   @if(is_array(old('equipments')) && in_array($equipment->id, old('equipments'))) checked @endif>
                            <label for="equipment-{{ $equipment->id }}" class="form-check-label">{{ $equipment->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image de la salle</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>

            <!-- Bouton -->
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
@endsection
