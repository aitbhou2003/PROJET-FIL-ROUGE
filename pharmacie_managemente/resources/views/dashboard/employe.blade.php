@extends('layouts.app')

@section('title', 'Dashboard Employé')

@section('content')
    <div class="px-4 sm:px-0 text-center">
        <div class="bg-white shadow rounded-lg p-12">
            <i class="fas fa-cash-register text-6xl text-green-500 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Point de Vente</h2>
            <p class="text-gray-500 mb-8">Cliquez ci-dessous pour démarrer une nouvelle vente</p>

            <a href="#"
                class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:text-xl">
                <i class="fas fa-plus-circle mr-3"></i>Nouvelle Vente
            </a>
        </div>

        <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Mes ventes aujourd'hui</h3>
            </div>
            <div class="p-4 text-gray-500">
                Aucune vente enregistrée aujourd'hui
            </div>
        </div>
    </div>
@endsection
