<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | PharmaCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">
    @auth
        <!-- Navigation -->
        <nav class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- Left: Logo + Links -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <i class="fas fa-pills text-blue-600 text-2xl mr-2"></i>
                            <span class="font-bold text-xl text-gray-800">PharmaCare</span>
                        </div>
                        <div class="hidden md:ml-8 md:flex md:space-x-6">
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('medicaments.index') }}"
                                    class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-pills mr-1"></i>Médicaments
                                </a>
                                <a href="{{ route('stocks.index') }}"
                                    class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-boxes mr-1"></i>Stocks
                                </a>
                                <a href="{{ route('register') }}"
                                    class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-users mr-1"></i>Employés
                                </a>
                            @endif

                            <a href="{{ route('ventes.index') }}"
                                class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-cash-register mr-1"></i>Ventes
                            </a>
                        </div>
                    </div>

                    <!-- Right: Bell + User + Actions -->
                    <div class="flex items-center space-x-4">

                        @if (Auth::user()->isAdmin())
                            <div class="relative"
                                 x-data="{
                                     open: false,
                                     nonLues: 0,
                                     alertes: [],
                                     async charger() {
                                         const res = await fetch('{{ route('admin.alertes.dernieres') }}');
                                         const data = await res.json();
                                         this.nonLues = data.non_lues;
                                         this.alertes = data.alertes;
                                     }
                                 }"
                                 x-init="charger(); setInterval(() => charger(), 30000)">

                                <!-- Bell Button -->
                                <button @click="open = !open; charger()"
                                    class="relative p-2 text-gray-600 hover:text-blue-600 focus:outline-none">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span x-show="nonLues > 0" x-text="nonLues"
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    </span>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50"
                                     style="display: none;">

                                    <!-- Header -->
                                    <div class="p-3 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                                        <h3 class="font-bold text-gray-800 text-sm">🔔 Alertes</h3>
                                        <a href="{{ route('admin.alertes.index') }}"
                                            class="text-xs text-blue-600 hover:underline">Voir tout</a>
                                    </div>

                                    <!-- List -->
                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-if="alertes.length === 0">
                                            <div class="p-4 text-center text-gray-500">
                                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                                <p class="text-sm">Aucune alerte</p>
                                            </div>
                                        </template>
                                        <template x-for="a in alertes" :key="a.id">
                                            <div class="p-3 border-b hover:bg-gray-50"
                                                 :class="!a.is_read ? 'bg-yellow-50' : ''">
                                                <p class="text-sm text-gray-800" x-text="a.message"></p>
                                                <p class="text-xs text-gray-500 mt-1"
                                                   x-text="new Date(a.created_at).toLocaleString('fr-FR')"></p>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Footer -->
                                    <div class="p-3 border-t bg-gray-50 rounded-b-lg">
                                        <a href="{{ route('admin.alertes.index') }}"
                                            class="block text-center text-blue-600 hover:underline text-xs font-medium">
                                            Voir toutes les alertes →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- User Info -->
                        <span class="text-sm text-gray-600 hidden md:block">
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                            <span class="ml-2 px-2 py-1 text-xs rounded-full
                                {{ Auth::user()->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ Auth::user()->isAdmin() ? 'Admin' : 'Employé' }}
                            </span>
                        </span>

                        <!-- New Sale Button -->
                        <a href="{{ route('ventes.create') }}"
                            class="bg-green-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-600">
                            <i class="fas fa-plus mr-1"></i>Nouvelle Vente
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </nav>
    @endauth

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>

    @stack('scripts')

</body>

</html>