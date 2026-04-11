@extends('layouts.app')

@section('title', 'Gestion des Médicaments')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Liste des Médicaments</h1>
        <a href="{{ route('medicaments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nouveau Médicament
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="GET" action="{{ route('medicaments.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou code..."
                class="border px-4 py-2 rounded flex-1">

            <select name="categorie" class="border px-4 py-2 rounded">
                <option value="">Toutes les catégories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Filtrer
            </button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Image</th>
                    <th class="p-4">Nom</th>
                    <th class="p-4">Catégorie</th>
                    <th class="p-4">Code Barre</th>
                    <th class="p-4">Stock Total</th>
                    <th class="p-4">Prix Vente</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicaments as $med)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            @if ($med->image)
                                <img src="{{ $med->image }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-pills text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-medium">{{ $med->nom }}</td>
                        <td class="p-4">{{ $med->categorie->nom }}</td>
                        <td class="p-4 text-sm">{{ $med->code_barre }}</td>
                        <td class="p-4">
                            <span class="{{ $med->stockTotal() <= 10 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                {{ $med->stockTotal() }} unités
                            </span>
                        </td>
                        <td class="p-4">{{ number_format($med->stocks->first()->prix_vente ?? 0, 2) }} €</td>
                        <td class="p-4">
                            <a href="{{ route('medicaments.show', $med) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('medicaments.edit', $med) }}"
                                class="text-yellow-600 hover:text-yellow-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('medicaments.destroy', $med) }}" class="inline"
                                onsubmit="return confirm('Supprimer ce médicament ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $medicaments->links() }}
        </div>
    </div>
@endsection
