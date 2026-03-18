<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div class="mt-6 flex flex-wrap gap-3">
            <x-filament::button type="submit" size="lg">
                💾 Sauvegarder les paramètres
            </x-filament::button>

            <x-filament::button
                type="button"
                color="success"
                size="lg"
                wire:click="sendTest"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="sendTest">🧪 Envoyer un message test</span>
                <span wire:loading wire:target="sendTest">⏳ Envoi en cours…</span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
