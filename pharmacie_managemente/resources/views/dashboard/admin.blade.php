@extends('layouts.app')

@section('title', 'Dashboard Administrateur')

@section('content')
    <div class="px-4 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Tableau de bord</h1>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-euro-sign text-3xl text-green-500"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Chiffre d'affaires du jour</dt>
                                <dd class="text-2xl font-semibold text-gray-900">0,00 €</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-3xl text-blue-500"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ventes aujourd'hui</dt>
                                <dd class="text-2xl font-semibold text-gray-900">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Alertes stock</dt>
                                <dd class="text-2xl font-semibold text-gray-900">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-3xl text-purple-500"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Employés actifs</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::where('is_actif', true)->whereHas('role', fn($q) => $q->where('role', 'employe'))->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h2>
            <div class="flex flex-wrap gap-4">
                <a href="#"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Nouvelle vente
                </a>
                <a href="{{ route('medicaments.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-pills mr-2"></i>Gérer médicaments
                </a>
                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-user-plus mr-2"></i>Ajouter employé
                </a>
            </div>
        </div>
    </div>
@endsection
