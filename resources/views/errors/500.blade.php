@extends('layouts.app')

@section('title', 'Erreur serveur')

@section('content')
<div class="min-h-screen flex items-center justify-center py-16 px-4">
    <div class="text-center max-w-lg">
        <div class="text-9xl font-black text-red-100 leading-none select-none">500</div>
        <div class="text-6xl mb-6">⚠️</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Erreur serveur</h1>
        <p class="text-gray-500 mb-8">Une erreur inattendue s'est produite. Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.</p>
        <a href="{{ route('home') }}"
           class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            🏠 Retour à l'accueil
        </a>
    </div>
</div>
@endsection
