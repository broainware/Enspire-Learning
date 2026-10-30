
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Dashboard') · Enspire Learning</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body" x-data="{ sidebarOpen: false, profileOpen: false }">

    @php
        $isTeacher = auth()->user()->role === 'teacher';

        $navItems = $isTeacher ? [
            ['label' => 'Dashboard', 'route' => 'teacher.dashboard', 'icon' => '▦'],
            ['label' => 'Jadwal Mengajar', 'route' => 'teacher.sessions.index', 'icon' => '▣'],
            ['label' => 'Materi', 'route' => 'teacher.materials.index', 'icon' => '▤'],
            ['label' => 'Siswa', 'route' => 'teacher.students.index', 'icon' => '♧'],
            ['label' => 'Laporan', 'route' => 'teacher.reports.index', 'icon' => '▥'],
            ['label' => 'Profil', 'route' => 'teacher.profile.edit', 'icon' => '◉'],
        ] : [
            ['label' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => '▦'],
            ['label' => 'Materi', 'route' => 'student.materials.index', 'icon' => '▤'],
            ['label' => 'Jadwal', 'route' => 'student.schedules.index', 'icon' => '▣'],
            ['label' => 'Rencana Belajar', 'route' => 'student.plans.index', 'icon' => '◇'],
            ['label' => 'Progress', 'route' => 'student.progress.index', 'icon' => '◷'],
            ['label' => 'Profil', 'route' => 'student.profile.edit', 'icon' => '◉'],
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

            <nav class="side-nav">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="side-link {{ request()->routeIs($item['route']) || (str_starts_with($item['route'], 'teacher.') && request()->routeIs('teacher.'.explode('.', $item['route'])[1].'.*')) || (str_starts_with($item['route'], 'student.') && request()->routeIs('student.'.explode('.', $item['route'])[1].'.*')) ? 'active' : '' }}"
                    >
                        <span class="side-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-note">
                    <span class="note-spark">✦</span>
                    <strong>Terus bertumbuh!</strong>
                    <p>Setiap langkah kecil membawa kamu lebih dekat ke tujuan.</p>
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
                    ☰
                </button>

                <div class="top-search">
                    <span>⌕</span>
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

                    <div class="profile-menu" @click.outside="profileOpen = false">

                        <button
                            class="profile-trigger"
                            @click="profileOpen = !profileOpen"
                            type="button"
                        >
                            <span class="avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>

                            <span class="profile-trigger-text">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ $isTeacher ? 'Pengajar' : 'Siswa' }}</small>
                            </span>

                            <span class="chevron">⌄</span>
                        </button>

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
