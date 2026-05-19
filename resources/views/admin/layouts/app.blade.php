<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Administration') | {{ config('app.name') }}</title>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            body {
                font-family: 'Figtree', sans-serif;
            }
        </style>

        @vite(['resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <div class="flex h-screen">
            <!-- Sidebar avec style inline pour garantir la visibilité -->
            <aside class="w-64 flex-shrink-0" style="background-color: #1e293b; color: white;">
                <div class="p-4" style="border-bottom: 1px solid #334155;">
                    <h1 class="text-xl font-bold">CGS - Admin</h1>
                </div>
                <nav class="p-4">
                    <ul class="space-y-2">
                        @can('voir dashboard')
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('dashboard')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span style="color: white;">Tableau de bord</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir agents')
                        <li>
                            <a href="{{ route('admin.agents.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.agents.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                                <span style="color: white;">Agents</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir utilisateurs')
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.users.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span style="color: white;">Utilisateurs</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir partenaires')
                        <li>
                            <a href="{{ route('admin.partenaires.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.partenaires.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span style="color: white;">Partenaires</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir imports')
                        <li>
                            <a href="{{ route('admin.import.agents') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.import.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span style="color: white;">Import SAP</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir demandes')
                        <li>
                            <a href="{{ route('dprh.demandes.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('dprh.demandes.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span style="color: white;">Demandes PEC</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir demandes')
                        <li>
                            <a href="{{ route('dprh.facturation.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('dprh.facturation.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span style="color: white;">Facturation</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir rôles')
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.roles.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span style="color: white;">Rôles</span>
                            </a>
                        </li>
                        @endcan

                        @can('voir permissions')
                        <li>
                            <a href="{{ route('admin.permissions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-slate-700 @if(request()->routeIs('admin.permissions.*')) bg-slate-700 @endif" style="color: white;">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <span style="color: white;">Permissions</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </nav>
                <div class="absolute bottom-0 left-0 w-64 p-4" style="border-top: 1px solid #334155;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm rounded-lg hover:bg-red-600" style="color: #fca5a5;">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fca5a5;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Header -->
                <header class="bg-white shadow-sm z-10">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Tableau de bord')</h2>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Admin
                            </span>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mx-6 mt-4">
                        <div class="rounded-md bg-green-50 p-4 border border-green-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-6 mt-4">
                        <div class="rounded-md bg-red-50 p-4 border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
