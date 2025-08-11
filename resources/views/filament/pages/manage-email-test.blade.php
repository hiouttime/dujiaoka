<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}
        
        <div class="mt-6">
            <x-filament-actions::group :actions="$this->getFormActions()" />
        </div>
    </form>
</x-filament-panels::page>