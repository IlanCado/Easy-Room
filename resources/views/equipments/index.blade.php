@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Gestion des équipements</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <a href="{{ route('equipments.create') }}" class="btn btn-primary mb-3">Ajouter un équipement</a>

    @if ($equipments->isEmpty())
        <p>Aucun équipement trouvé.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipments as $equipment)
                    <tr>
                        <td>{{ $equipment->id }}</td>
                        <td>{{ $equipment->name }}</td>
                        <td>
                            <a href="{{ route('equipments.edit', $equipment->id) }}" class="btn btn-info btn-sm">Modifier</a>
                            <form action="{{ route('equipments.destroy', $equipment->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cet équipement ?');">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
