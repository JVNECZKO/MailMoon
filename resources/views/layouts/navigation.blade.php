<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-bold text-blue-700">MailMoon</span>
                    <span class="hidden sm:block text-xs uppercase tracking-wide text-slate-500">Email desk</span>
                </a>
                <div class="hidden md:flex items-center space-x-3 text-sm font-medium">
                    @php
                        $linkClasses = fn ($active) => ($active ? 'text-blue-700 border-blue-600' : 'text-slate-600 border-transparent hover:text-slate-900') . ' border-b-2 pb-1 transition';
                    @endphp
                    <a href="{{ route('dashboard') }}" class="{{ $linkClasses(request()->routeIs('dashboard')) }}">Dashboard</a>
                    <a href="{{ route('campaigns.index') }}" class="{{ $linkClasses(request()->routeIs('campaigns.*')) }}">Kampanie</a>
                    <a href="{{ route('templates.index') }}" class="{{ $linkClasses(request()->routeIs('templates.*')) }}">Szablony</a>
                    <a href="{{ route('contact-lists.index') }}" class="{{ $linkClasses(request()->routeIs('contact-lists.*') || request()->routeIs('contact-lists.contacts.*')) }}">Listy kontaktów</a>
                    <a href="{{ route('sending-identities.index') }}" class="{{ $linkClasses(request()->routeIs('sending-identities.*')) }}">Tożsamości</a>
                    <a href="{{ route('settings.cron') }}" class="{{ $linkClasses(request()->routeIs('warming.*')) }}">Warming</a>
                    <a href="{{ route('settings.cron') }}" class="{{ $linkClasses(request()->routeIs('settings.*')) }}">Ustawienia</a>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('profile.edit') }}" class="hidden sm:block text-sm text-slate-600 hover:text-slate-900">
                    {{ Auth::user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800">
                        Wyloguj
                    </button>
                </form>
                <button @click="open = !open" class="md:hidden inline-flex items-center p-2 rounded-md text-slate-600 hover:text-slate-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="md:hidden border-t border-slate-200" x-show="open" x-transition>
        <div class="px-4 pt-2 pb-3 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block px-2 py-2 {{ request()->routeIs('dashboard') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Dashboard</a>
            <a href="{{ route('campaigns.index') }}" class="block px-2 py-2 {{ request()->routeIs('campaigns.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Kampanie</a>
            <a href="{{ route('templates.index') }}" class="block px-2 py-2 {{ request()->routeIs('templates.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Szablony</a>
            <a href="{{ route('contact-lists.index') }}" class="block px-2 py-2 {{ request()->routeIs('contact-lists.*') || request()->routeIs('contact-lists.contacts.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Listy kontaktów</a>
            <a href="{{ route('sending-identities.index') }}" class="block px-2 py-2 {{ request()->routeIs('sending-identities.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Tożsamości</a>
            <a href="{{ route('settings.cron') }}" class="block px-2 py-2 {{ request()->routeIs('warming.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Warming</a>
            <a href="{{ route('settings.cron') }}" class="block px-2 py-2 {{ request()->routeIs('settings.*') ? 'text-blue-700 font-semibold' : 'text-slate-700' }}">Ustawienia</a>
            <a href="{{ route('profile.edit') }}" class="block px-2 py-2 text-slate-700">Profil</a>
        </div>
    </div>
</nav>
