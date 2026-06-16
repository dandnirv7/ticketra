<nav x-data="{ open: false }" class="bg-neo-cream border-b-4 border-black">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center p-2 bg-neo-pink border-4 border-black rounded-xl shadow-neo-sm hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-neo-hover transition-all">
                    <x-application-logo class="block w-auto h-8 text-gray-900 fill-current" />
                </a>

                <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-4">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-900 bg-white border-4 border-black rounded-xl shadow-neo-sm hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-neo-hover transition-all focus:outline-none">
                            <span class="truncate max-w-[9rem]">
                                @auth
                                {{ auth()->user()->name }}
                                @endauth
                            </span>
                            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-900 bg-neo-yellow border-4 border-black rounded-xl shadow-neo-sm hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-neo-hover focus:outline-none transition-all">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-3 pb-4 px-4 space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <div class="pt-3 mt-3 border-t-4 border-dashed border-black">
                <div class="px-2 py-2 mb-2 bg-white border-4 border-black rounded-xl shadow-neo-sm">
                    <div class="text-base font-black text-gray-900">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-bold text-gray-600">{{ Auth::user()->email }}</div>
                </div>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>