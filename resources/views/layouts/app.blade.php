<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Cimol Bojot AA</title>
    <link rel="icon" type="image/png" href="/images/logo/logo-cimol-bojot-aa.png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{--
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (document.body) {
                    document.body.classList.add('dark', 'bg-gray-900');
                }
            } else {
                document.documentElement.classList.remove('dark');
                if (document.body) {
                    document.body.classList.remove('dark', 'bg-gray-900');
                }
            }
        })();
    </script>

</head>

<body x-data="{ 'loaded': true}" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader />
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out" :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

    @include('components.common.toast')
    <x-common.delete-confirm />
    <script>
        window.showToast = function (message, type = 'success') {
            const existing = document.getElementById('app-toast');
            if (existing) {
                existing.remove();
            }

            const bg = type === 'success' ? '#059669' : '#dc2626';
            const shadow = type === 'success' ? 'rgba(5, 150, 105, 0.4)' : 'rgba(220, 38, 38, 0.4)';
            const iconSvg = type === 'success'
                ? `<svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>`
                : `<svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>`;

            const toastDiv = document.createElement('div');
            toastDiv.id = 'app-toast';
            toastDiv.className = 'animate-toast-in';
            toastDiv.style.cssText = `position: fixed; top: 30px; right: 30px; z-index: 99999999; display: flex; align-items: center; gap: 16px; background: ${bg}; color: white; padding: 16px 28px; border-radius: 24px; box-shadow: 0 20px 25px -5px ${shadow}, 0 10px 10px -5px rgba(0, 0, 0, 0.1); font-family: 'Outfit', sans-serif;`;

            toastDiv.innerHTML = `
                <div style="flex-shrink: 0; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
                    ${iconSvg}
                </div>
                <div style="padding-right: 8px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 800; letter-spacing: 0.025em; line-height: 1.2;">
                        ${message}
                    </p>
                </div>
                <button onclick="closeToast()" style="margin-left: 8px; background: none; border: none; color: white; cursor: pointer; padding: 6px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='none'">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            `;

            if (!document.getElementById('toast-styles')) {
                const style = document.createElement('style');
                style.id = 'toast-styles';
                style.textContent = `
                    @keyframes toast-in {
                        0% { transform: translateY(-50px) scale(0.9); opacity: 0; }
                        100% { transform: translateY(0) scale(1); opacity: 1; }
                    }
                    @keyframes toast-out {
                        0% { transform: translateY(0) scale(1); opacity: 1; }
                        100% { transform: translateY(-50px) scale(0.9); opacity: 0; }
                    }
                    .animate-toast-in { animation: toast-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
                    .animate-toast-out { animation: toast-out 0.4s cubic-bezier(0.6, -0.28, 0.735, 0.045) forwards; }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(toastDiv);

            window.closeToast = function () {
                const t = document.getElementById('app-toast');
                if (t) {
                    t.className = 'animate-toast-out';
                    setTimeout(() => t.remove(), 400);
                }
            };

            setTimeout(window.closeToast, 5000);
        }
    </script>
</body>

@stack('scripts')

</html>