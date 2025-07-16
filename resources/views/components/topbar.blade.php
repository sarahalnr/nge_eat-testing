<header style="height: 64px; flex-shrink: 0; background-color: #fff; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; padding: 0 20px;">
    <button @click="sidebarOpen = !sidebarOpen" style="font-size: 24px; cursor: pointer; background: none; border: none; color: #BE1E2D;">
        ☰
    </button>
    
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="text-align: right;">
            <div style="font-size: 14px; font-weight: 600;">{{ Auth::user()->name }}</div>
            <div style="font-size: 12px; color: #666;">{{ Auth::user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display: flex; align-items: center; gap: 8px;">
            @csrf
            <button type="submit" style="cursor: pointer; background: none; border: none; color: #333; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                {{-- Icon Logout --}}
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; fill: currentColor;" viewBox="0 0 24 24">
                    <path d="M16 17v-2h-4v-2h4v-2l5 3-5 3zm-2-14c1.103 0 2 .897 2 2v3h-2v-3h-10v14h10v-3h2v3c0 1.103-.897 2-2 2h-10c-1.103 0-2-.897-2-2v-14c0-1.103.897-2 2-2h10z"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</header>
