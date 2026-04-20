@extends('layouts.app')

@section('title', 'Dashboard Administrateur')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-gray-900">0,00 €</p>
                </div>
                <i class="fas fa-euro-sign text-3xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Ventes aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
                <i class="fas fa-shopping-cart text-3xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Alertes stock</p>
                    <p class="text-2xl font-bold text-red-600">0</p>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-red-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Médicaments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Medicament::count() }}</p>
                </div>
                <i class="fas fa-pills text-3xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold mb-4">Actions rapides</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('ventes.create') }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-medium">
                <i class="fas fa-cash-register mr-2"></i>Nouvelle vente
            </a>
            <a href="{{ route('medicaments.create') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 font-medium">
                <i class="fas fa-plus mr-2"></i>Ajouter médicament
            </a>
            <a href="{{ route('register') }}" class="bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 font-medium">
                <i class="fas fa-user-plus mr-2"></i>Nouvel employé
            </a>
        </div>
    </div>
</div>
@endsection
