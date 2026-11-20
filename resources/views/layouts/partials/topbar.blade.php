        {{-- Topbar / Header --}}
        <div class="flex items-center justify-between px-4 lg:px-8 py-4 z-10 w-full no-print mb-2">
            
            <!-- Mobile Toggle Button -->
            <button id="topbarToggleBtn" onclick="toggleSidebar()" class="lg:hidden glass-panel flex items-center justify-center p-2 rounded-xl text-indigo-300 hover:text-white transition-all duration-300">
                <i class="bi bi-list text-2xl"></i>
            </button>

            {{-- Spacer for desktop --}}
            <div class="hidden lg:block flex-1"></div>

            {{-- Profile Info (Oval Pill) --}}
            <div id="profilePill" onclick="toggleProfilePill()" class="glass-panel rounded-full px-5 py-2 flex items-center gap-3 shadow-lg cursor-pointer transition-all duration-300">
                @if($user)
                <div id="profilePillText" class="text-right hidden sm:block transition-all duration-300 w-auto opacity-100 whitespace-nowrap">
                    <p class="text-sm font-semibold text-glass-title">{{ $user['nama'] }}</p>
                    <p class="text-xs text-indigo-300">{{ ucfirst($user['role']) }}</p>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user['nama']) }}&background=312e81&color=e0e7ff&size=40" alt="Profile" class="w-10 h-10 rounded-full border border-indigo-400/50 shadow-inner">
                @else
                <button class="flex items-center gap-2 text-indigo-200 hover:text-white font-medium transition-colors focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name=Guest&background=312e81&color=e0e7ff" alt="Profile" class="w-10 h-10 rounded-full border border-indigo-400/50 shadow-inner">
                    <span class="text-sm hidden sm:block">Profile</span>
                    <i class="bi bi-chevron-down text-xs ml-1 opacity-70"></i>
                </button>
                @endif
            </div>
        </div>
