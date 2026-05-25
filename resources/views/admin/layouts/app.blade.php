<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Administration') | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Header -->
                <header class="bg-white shadow-sm z-10">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Tableau de bord')</h2>

                        <div class="flex items-center space-x-4">
                            <!-- Notifications PEC en attente -->
                            @can('voir demandes')
                            <a href="{{ route('dprh.demandes.index') }}" class="relative p-2 text-gray-500 hover:text-gray-700 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @php
                                    $pecEnAttente = \App\Models\DemandePEC::enAttente()->count();
                                @endphp
                                @if($pecEnAttente > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                                        {{ $pecEnAttente > 99 ? '99+' : $pecEnAttente }}
                                    </span>
                                @endif
                            </a>
                            @endcan

                            <!-- User Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-3 focus:outline-none">
                                    <!-- Avatar avec photo de profil -->
                                    @if(auth()->user()->profile_photo_path)
                                        <img src="{{ asset('avatars/' . auth()->user()->profile_photo_path) }}"
                                             alt="{{ auth()->user()->name }}"
                                             class="w-10 h-10 rounded-full object-cover shadow-md border-2 border-indigo-200">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg shadow-md">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <!-- User Info -->
                                    <div class="hidden md:block text-left">
                                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    <!-- Dropdown Arrow -->
                                    <svg class="w-5 h-5 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                                     style="display: none;">
                                    <!-- User Info Header avec grande photo -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            @if(auth()->user()->profile_photo_path)
                                                <img src="{{ asset('avatars/' . auth()->user()->profile_photo_path) }}"
                                                     alt="{{ auth()->user()->name }}"
                                                     class="w-12 h-12 rounded-full object-cover border-2 border-indigo-200">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }}
                                        </span>
                                    </div>

                                    <!-- PEC en attente Badge -->
                                    @can('voir demandes')
                                    <a href="{{ route('dprh.demandes.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>PEC en attente</span>
                                        @if($pecEnAttente > 0)
                                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                                {{ $pecEnAttente > 99 ? '99+' : $pecEnAttente }}
                                            </span>
                                        @else
                                            <span class="ml-auto text-xs text-gray-400">0</span>
                                        @endif
                                    </a>
                                    @endcan

                                    <!-- Profile Link -->
                                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Mon Profil
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
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
