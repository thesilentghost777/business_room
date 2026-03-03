<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail d'accès - Business Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { bm: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d' }}}}}};</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>* { -webkit-font-smoothing: antialiased; } body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }</style>
</head>
<body class="bg-white min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-6 px-6">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-bm-600 flex items-center justify-center">
                        <i class="fas fa-landmark text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">Business Room</span>
                </div>
                <span class="text-xs text-gray-400 hidden sm:block">Portail de développement</span>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="max-w-4xl w-full">
                <div class="text-center mb-12">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-3">Choisir un accès</h1>
                    <p class="text-gray-500 text-sm">Sélectionnez votre profil pour vous connecter ou créer un compte</p>
                    <div class="mt-4 inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-200 rounded-full text-amber-700 text-xs">
                        <i class="fas fa-flask mr-1.5"></i> Mode développement — sera supprimé en production
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Admin -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-bm-300 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-bm-100 flex items-center justify-center mb-4 group-hover:bg-bm-200 transition-colors">
                            <i class="fas fa-shield-alt text-bm-600 text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Administrateur</h3>
                        <p class="text-xs text-gray-500 mb-5 leading-relaxed">Paramétrage, validation, statistiques globales</p>
                        <a href="{{ route('busmetro.login') }}" class="block text-center bg-bm-600 text-white text-sm font-medium py-2.5 rounded-xl hover:bg-bm-700 transition-colors">
                            Se connecter
                        </a>
                    </div>

                    <!-- Agent -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-bm-300 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-user-tie text-blue-600 text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Agent terrain</h3>
                        <p class="text-xs text-gray-500 mb-5 leading-relaxed">Enrôlement, collecte, suivi adhérents</p>
                        <a href="{{ route('busmetro.login') }}" class="block text-center bg-blue-600 text-white text-sm font-medium py-2.5 rounded-xl hover:bg-blue-700 transition-colors">
                            Se connecter
                        </a>
                    </div>

                    <!-- Direction -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-bm-300 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-chart-line text-purple-600 text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Direction</h3>
                        <p class="text-xs text-gray-500 mb-5 leading-relaxed">Analyse des sessions, validation des financements</p>
                        <a href="{{ route('busmetro.login') }}" class="block text-center bg-purple-600 text-white text-sm font-medium py-2.5 rounded-xl hover:bg-purple-700 transition-colors">
                            Se connecter
                        </a>
                    </div>

                    <!-- Adhérent -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-bm-300 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center mb-4 group-hover:bg-amber-200 transition-colors">
                            <i class="fas fa-user text-amber-600 text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Adhérent</h3>
                        <p class="text-xs text-gray-500 mb-5 leading-relaxed">Cotisations, financement, carnet de recettes</p>
                        <div class="space-y-2">
                            <a href="{{ route('busmetro.adherent.login') }}" class="block text-center bg-amber-500 text-white text-sm font-medium py-2.5 rounded-xl hover:bg-amber-600 transition-colors">
                                Se connecter
                            </a>
                            <a href="{{ route('busmetro.adherent.register') }}" class="block text-center border border-gray-200 text-gray-700 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                                S'inscrire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-6 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Business Room — Plateforme d'insertion et de financement</p>
        </footer>
    </div>
</body>
</html>
