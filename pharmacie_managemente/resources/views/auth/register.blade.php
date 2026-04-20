@extends('layouts.app')

@section('title', 'Nouvel Employé')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>Créer un nouvel employé
            </h1>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('firstname') border-red-500 @enderror" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('lastname') border-red-500 @enderror" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle <span class="text-red-500">*</span></label>
                <select name="role_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror" required>
                    <option value="">Sélectionner...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->role) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                {{-- <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a> --}}
                
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Créer l'employé
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
