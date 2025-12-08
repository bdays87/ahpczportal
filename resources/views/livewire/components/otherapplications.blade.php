<div>
    <x-header title="Other Applications" subtitle="Manage other applications" class="mt-5 bg-gradient-to-r from-green-200 to-green-600 rounded-sm shadow-lg p-6" separator>
       
        <x-slot:actions>
            <x-button label="New" responsive icon="o-plus" class="btn-primary " @click="$wire.modal = true" />
        </x-slot:actions>
    </x-header>
   <x-card  class="mt-5 border-2 border-gray-200">
    
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Other Service</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($otherapplications as $otherapplication)
            <tr>
                <td>
                    <div><span class="font-bold">Other Service:</span> {{ $otherapplication->otherservice->name }}</div>
                    <div><span class="font-bold">Period:</span> {{ $otherapplication->period }}</div>
                    <div><span class="font-bold">Status:</span> <x-badge value="{{ $otherapplication->status }}" class="{{ $otherapplication->status=='PENDING' ? 'badge-warning' : 'badge-success' }} badge-xs" /></div>
                </td>
                <td>
                    <div class="grid lg:grid-cols-2 gap-3">

                        @if($otherapplication->status == "APPROVED" && $otherapplication->otherservice->generatecertificate == "YES")
                        <x-button icon="o-arrow-down-tray" label="Download" class="btn-sm btn-success" wire:click="downloadcertificate('{{ $otherapplication->uuid }}')" spinner />
                        @endif

                        <x-button icon="o-eye" label="View" class="btn-sm btn-info" 
                        link="{{ route('otherapplications.show', $otherapplication->uuid) }}" />
                        @if($otherapplication->status == "PENDING")
                        <x-button icon="o-pencil" label="Edit" class="btn-sm btn-info btn-outline" 
                        wire:click="edit({{ $otherapplication->id }})" spinner />
                        <x-button icon="o-trash" label="Delete" class="btn-sm btn-danger btn-outline" 
                        wire:click="delete({{ $otherapplication->id }})" wire:confirm="Are you sure?" spinner />
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">No other applications found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
   </x-card>

   <x-modal title="{{ $id ? 'Edit Other Application' : 'New Other Application' }}" wire:model="modal">
    <x-form wire:submit="save">
        <x-select label="Other Service" wire:model.live="otherservice_id" placeholder="Select Other Service" :options="$otherservices" option-label="name" option-value="id" />
        
        <x-select label="Period" wire:model="period" placeholder="Select Period" :options="$sessions" option-label="year" option-value="year" />
        @if($selectedotherservice?->practisingonly=="YES")
        <x-select label="Profession" wire:model="profession_id" placeholder="Select Profession" :options="$professions" option-label="name" option-value="id" />
        @endif
        @if($selectedotherservice?->requiretradename=="Y")
        <x-input label="Tradename" wire:model="tradename" placeholder="Enter Tradename" />
        @endif
    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.modal = false" />
        <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
    </x-slot:actions>
    </x-form>
   </x-modal>
</div>
