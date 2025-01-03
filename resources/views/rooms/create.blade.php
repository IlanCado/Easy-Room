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
            <!-- Nom de la salle -->
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
                <label for="equipments" class="form-label">Équipements</label>
                <select name="equipments[]" id="equipments" class="form-control" multiple>
                    @forelse (\App\Models\Equipment::all() as $equipment)
                        <option value="{{ $equipment->id }}" 
                            @if(isset($room) && $room->equipments->contains($equipment->id)) selected @endif>
                            {{ $equipment->name }}
                        </option>
                    @empty
                        <option disabled>Aucun équipement disponible</option>
                    @endforelse
                </select>
            </div>

            <!-- Image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image de la salle</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
@endsection
