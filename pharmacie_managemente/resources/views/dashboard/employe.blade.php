@extends('layouts.app')

@section('title', 'Dashboard Employé')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Mon Espace</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Bouton vente principal -->
        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg shadow-lg p-8 text-white text-center">
            <i class="fas fa-cash-register text-6xl mb-4 opacity-80"></i>
            <h2 class="text-2xl font-bold mb-4">Point de Vente</h2>
            <p class="mb-6 opacity-90">Cliquez ci-dessous pour démarrer une nouvelle vente</p>
            <a href="{{ route('ventes.create') }}" class="inline-block bg-white text-green-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-plus-circle mr-2"></i>Nouvelle Vente
            </a>
        </div>

        <!-- Mes stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Mes ventes aujourd'hui</h2>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-receipt text-4xl mb-2 opacity-30"></i>
                <p>Aucune vente enregistrée aujourd'hui</p>
            </div>
        </div>
    </div>
</div>
@endsection
