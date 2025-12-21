<x-filament-widgets::widget>
    <x-slot name="heading">
        Acceso al sistema
    </x-slot>

    <p>
        Has iniciado sesión como
        <strong>{{ auth()->user()->name }}</strong>
        ({{ auth()->user()->getRoleNames()->first() }}).
    </p>

    @if(auth()->user()->hasRole('super_admin'))
        <x-filament::badge color="success" class="mt-2">
            Acceso completo al sistema
        </x-filament::badge>
    @endif

    @if(auth()->user()->hasRole('coordinator'))
        <x-filament::badge color="primary" class="mt-2">
            Gestión de proyectos y rúbricas
        </x-filament::badge>
    @endif
</x-filament-widgets::widget>
