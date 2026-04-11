@extends('layouts.app')

@section('title', 'Nouveau Médicament')

@section('content')
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Ajouter un Médicament</h1>

        <form method="POST" action="{{ route('medicaments.store') }}">
            @csrf

            <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Informations Générales</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                    @error('nom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Catégorie *</label>
                    <select name="categorie_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">Choisir...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Code Barre *</label>
                <input type="text" name="code_barre" value="{{ old('code_barre') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fabricant *</label>
                    <input type="text" name="fabricant" value="{{ old('fabricant') }}"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Forme/Dosage *</label>
                    <input type="text" name="forme_dosage" value="{{ old('forme_dosage') }}"
                        placeholder="Ex: Comprimé 500mg" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="ordonnance_requise" value="1" class="mr-2"
                        {{ old('ordonnance_requise') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Ordonnance requise</span>
                </label>
            </div>

            <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Stock Initial</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">N° de Lot *</label>
                    <input type="text" name="numero_lot" value="{{ old('numero_lot') }}"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantité *</label>
                    <input type="number" name="quantite" value="{{ old('quantite') }}" min="1"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Seuil Minimum *</label>
                    <input type="number" name="seuil_minimum" value="{{ old('seuil_minimum', 10) }}" min="1"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix Achat *</label>
                    <input type="number" step="0.01" name="prix_achat" value="{{ old('prix_achat') }}"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix Vente *</label>
                    <input type="number" step="0.01" name="prix_vente" value="{{ old('prix_vente') }}"
                        class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">description *</label>
                <input type="text" name="description" value="{{ old('description') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Date Expiration *</label>
                <input type="date" name="date_expiration" value="{{ old('date_expiration') }}"
                    class="mt-1 w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('medicaments.index') }}" class="text-gray-600 hover:text-gray-900">Annuler</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
