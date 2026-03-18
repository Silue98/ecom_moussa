@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🔑 Mot de passe oublié</h1>
            <p class="mt-2 text-gray-600">Entrez votre email et nous vous enverrons un lien de réinitialisation.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6">
            @foreach($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="votre@email.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition">
                    Envoyer le lien de réinitialisation
                </button>
            </form>
        </div>

        <p class="text-center mt-6 text-sm text-gray-600">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">← Retour à la connexion</a>
        </p>

    </div>
</div>
@endsection
