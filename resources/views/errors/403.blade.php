@extends('layouts.app')

@section('title', 'Accès refusé')

@section('content')
<div class="min-h-screen flex items-center justify-center py-16 px-4">
    <div class="text-center max-w-lg">
        <div class="text-9xl font-black text-orange-100 leading-none select-none">403</div>
        <div class="text-6xl mb-6">🔒</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Accès refusé</h1>
        <p class="text-gray-500 mb-8">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <a href="{{ route('home') }}"
           class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            🏠 Retour à l'accueil
        </a>
    </div>
</div>
@endsection
