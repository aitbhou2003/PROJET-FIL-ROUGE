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

<body class="bg-gray-100 font-sans antialiased">
    @auth
        <!-- Navigation -->
        <nav class="bg-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <i class="fas fa-pills text-blue-600 text-2xl mr-2"></i>
                            <span class="font-bold text-xl text-gray-800">PharmaCare</span>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('dashboard.admin') }}"
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 hover:text-blue-600">
                                    Dashboard
                                </a>
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-blue-600">
                                    Employés
                                </a>
                            @else
                                <a href="{{ route('dashboard.employe') }}"
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 hover:text-blue-600">
                                    Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-4 text-sm text-gray-600">
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                            <span
                                class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                {{ Auth::user()->isAdmin() ? 'Admin' : 'Employé' }}
                            </span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
