@extends('layouts.app')

@section('title', 'Liste des Médicaments')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Médicaments</h1>
        <a href="{{ route('medicaments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nouveau médicament
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                class="border rounded-lg px-4 py-2 flex-1">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4">Image</th> {{-- ajouter ça --}}
                    <th class="p-4">Nom</th>
                    <th class="p-4">Catégorie</th>
                    <th class="p-4">Code Barre</th>
                    <th class="p-4">Stock Total</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicaments as $med)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            @if ($med->image)
                                <img src="{{ asset('storage/' . $med->image) }}"
                                    class="w-12 h-12 object-cover rounded-lg" />
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-pills text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-medium">{{ $med->nom }}</td>
                        <td class="p-4">{{ $med->categorie->nom }}</td>
                        <td class="p-4 text-sm font-mono">{{ $med->code_barre }}</td>
                        <td class="p-4">
                            <span class="{{ $med->stockTotal() <= 10 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                {{ $med->stockTotal() }}
                            </span>
                        </td>
                        <td class="p-4 flex space-x-2">
                            <a href="{{ route('medicaments.show', $med) }}" class="text-blue-600 hover:text-blue-900"
                                title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('medicaments.edit', $med) }}" class="text-yellow-600 hover:text-yellow-900"
                                title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('medicaments.destroy', $med) }}" class="inline"
                                onsubmit="return confirm('Supprimer ce médicament ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">Aucun médicament trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $medicaments->links() }}
    </div>
@endsection
