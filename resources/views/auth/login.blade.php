@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen gradient-background">
    <!-- Logo -->
    <div class="mb-6">
        <img src="{{ asset('logo.jpg') }}" class="h-20 w-auto" alt="Logo">
    </div>

    <!-- Formulaire de connexion -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-gray-800">Email</label>
            <input id="email" type="email" name="email" class="block mt-1 w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                value="{{ old('email') }}" required autofocus autocomplete="username">
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

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-700">Se souvenir de moi</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Connexion
            </button>
        </div>

        <!-- Lien vers l'inscription -->
        <p class="mt-4 text-center text-sm text-gray-700">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500 font-semibold">S'inscrire</a>
        </p>
    </form>
</div>
@endsection
