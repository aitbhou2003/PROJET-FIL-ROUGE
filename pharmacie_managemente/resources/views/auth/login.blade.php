@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="bg-white rounded-lg shadow-xl p-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
            <i class="fas fa-pills text-3xl text-blue-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">PharmaCare</h1>
        <p class="text-gray-500 text-sm mt-1">Système de Gestion Pharmacie</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="admin@pharmacie.com" required autofocus>
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" name="password" id="password" 
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="••••••••" required>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
        </button>
    </form>

    <div class="mt-6 text-center text-xs text-gray-400">
        <p>Admin: admin@pharmacie.com / password</p>
        <p>Employé: employe@pharmacie.com / password</p>
    </div>
</div>
@endsection
