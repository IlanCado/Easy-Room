@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen gradient-background">
    <!-- Logo -->
    <div class="mb-6">
        <img src="{{ asset('logo.jpg') }}" class="h-20 w-auto" alt="Logo">
    </div>

    <!-- Formulaire d'inscription -->
    <form method="POST" action="{{ route('register') }}" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-medium text-gray-800">Nom</label>
            <input id="name" type="text" name="name" class="block mt-1 w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-medium text-gray-800">Email</label>
            <input id="email" type="email" name="email" class="block mt-1 w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-gray-800">Mot de passe</label>
            <input id="password" type="password" name="password" class="block mt-1 w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white" required>
            @error('password')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-gray-800">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="block mt-1 w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white" required>
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-4">
            <a class="text-sm text-indigo-600 hover:text-indigo-500" href="{{ route('login') }}">
                Déjà inscrit ?
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                S'inscrire
            </button>
        </div>
    </form>
</div>
@endsection