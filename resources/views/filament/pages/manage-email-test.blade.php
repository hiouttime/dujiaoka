<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}
        
        <div class="mt-6">
            {{ $this->getFormActions() }}
        </div>
    </form>
</x-filament-panels::page>