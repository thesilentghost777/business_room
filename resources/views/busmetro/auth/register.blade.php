<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Business Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{bm:{50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { -webkit-font-smoothing: antialiased; } body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-bm-600 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-landmark text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Devenir membre</h1>
            <p class="text-sm text-gray-500 mt-1">Rejoignez le programme Business Room</p>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc pl-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('busmetro.adherent.register.submit') }}" method="POST" class="space-y-4" x-data="{ hasCode: true }">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="KAMGA">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="Jean">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="6XX XXX XXX">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Email <span class="text-gray-400">(optionnel)</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="jean@email.com">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Sexe</label>
                    <select name="sexe" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500">
                        <option value="">Choisir</option>
                        <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="Douala">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Quartier</label>
                    <input type="text" name="quartier" value="{{ old('quartier') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="Akwa">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-medium text-gray-700">Code de parrainage</label>
                    <button type="button" @click="hasCode = !hasCode" class="text-xs text-bm-600 hover:text-bm-700 font-medium">
                        <span x-text="hasCode ? 'Je n\'ai pas de code' : 'J\'ai un code'"></span>
                    </button>
                </div>
                <input x-show="hasCode" type="text" name="code_parrainage" value="{{ old('code_parrainage') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="Entrez votre code">
                <p x-show="!hasCode" class="text-xs text-gray-500 bg-bm-50 px-3 py-2.5 rounded-xl">
                    <i class="fas fa-info-circle mr-1 text-bm-500"></i>
                    Le code de parrainage par défaut sera utilisé automatiquement.
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <input type="password" name="password" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="Minimum 6 caractères">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-bm-600 text-white py-3 rounded-xl text-sm font-semibold hover:bg-bm-700 transition-colors mt-2">
                Créer mon compte
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('busmetro.adherent.login') }}" class="text-sm text-gray-500 hover:text-bm-600">
                Déjà membre ? <span class="font-medium text-bm-600">Se connecter</span>
            </a>
        </div>
    </div>
</body>
</html>
