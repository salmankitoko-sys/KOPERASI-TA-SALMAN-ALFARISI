@props(['title' => 'Administrasi Akun'])

<x-app-layout>
    <div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
        @include('dashboard.admin.partials.sidebar')

        <div class="min-h-screen lg:ml-64">
            @include('dashboard.admin.partials.navbar', ['title' => $title])

            <main class="p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>

        <div
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-gray-950/45 lg:hidden"
        ></div>
    </div>
</x-app-layout>
