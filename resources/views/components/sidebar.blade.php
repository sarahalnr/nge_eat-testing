<style>
    .sidebar {
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 45;
        height: 100vh;
        background: linear-gradient(to bottom, #FFD5AB, #F58220);
        display: flex;
        flex-direction: column;
        padding: 20px 10px;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
    }

    .sidebar-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
    }

    .sidebar-logo img {
        height: 90px;
        width: auto;
        padding: 5px;
        object-fit: contain;
    }

    nav.sidebar-nav .submenu > a.active {
        background-color: #ffffff;
        color: #FF6B00;
        font-weight: 600;
        border: 2px solid #ffffff;
    }

    .sidebar-separator {
        height: 2px;
        background-color: #ffffff;
        margin: 20px 0;
    }

    nav.sidebar-nav {
        display: flex;
        flex-direction: column;
    }

    nav.sidebar-nav .submenu > a.menu-item,
    nav.sidebar-nav > a {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        color: #ffffff;
        transition: background-color 0.2s ease, color 0.2s ease;
        border: 2px solid transparent;
        user-select: none;
        cursor: pointer;
        margin-bottom: 6px;
    }

    nav.sidebar-nav .submenu > a.menu-item.active,
    nav.sidebar-nav .submenu > a.menu-item:hover,
    nav.sidebar-nav > a.active,
    nav.sidebar-nav > a:hover {
        background-color: #ffffff;
        color: #FF6B00;
        border: 2px solid #ffffff;
    }

    nav.sidebar-nav a img,
    nav.sidebar-nav a svg {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        stroke: #ffffff;
        fill: none;
    }

    nav.sidebar-nav a.active img,
    nav.sidebar-nav a.active svg,
    nav.sidebar-nav a:hover img,
    nav.sidebar-nav a:hover svg {
        stroke: #FF6B00;
        fill: #FF6B00;
    }

    nav.sidebar-nav .submenu {
        display: none;
        flex-direction: column;
        margin-left: 20px;
    }

    nav.sidebar-nav .submenu.open {
        display: flex;
    }

    nav.sidebar-nav .submenu > a {
        padding: 10px 20px;
        font-weight: 500;
        border-radius: 6px;
        margin-bottom: 4px;
        background-color: rgba(255, 255, 255, 0.1);
    }

    nav.sidebar-nav .submenu > a:hover {
        background-color: #ffffff;
        color: #FF6B00;
    }
</style>

<div
    x-data="{ transaksiOpen: false, itemTerjualOpen: false }"
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform -translate-x-full"
    class="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"/>
        </a>
    </div>

    <div class="sidebar-separator"></div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <a
            href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7m-9 2v10m4-10l2 2m-2-2v10"/>
            </svg>
            Dashboard
        </a>

        <a
            href="{{ route('kategori.index') }}"
            class="{{ request()->routeIs('kategori.index') ? 'active' : '' }}">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 3.75h4.5v4.5h-4.5v-4.5zm0 12h4.5v4.5h-4.5v-4.5zm12-12h4.5v4.5h-4.5v-4.5zm0 12h4.5v4.5h-4.5v-4.5z"/>
            </svg>
            Kategori
        </a>
        <a
            href="{{ route('menus.index') }}"
            class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 2.25h6M9 2.25a1.5 1.5 0 00-1.5 1.5v.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25h12a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-1.5v-.75A1.5 1.5 0 0015 2.25M9 7.5h6M9 11.25h6M9 15h4.5"/>
            </svg>
            Menu
        </a>

        {{-- Transaksi dropdown --}}
        <a
            href="javascript:void(0)"
            @click="transaksiOpen = !transaksiOpen"
            x-init="
    transaksiOpen =
      '{{ request()->routeIs('gofood.*') || request()->routeIs('grabfood.*') || request()->routeIs('shopeefood.*') ? 'true' : 'false' }}' === 'true'
  "
            :class="transaksiOpen || '{{ request()->routeIs('gofood.*') || request()->routeIs('grabfood.*') || request()->routeIs('shopeefood.*') ? 'true' : 'false' }}' === 'true' ? 'active' : ''"
            class="menu-item">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 3v18h18M9 17V9m4 8v-6m4 6V5"/>
            </svg>
            Transaksi
            <svg
                style="margin-left:auto;"
                xmlns="http://www.w3.org/2000/svg"
                class="transform transition-transform duration-200 w-5 h-5"
                :class="transaksiOpen ? 'rotate-180' : ''"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </a>
        <div class="submenu" :class="transaksiOpen ? 'open' : ''">
            <a
                href="{{ route('gofood.index') }}"
                class="{{ request()->routeIs('gofood.*') ? 'active' : '' }}">
                GoFood
            </a>
            <a
                href="{{ route('grabfood.index') }}"
                class="{{ request()->routeIs('grabfood.*') ? 'active' : '' }}">
                GrabFood
            </a>
            <a
                href="{{ route('shopeefood.index') }}"
                class="{{ request()->routeIs('shopeefood.*') ? 'active' : '' }}">
                ShopeeFood
            </a>
        </div>

        {{-- Item Terjual dropdown --}}
<a
    href="javascript:void(0)"
    @click="itemTerjualOpen = !itemTerjualOpen"
    x-init="
      itemTerjualOpen =
        '{{ request()->routeIs('items-terjual.gofood') || request()->routeIs('items-terjual.grabfood') || request()->routeIs('items-terjual.shopeefood') ? 'true' : 'false' }}' === 'true'
    "
    :class="itemTerjualOpen || '{{ request()->routeIs('items-terjual.gofood') || request()->routeIs('items-terjual.grabfood') || request()->routeIs('items-terjual.shopeefood') ? 'true' : 'false' }}' === 'true' ? 'active' : ''"
    class="menu-item">
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2l4 -4M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z"/>
    </svg>
    Item Terjual
    <svg
        style="margin-left:auto;"
        xmlns="http://www.w3.org/2000/svg"
        class="transform transition-transform duration-200 w-5 h-5"
        :class="itemTerjualOpen ? 'rotate-180' : ''"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
    </svg>
</a>

<div class="submenu" :class="itemTerjualOpen ? 'open' : ''">
    <a
        href="{{ route('items-terjual.gofood') }}"
        class="{{ request()->routeIs('items-terjual.gofood') ? 'active' : '' }}">
        GoFood
    </a>
    <a
        href="{{ route('items-terjual.grabfood') }}"
        class="{{ request()->routeIs('items-terjual.grabfood') ? 'active' : '' }}">
        GrabFood
    </a>
    <a
        href="{{ route('items-terjual.shopeefood') }}"
        class="{{ request()->routeIs('items-terjual.shopeefood') ? 'active' : '' }}">
        ShopeeFood
    </a>
</div>


        {{-- Laporan --}}
        <a
            href="{{ route('laporan.index') }}"
            class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 17v-4h6v4m-7 4h8a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Laporan Transaksi
        </a>

        {{-- Profil --}}
        <a
            href="{{ route('profile.edit') }}"
            class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Profil
        </a>
    </nav>
</div>