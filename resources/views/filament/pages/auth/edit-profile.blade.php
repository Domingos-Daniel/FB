<div>
 
    <header class="fi-simple-header py-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                Meu Perfil
            </h1>
            <p class="fi-header-subheading mt-2 text-left text-sm text-gray-500 dark:text-gray-400">
                Edite seu perfil aqui
            </p>
        </div>
    </header>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</div>