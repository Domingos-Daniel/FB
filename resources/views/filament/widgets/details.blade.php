<x-filament-widgets::widget>
    <x-filament::section>

    <div name="space-y-4 mb-4">
        <h2 class="font-bold">Informações Pessoais</h2>
    </div>

    <div class="space-y-4 mt-4">

        <div>
            <p>Emiail: {{ auth()->user()->email }}</p>
        </div>

        <div>
            <p>Nome: {{ auth()->user()->name }}</p>
        </div>

    </div>
    </x-filament::section>
</x-filament-widgets::widget>
