<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rafh Entory</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inclusive+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Login-specific styles --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

{{-- ===================== ANIMATED BACKGROUND ===================== --}}
<canvas id="login-bg-canvas" class="fixed inset-0 w-full h-full" style="display:block;"></canvas>

{{-- ===================== LOGIN CARD ===================== --}}
<div class="fixed inset-0 z-10 flex items-center justify-center p-4">
    <div class="glass-card w-full max-w-[420px] rounded-[2rem] p-8 sm:p-12 shadow-2xl">

        {{-- Title --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight glass-title">Rafh Entory</h1>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->has('login'))
            <div class="alert-error">{{ $errors->first('login') }}</div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Username --}}
            <div class="group">
                <label for="username" class="glass-label">
                    Username
                </label>
                <input type="text" name="username" id="username"
                       value="{{ old('username') }}"
                       class="glass-input"
                       placeholder="Ketik username Anda..."
                       required autofocus>
            </div>

            {{-- Password --}}
            <div class="group">
                <label for="password" class="glass-label">
                    Password
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                           class="glass-input glass-input-password"
                           placeholder="Ketik password Anda..."
                           required>
                    <button type="button" onclick="togglePassword()" class="eye-toggle">
                        {{-- Eye open --}}
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{-- Eye closed --}}
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-4">
                <button type="submit" class="glass-btn">
                    Masuk
                    <i class="bi bi-arrow-right text-lg"></i>
                </button>
            </div>
        </form>

    </div>
</div>

{{-- Scripts —— password toggle from main.js, bg from login-bg.js --}}
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/login-bg.js') }}"></script>

</body>
</html>