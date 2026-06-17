<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login admin SigerZeroNet">
    <title>Login Admin - SigerZeroNet</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[--color-surface] text-[--color-dark-text] font-sans">
    <main class="min-h-screen grid place-items-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-[#006954] flex items-center justify-center shadow-lg shadow-emerald-900/10">
                    <svg class="w-8 h-8 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">SigerZeroNet Admin</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk untuk mengelola data carbon.</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <input id="password" name="password" type="password" required
                               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Ingat saya
                    </label>

                    <button type="submit"
                            class="w-full rounded-lg bg-[#006954] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#004236] focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
