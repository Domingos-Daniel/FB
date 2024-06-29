<x-filament-widgets::widget>

<x-filament::card>
    <style>
        .custom-filament-button {
            display: inline-flex;
            padding: 0.75rem 1.5rem; /* px-6 py-3 */
            font-size: 0.875rem; /* text-sm */
            font-weight: 600; /* font-semibold */
            background-color: #32a723; /* bg-blue-500 */
            color: white; /* text-white */
            svg {
                color: white;
                width: 20px;
                height: 20px;
            }
            border-radius: 0.5rem; /* rounded-lg */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* shadow-md */
            transition: background-color 0.3s ease; /* transition duration-300 */
        }

        .custom-filament-button:hover {
            background-color: #34c221; /* hover:bg-blue-600 */
        }
    </style>
        <div class="flex justify-center above">
            <form action="{{ route('download-projects-excel') }}" method="GET">
                @csrf
                <button type="submit" class="custom-filament-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                      </svg>
                      
                    Exportar Planilha Geral
                </button>
            </form>
        </div>
    </x-filament::card>



</x-filament-widgets::widget>
