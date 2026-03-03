<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Staff - Business Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{bm:{50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d'}}}}};</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>* { -webkit-font-smoothing: antialiased; } body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-bm-600 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-landmark text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Connexion</h1>
            <p class="text-sm text-gray-500 mt-1">Espace administratif</p>
        </div>

        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-exclamation-circle mr-1.5"></i>{{ session('error') }}
        </div>
        @endif

        <form action="{{ route('busmetro.login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500 transition-colors"
                    placeholder="admin@busmetro.com">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bm-500/20 focus:border-bm-500 transition-colors"
                    placeholder="••••••••">
            </div>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-bm-600 focus:ring-bm-500">
                <span class="text-xs text-gray-600">Se souvenir de moi</span>
            </label>
            <button type="submit" class="w-full bg-bm-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-bm-700 transition-colors">
                Se connecter
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('busmetro.adherent.login') }}" class="text-xs text-gray-400 hover:text-bm-600 transition-colors">
                Espace adhérent <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</body>
</html>
