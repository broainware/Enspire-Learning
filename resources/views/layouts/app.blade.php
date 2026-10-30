
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/enspire-icon.png') }}">

    <title>@yield('title', 'Dashboard') · Enspire Learning</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body" x-data="{ sidebarOpen: false, profileOpen: false }">

    @php
        $isTeacher = auth()->user()->role === 'teacher';

        // MENU PENGAJAR
        $navItems = $isTeacher ? [
            ['label' => 'Dashboard', 'route' => 'teacher.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Jadwal Mengajar', 'route' => 'teacher.sessions.index', 'icon' => 'calendar'],
            ['label' => 'Materi', 'route' => 'teacher.materials.index', 'icon' => 'book'],
            ['label' => 'Siswa', 'route' => 'teacher.students.index', 'icon' => 'users'],
            ['label' => 'Laporan', 'route' => 'teacher.reports.index', 'icon' => 'chart'],
            ['label' => 'Profil', 'route' => 'teacher.profile.edit', 'icon' => 'user'],
        ] : [
            // MENU SISWA
            ['label' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Materi', 'route' => 'student.materials.index', 'icon' => 'book'],
            ['label' => 'Jadwal', 'route' => 'student.schedules.index', 'icon' => 'calendar'],
            ['label' => 'Rencana Belajar', 'route' => 'student.plans.index', 'icon' => 'clipboard'],
            ['label' => 'Progress', 'route' => 'student.progress.index', 'icon' => 'trending'],
            ['label' => 'Profil', 'route' => 'student.profile.edit', 'icon' => 'user'],
        ];
    @endphp

    <div class="app-shell">

        {{-- SIDEBAR --}}
        <aside class="sidebar" :class="sidebarOpen ? 'sidebar-open' : ''">

            {{-- BRAND LOGO --}}
            <a href="{{ route('dashboard') }}" class="brand-lockup">
                <img
                    src="{{ asset('images/enspire-icon.png') }}"
                    alt="Logo Enspire Learning"
                    class="brand-image"
                    style="width: 58px; height: 58px; object-fit: contain; flex-shrink: 0;"
                >

                <span>
                    <strong>Enspire Learning</strong>
                    <small>Belajar · Tumbuh · Raih Mimpi</small>
                </span>
            </a>

            <div class="side-caption">MENU UTAMA</div>

            {{-- NAVIGATION --}}
            <nav class="side-nav">

                @foreach ($navItems as $item)

                    <a
                        href="{{ route($item['route']) }}"
                        class="side-link {{ request()->routeIs($item['route']) || (str_starts_with($item['route'], 'teacher.') && request()->routeIs('teacher.'.explode('.', $item['route'])[1].'.*')) || (str_starts_with($item['route'], 'student.') && request()->routeIs('student.'.explode('.', $item['route'])[1].'.*')) ? 'active' : '' }}"
                    >

                        <span class="side-icon">

                            @switch($item['icon'])

                                {{-- DASHBOARD --}}
                                @case('dashboard')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                        <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                                        <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                    </svg>
                                    @break

                                {{-- CALENDAR --}}
                                @case('calendar')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                                        <path d="M16 3v4M8 3v4M3 10h18"/>
                                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/>
                                    </svg>
                                    @break

                                {{-- BOOK / MATERI --}}
                                @case('book')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 7v14"/>
                                        <path d="M3 18.5V5.8a1.8 1.8 0 0 1 2.2-1.7l5.2 1.1A2 2 0 0 1 12 7a2 2 0 0 1 1.6-1.8l5.2-1.1A1.8 1.8 0 0 1 21 5.8v12.7a1.5 1.5 0 0 1-1.8 1.5l-5.6-1.1a2 2 0 0 0-1.6.4 2 2 0 0 0-1.6-.4l-5.6 1.1A1.5 1.5 0 0 1 3 18.5Z"/>
                                    </svg>
                                    @break

                                {{-- STUDENTS / USERS --}}
                                @case('users')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="10" cy="7" r="4"/>
                                        <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    @break

                                {{-- REPORT / CHART --}}
                                @case('chart')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 3v18h18"/>
                                        <rect x="7" y="12" width="3" height="6" rx="0.5"/>
                                        <rect x="13" y="8" width="3" height="10" rx="0.5"/>
                                        <rect x="19" y="4" width="2" height="14" rx="0.5"/>
                                    </svg>
                                    @break

                                {{-- PROFILE --}}
                                @case('user')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="8" r="4"/>
                                        <path d="M5 21a7 7 0 0 1 14 0"/>
                                    </svg>
                                    @break

                                {{-- LEARNING PLAN --}}
                                @case('clipboard')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                                        <path d="M9 4.5h6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1Z"/>
                                        <path d="M9 10h6M9 14h6M9 18h3"/>
                                    </svg>
                                    @break

                                {{-- PROGRESS --}}
                                @case('trending')
                                    <svg class="side-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m3 17 6-6 4 4 8-8"/>
                                        <path d="M15 7h6v6"/>
                                    </svg>
                                    @break

                            @endswitch

                        </span>

                        <span>{{ $item['label'] }}</span>

                    </a>

                @endforeach

            </nav>

            {{-- SIDEBAR BOTTOM --}}
            <div class="sidebar-bottom">

                <div class="sidebar-note">
                    <span class="note-spark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 3 1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2L12 3Z"/>
                            <path d="m19 14 1 2.5 2.5 1-2.5 1L19 21l-1-2.5-2.5-1 2.5-1L19 14Z"/>
                        </svg>
                    </span>

                    <strong>Terus bertumbuh!</strong>

                    <p>
                        Setiap langkah kecil membawa kamu lebih dekat ke tujuan.
                    </p>
                </div>

                <div class="sidebar-version">
                    ENSPIRE LEARNING · v1.0
                </div>

            </div>

        </aside>


        {{-- MAIN AREA --}}
        <div class="main-area">

            {{-- TOPBAR --}}
            <header class="topbar">

                <button
                    class="mobile-menu"
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="Buka menu"
                >
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- SEARCH --}}
                <div class="top-search">

                    <span class="top-search-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-4-4"/>
                        </svg>
                    </span>

                    <input
                        type="search"
                        placeholder="Cari materi, mata pelajaran, atau topik..."
                        aria-label="Cari"
                    >

                </div>


                <div class="topbar-right">

                    <span class="topbar-date">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>

                    {{-- PROFILE MENU --}}
                    <div class="profile-menu" @click.outside="profileOpen = false">

                        <button
                            class="profile-trigger"
                            @click="profileOpen = !profileOpen"
                            type="button"
                        >

                            <span class="avatar">

                                @if (auth()->user()->photo_path)
                                    <img
                                        src="{{ asset('storage/' . auth()->user()->photo_path) }}"
                                        alt="Foto Profil"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                    >
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif

                            </span>

                            <span class="profile-trigger-text">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ $isTeacher ? 'Pengajar' : 'Siswa' }}</small>
                            </span>

                            <span class="chevron">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m7 10 5 5 5-5"/>
                                </svg>
                            </span>

                        </button>

                        {{-- DROPDOWN --}}
                        <div
                            class="profile-dropdown"
                            x-show="profileOpen"
                            x-transition
                            x-cloak
                        >

                            <a href="{{ route($isTeacher ? 'teacher.profile.edit' : 'student.profile.edit') }}">
                                Pengaturan profil
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Keluar dari akun</button>
                            </form>

                        </div>

                    </div>

                </div>

            </header>


            {{-- PAGE CONTENT --}}
            <main class="page-content">

                @if (session('success'))
                    <div class="alert alert-success">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Periksa kembali data yang dimasukkan.</strong>

                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </main>


            {{-- FOOTER --}}
            <footer class="app-footer">
                © {{ date('Y') }} Enspire Learning. Belajar · Tumbuh · Raih Mimpi.
            </footer>

        </div>

    </div>


    {{-- MOBILE OVERLAY --}}
    <div
        class="mobile-overlay"
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        x-cloak
    ></div>

    @stack('scripts')

</body>
</html>
