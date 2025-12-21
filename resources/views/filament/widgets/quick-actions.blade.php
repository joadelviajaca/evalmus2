<x-filament-widgets::widget>
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
            icon="heroicon-o-folder"
            color="info"
            tag="a"
            href="{{ route('filament.admin.resources.projects.index') }}"
        >
            Ver proyectos
        </x-filament::button>

        @if(auth()->user()->hasRole('super_admin'))
            <x-filament::button
                icon="heroicon-o-users"
                color="warning"
                tag="a"
                href="{{ route('filament.admin.resources.users.index') }}"
            >
                Gestionar usuarios
            </x-filament::button>
        @endif
    </div>
    </x-filament::section>
</x-filament-widgets::widget>
