<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Menu Latéral -->
                <div class="lg:w-64 flex-shrink-0">
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <!-- En-tête du menu avec photo -->
                        <div class="p-6 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-center">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('avatars/' . auth()->user()->profile_photo_path) }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="w-20 h-20 rounded-full mx-auto mb-3 object-cover border-4 border-white/30">
                            @else
                                <div class="w-20 h-20 rounded-full mx-auto mb-3 bg-white/20 flex items-center justify-center text-white font-bold text-2xl border-4 border-white/30">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="font-semibold text-lg">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-white/80">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs">
                                {{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }}
                            </span>
                        </div>

                        <!-- Menu Items -->
                        <nav class="p-2">
                            <button onclick="showSection('info')" id="menu-info" class="menu-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-indigo-600 bg-indigo-50 transition">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informations personnelles
                            </button>

                            <button onclick="showSection('photo')" id="menu-photo" class="menu-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Photo de profil
                            </button>

                            <button onclick="showSection('password')" id="menu-password" class="menu-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Mot de passe
                            </button>

                            <button onclick="showSection('delete')" id="menu-delete" class="menu-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Supprimer le compte
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="flex-1">
                    <!-- Section Informations personnelles -->
                    <div id="section-info" class="section-content">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="p-4 sm:p-8 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                                <p class="mt-1 text-sm text-gray-600">Mettez à jour vos informations personnelles.</p>
                            </div>
                            <div class="p-4 sm:p-8">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    <!-- Section Photo de profil -->
                    <div id="section-photo" class="section-content hidden">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="p-4 sm:p-8 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Photo de profil</h3>
                                <p class="mt-1 text-sm text-gray-600">Modifiez votre photo de profil.</p>
                            </div>
                            <div class="p-4 sm:p-8">
                                <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="space-y-6">
                                    @csrf
                                    @method('PUT')

                                    <!-- Aperçu actuel -->
                                    <div class="flex items-center space-x-6">
                                        @if(auth()->user()->profile_photo_path)
                                            <img src="{{ asset('avatars/' . auth()->user()->profile_photo_path) }}"
                                                 alt="Photo actuelle"
                                                 class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                                        @else
                                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-3xl">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                        @endif>

                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Photo actuelle</p>
                                            <p class="text-sm text-gray-500">JPG, PNG ou GIF. Max 2 MB.</p>
                                        </div>
                                    </div>

                                    <!-- Upload -->
                                    <div>
                                        <label for="photo" class="block text-sm font-medium text-gray-700">Nouvelle photo</label>
                                        <div class="mt-2 flex items-center">
                                            <input type="file" name="photo" id="photo" accept="image/*"
                                                   class="block w-full text-sm text-gray-500
                                                          file:mr-4 file:py-2 file:px-4
                                                          file:rounded-full file:border-0
                                                          file:text-sm file:font-semibold
                                                          file:bg-indigo-50 file:text-indigo-700
                                                          hover:file:bg-indigo-100
                                                          cursor-pointer">
                                        </div>
                                        @error('photo')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Boutons -->
                                    <div class="flex items-center gap-3">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                                            Enregistrer la photo
                                        </button>
                                        @if(auth()->user()->profile_photo_path)
                                            <button type="submit" name="remove_photo" value="1"
                                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')">
                                                Supprimer la photo
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Section Mot de passe -->
                    <div id="section-password" class="section-content hidden">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="p-4 sm:p-8 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Mot de passe</h3>
                                <p class="mt-1 text-sm text-gray-600">Assurez-vous que votre compte utilise un mot de passe long et aléatoire.</p>
                            </div>
                            <div class="p-4 sm:p-8">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    <!-- Section Supprimer compte -->
                    <div id="section-delete" class="section-content hidden">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="p-4 sm:p-8 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-red-600">Supprimer le compte</h3>
                                <p class="mt-1 text-sm text-gray-600">Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.</p>
                            </div>
                            <div class="p-4 sm:p-8">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(section) {
            // Cacher toutes les sections
            document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));

            // Réinitialiser tous les menus
            document.querySelectorAll('.menu-item').forEach(el => {
                el.classList.remove('text-indigo-600', 'bg-indigo-50');
                el.classList.add('text-gray-700');
            });

            // Afficher la section sélectionnée
            document.getElementById('section-' + section).classList.remove('hidden');

            // Mettre en surbrillance le menu actif
            const activeMenu = document.getElementById('menu-' + section);
            activeMenu.classList.remove('text-gray-700');
            activeMenu.classList.add('text-indigo-600', 'bg-indigo-50');
        }
    </script>
</x-app-layout>
