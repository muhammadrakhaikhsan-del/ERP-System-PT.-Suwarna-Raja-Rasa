@php 
    $user = session('user'); 
    $role = $user['role'] ?? '';
@endphp

<div id="sidebar" class="sidebar-container">
    
    {{-- Background Layer --}}
    <div class="sidebar-bg-layer"></div>

    {{-- Content Container --}}
    <div class="sidebar-inner">
        
        {{-- Profile Section: only avatar + toggle button --}}
        <div class="sidebar-profile-row">
            <div class="sidebar-avatar-wrap">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user['nama'] ?? 'Guest') }}&background=ffffff&color=000000&size=80&bold=true" 
                     alt="Avatar" class="sidebar-avatar">
                <span class="sidebar-avatar-label">{{ strtoupper(substr($user['nama'] ?? 'G', 0, 2)) }}</span>
            </div>

            {{-- Toggle Button --}}
            <button id="sidebarToggleBtn" onclick="toggleSidebar()" class="sidebar-toggle-btn">
                <i class="bi bi-chevron-left" id="toggleIcon"></i>
            </button>
        </div>

        {{-- Scrollable Menu Area --}}
        <div class="sidebar-scroll-area">
            <nav class="sidebar-nav">

                {{-- MENUS Section --}}
                <div class="sidebar-section-title">Menus</div>

                {{-- Dashboard --}}
                <a href="{{ url('/dashboard') }}" 
                   class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}" 
                   data-tooltip="Dashboard">
                    <span class="sidebar-item-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span class="sidebar-item-text">Dashboard</span>
                </a>

                {{-- Master Data Dropdown --}}
                @php
                    $showMasterData = ($role === 'manager') || ($role === 'purchasing');
                    $showOutletTenant = ($role === 'manager');
                    $showBarang = ($role === 'purchasing');
                @endphp
                @if($showMasterData)
                <div class="sidebar-dropdown-wrap">
                    <button onclick="toggleSidebarDropdown('masterDataMenu', this)" 
                            class="sidebar-item sidebar-dropdown-btn" data-tooltip="Master Data">
                        <span class="sidebar-item-icon"><i class="bi bi-database-fill"></i></span>
                        <span class="sidebar-item-text">Master Data</span>
                        <i class="bi bi-chevron-down sidebar-chevron sidebar-item-text"></i>
                    </button>
                    <div id="masterDataMenu" class="sidebar-submenu">
                        @if($showOutletTenant)
                            <a href="{{ url('/outlet') }}" class="sidebar-sub-item {{ request()->is('outlet') ? 'active' : '' }}">Outlet</a>
                            <a href="{{ url('/tenant') }}" class="sidebar-sub-item {{ request()->is('tenant') ? 'active' : '' }}">Tenant</a>
                        @endif
                        @if($showBarang)
                            <a href="{{ url('/barang') }}" class="sidebar-sub-item {{ request()->is('barang') ? 'active' : '' }}">Barang</a>
                        @endif
                    </div>
                    <div class="sidebar-flyout">
                        <div class="sidebar-flyout-title">Master Data</div>
                        @if($showOutletTenant)
                            <a href="{{ url('/outlet') }}" class="sidebar-flyout-item {{ request()->is('outlet') ? 'active' : '' }}">Outlet</a>
                            <a href="{{ url('/tenant') }}" class="sidebar-flyout-item {{ request()->is('tenant') ? 'active' : '' }}">Tenant</a>
                        @endif
                        @if($showBarang)
                            <a href="{{ url('/barang') }}" class="sidebar-flyout-item {{ request()->is('barang') ? 'active' : '' }}">Barang</a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Transaksi Dropdown --}}
                @php
                    $showBarangMasukKeluar = ($role === 'crew');
                    $showKeuanganHarian   = ($role === 'crew');
                    $showStockOpname      = ($role === 'purchasing');
                    $showTransaksi        = $showBarangMasukKeluar || $showKeuanganHarian || $showStockOpname;
                @endphp
                @if($showTransaksi)
                <div class="sidebar-dropdown-wrap">
                    <button onclick="toggleSidebarDropdown('transaksiMenu', this)" 
                            class="sidebar-item sidebar-dropdown-btn" data-tooltip="Transaksi">
                        <span class="sidebar-item-icon"><i class="bi bi-cart-fill"></i></span>
                        <span class="sidebar-item-text">Transaksi</span>
                        <i class="bi bi-chevron-down sidebar-chevron sidebar-item-text"></i>
                    </button>
                    <div id="transaksiMenu" class="sidebar-submenu">
                        @if($showBarangMasukKeluar)
                            <a href="{{ url('/barang_masuk') }}" class="sidebar-sub-item {{ request()->is('barang_masuk') ? 'active' : '' }}">Barang Masuk</a>
                            <a href="{{ url('/barang_keluar') }}" class="sidebar-sub-item {{ request()->is('barang_keluar') ? 'active' : '' }}">Barang Keluar</a>
                        @endif
                        @if($showKeuanganHarian)
                            <a href="{{ url('/keuangan_harian') }}" class="sidebar-sub-item {{ request()->is('keuangan_harian') ? 'active' : '' }}">Keuangan Harian</a>
                        @endif
                        @if($showStockOpname)
                            <a href="{{ url('/stock_opname') }}" class="sidebar-sub-item {{ request()->is('stock_opname') ? 'active' : '' }}">Stock Opname</a>
                        @endif
                    </div>
                    <div class="sidebar-flyout">
                        <div class="sidebar-flyout-title">Transaksi</div>
                        @if($showBarangMasukKeluar)
                            <a href="{{ url('/barang_masuk') }}" class="sidebar-flyout-item {{ request()->is('barang_masuk') ? 'active' : '' }}">Barang Masuk</a>
                            <a href="{{ url('/barang_keluar') }}" class="sidebar-flyout-item {{ request()->is('barang_keluar') ? 'active' : '' }}">Barang Keluar</a>
                        @endif
                        @if($showKeuanganHarian)
                            <a href="{{ url('/keuangan_harian') }}" class="sidebar-flyout-item {{ request()->is('keuangan_harian') ? 'active' : '' }}">Keuangan Harian</a>
                        @endif
                        @if($showStockOpname)
                            <a href="{{ url('/stock_opname') }}" class="sidebar-flyout-item {{ request()->is('stock_opname') ? 'active' : '' }}">Stock Opname</a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- SERVICE Section --}}
                @php
                    $showLaporanBarang       = in_array($role, ['purchasing', 'manager']);
                    $showLaporanKeuangan     = in_array($role, ['accounting', 'manager']);
                    $showLaporanRekap        = ($role === 'manager');
                    $showService             = $showLaporanBarang || $showLaporanKeuangan || $showLaporanRekap;
                @endphp
                @if($showService)
                <div class="sidebar-section-title">Service</div>

                {{-- Laporan Dropdown --}}
                <div class="sidebar-dropdown-wrap">
                    <button onclick="toggleSidebarDropdown('laporanMenu', this)" 
                            class="sidebar-item sidebar-dropdown-btn" data-tooltip="Laporan">
                        <span class="sidebar-item-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></span>
                        <span class="sidebar-item-text">Laporan</span>
                        <i class="bi bi-chevron-down sidebar-chevron sidebar-item-text"></i>
                    </button>
                    <div id="laporanMenu" class="sidebar-submenu">
                        @if($showLaporanBarang)
                            <a href="{{ url('/laporan_barang') }}" class="sidebar-sub-item {{ request()->is('laporan_barang') ? 'active' : '' }}">Laporan Barang</a>
                        @endif
                        @if($showLaporanKeuangan)
                            <a href="{{ url('/laporan_keuangan') }}" class="sidebar-sub-item {{ request()->is('laporan_keuangan') ? 'active' : '' }}">Laporan Keuangan</a>
                        @endif
                        @if($showLaporanRekap)
                            <a href="{{ url('/laporan_rekapitulasi') }}" class="sidebar-sub-item {{ request()->is('laporan_rekapitulasi') ? 'active' : '' }}">L. Rekapitulasi</a>
                        @endif
                    </div>
                    <div class="sidebar-flyout">
                        <div class="sidebar-flyout-title">Laporan</div>
                        @if($showLaporanBarang)
                            <a href="{{ url('/laporan_barang') }}" class="sidebar-flyout-item {{ request()->is('laporan_barang') ? 'active' : '' }}">Laporan Barang</a>
                        @endif
                        @if($showLaporanKeuangan)
                            <a href="{{ url('/laporan_keuangan') }}" class="sidebar-flyout-item {{ request()->is('laporan_keuangan') ? 'active' : '' }}">Laporan Keuangan</a>
                        @endif
                        @if($showLaporanRekap)
                            <a href="{{ url('/laporan_rekapitulasi') }}" class="sidebar-flyout-item {{ request()->is('laporan_rekapitulasi') ? 'active' : '' }}">L. Rekapitulasi</a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Kelola Pengguna --}}
                @if($role === 'manager')
                <a href="{{ url('/pengguna') }}" 
                   class="sidebar-item {{ request()->is('pengguna') ? 'active' : '' }}" 
                   data-tooltip="Kelola Pengguna">
                    <span class="sidebar-item-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="sidebar-item-text">Kelola Pengguna</span>
                </a>
                @endif

            </nav>
        </div>

        {{-- Logout --}}
        <div class="sidebar-bottom">
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="sidebar-item sidebar-logout" data-tooltip="Logout">
                    <span class="sidebar-item-icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span class="sidebar-item-text">Logout</span>
                </button>
            </form>
        </div>

    </div>
</div>
