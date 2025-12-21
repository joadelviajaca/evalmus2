<x-filament::page>
    <div class="space-y-6">
        {{-- Bienvenida --}}
        <div class="rounded-xl bg-white p-6 shadow">
            <h1 class="text-2xl font-bold text-gray-900">
                Bienvenido a Evalmus
            </h1>

            <p class="mt-2 text-gray-600">
                Sistema de gestión y evaluación de proyectos mediante rúbricas.
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Has iniciado sesión como <strong>{{ auth()->user()->name }}</strong>
                ({{ auth()->user()->getRoleNames()->first() }})
            </p>
        </div>

        {{-- Widgets --}}
        
            @livewire(\App\Filament\Widgets\ProjectsCount::class)
            @livewire(\App\Filament\Widgets\RubricsCount::class)
            @livewire(\App\Filament\Widgets\UsersCount::class)
        

        {{-- Acciones rápidas --}}
        <x-filament::section heading="Acciones rápidas">
    <div class="flex flex-wrap gap-3">
        <x-filament::button
            icon="heroicon-o-clipboard-document-check"
            tag="a"
            href="{{ route('filament.admin.resources.rubrics.index') }}"
        >
            Gestionar rúbricas
        </x-filament::button>

        <x-filament::button
            color="gray"
            icon="heroicon-o-folder"
            tag="a"
            href="{{ route('filament.admin.resources.projects.index') }}"
        >
            Ver proyectos
        </x-filament::button>
        <x-filament::button
                    color="gray"
                    icon="heroicon-o-users"
                    tag="a"
                    href="{{ route('filament.admin.resources.users.index') }}"
                >
                    Gestionar usuarios
                </x-filament::button>
    </div>
</x-filament::section>
    </div>
</x-filament::page>
