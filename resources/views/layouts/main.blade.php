<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rafh Entory</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Inclusive Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Inclusive+Sans&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="page-bg">
{{-- Animated Background Canvas --}}
<canvas id="login-bg-canvas" class="fixed inset-0 w-full h-full pointer-events-none" style="z-index: -10; display:block;"></canvas>
@php $user = session('user'); @endphp

{{-- Global Toast Notification --}}
@if(session('success') || session('error'))
@php
    $isSuccess = session()->has('success');
    $msg = session('success') ?? session('error');
    $bgColor = $isSuccess ? 'bg-emerald-600' : 'bg-red-600';
    $shadowColor = $isSuccess ? 'rgba(16,185,129,0.4)' : 'rgba(239,68,68,0.4)';
    $icon = $isSuccess ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
@endphp
<div id="globalToast" class="fixed top-6 right-6 z-[100] {{ $bgColor }} text-white px-5 py-3.5 rounded-xl shadow-[0_10px_20px_-10px_{{ $shadowColor }}] flex items-center gap-4 transform translate-x-[150%] opacity-0 transition-all duration-500 ease-out overflow-hidden min-w-[280px]">
    <i class="bi {{ $icon }} text-2xl"></i>
    <span class="font-semibold flex-1 text-sm">{{ $msg }}</span>
    <button onclick="closeToast()" class="text-white/70 hover:text-white transition-colors focus:outline-none ml-2">
        <i class="bi bi-x-lg"></i>
    </button>
    {{-- Progress bar --}}
    <div class="absolute bottom-0 left-0 h-1 bg-white/40 toast-progress" style="width: 100%;"></div>
</div>
@endif

{{-- Mobile Overlay --}}
<div id="mobileOverlay" class="mobile-overlay" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
@include('layouts.partials.sidebar')

{{-- MAIN CONTENT AREA --}}
<div id="mainContent" class="transition-[margin] duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] min-h-screen flex flex-col">
    
    @include('layouts.partials.topbar')

    {{-- Konten Halaman (Dinamis) --}}
    <div class="p-4 sm:p-8 relative z-0 flex-1">
        
        {{-- Breadcrumb Navigation --}}
        @php $segments = request()->segments(); @endphp
        <nav class="flex text-xs font-semibold text-indigo-300/60 mb-5 ml-2 tracking-wide uppercase no-print" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
              <a href="{{ url('/dashboard') }}" class="inline-flex items-center hover:text-indigo-100 transition-colors">
                <i class="bi bi-house-door-fill mr-1.5 text-[10px]"></i> Dashboard
              </a>
            </li>
            @if(count($segments) > 0 && $segments[0] !== 'dashboard')
                @foreach($segments as $key => $segment)
                <li>
                  <div class="flex items-center">
                    <i class="bi bi-chevron-right text-[10px] mx-1"></i>
                    <span class="ml-1 md:ml-1.5 {{ $loop->last ? 'text-indigo-100 font-bold' : 'hover:text-indigo-100 transition-colors' }}">
                        {{ ucwords(str_replace('_', ' ', $segment)) }}
                    </span>
                  </div>
                </li>
                @endforeach
            @endif
          </ol>
        </nav>

        <div class="w-full glass-panel p-6 sm:p-10 min-h-[500px]">
            <h1 class="text-2xl sm:text-3xl font-normal text-glass-title text-center mb-8 sm:mb-10 tracking-[0.2em] uppercase">
                @yield('header_title')
            </h1>
            
            @yield('content')
        </div>
    </div>
</div>

{{-- Modal Hapus Global --}}
<div id="deleteModal" class="fixed inset-0 bg-black/80 z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 backdrop-blur-sm">
    <div class="glass-panel rounded-2xl w-full max-w-sm shadow-2xl transform scale-90 opacity-0 -translate-y-10 transition-all duration-300 border border-indigo-500/30">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-900/30 flex items-center justify-center mx-auto mb-4 border border-red-500/30">
                <i class="bi bi-exclamation-triangle text-3xl text-red-400"></i>
            </div>
            <h3 class="text-xl font-bold text-glass-title mb-2">Hapus Data?</h3>
            <p class="text-sm text-glass-subtitle mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeModal('deleteModal')" class="px-5 py-2 text-indigo-200 bg-indigo-900/40 hover:bg-indigo-800/50 rounded-lg font-semibold transition-colors w-full border border-indigo-700/50">Tidak</button>
                <form id="globalDeleteForm" method="POST" action="" class="w-full m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-5 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-semibold shadow-sm transition-colors">Iya</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Global Base URL --}}
<script>window.BASE_URL = "{{ url('/') }}";</script>

{{-- External Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/crud.js') }}"></script>
<script src="{{ asset('js/login-bg.js') }}"></script>
{{-- Page-specific scripts --}}
@stack('scripts')

</body>
</html>