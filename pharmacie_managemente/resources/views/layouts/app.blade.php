<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | PharmaCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">
    @auth
        <!-- Navigation -->
        <nav class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <i class="fas fa-pills text-blue-600 text-2xl mr-2"></i>
                            <span class="font-bold text-xl text-gray-800">PharmaCare</span>
                        </div>
                        <div class="hidden md:ml-8 md:flex md:space-x-6">
                            {{-- <a href="{{ route('dashboard') }}"
                                    class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-home mr-1"></i>Dashboard
                                </a> --}}

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

                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600 hidden md:block">
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full {{ Auth::user()->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ Auth::user()->isAdmin() ? 'Admin' : 'Employé' }}
                            </span>
                        </span>

                        <a href="{{ route('ventes.create') }}"
                            class="bg-green-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-600">
                            <i class="fas fa-plus mr-1"></i>Nouvelle Vente
                        </a>

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
