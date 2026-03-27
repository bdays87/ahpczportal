<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card title="Institutions" separator class="mt-5 border-2 border-gray-200" progress-indicator>
        <x-slot:menu>
            <x-input placeholder="Search" wire:model.live="search" />
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$institutions" with-pagination>
            @scope("actions", $institution)
            <div class="flex items-center space-x-2">
                <x-button icon="o-list-bullet" class="btn-sm btn-outline"
                    wire:click="viewservices({{ $institution->id }})" spinner tooltip="Services" />
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline"
                    wire:click="edit({{ $institution->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error"
                    wire:click="delete({{ $institution->id }})" wire:confirm="Are you sure?" spinner />
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No institutions found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-modal title="{{ $id ? 'Edit Institution' : 'New Institution' }}" wire:model="modal">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" />
            <x-select label="Accredited" wire:model="accredited"
                :options="[['id'=>'Y','name'=>'Yes'],['id'=>'N','name'=>'No']]"
                option-label="name" option-value="id" placeholder="Select" />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="servicesmodal" title="Services  {{ $selectedinstitution?->name }}" box-class="max-w-xl">
        <div class="bg-base-200 rounded-xl p-4 mb-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">Add New Service</p>
            <x-form wire:submit="addservice" class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <x-input label="Name" wire:model="servicename" placeholder="e.g. Laboratory Testing" />
                    <x-select label="Status" wire:model="servicestatus"
                        :options="[['id'=>'active','label'=>'Active'],['id'=>'inactive','label'=>'Inactive']]"
                        option-label="label" option-value="id" />
                </div>
                <x-textarea label="Description" wire:model="servicedescription" placeholder="Brief description" rows="2" />
                <div class="flex justify-end">
                    <x-button label="Add Service" type="submit" icon="o-plus" class="btn-primary btn-sm" spinner="addservice" />
                </div>
            </x-form>
        </div>

        @if($selectedinstitutionservices && count($selectedinstitutionservices))
            <div class="space-y-2">
                @foreach($selectedinstitutionservices as $service)
                    <div class="flex items-start justify-between p-3 rounded-xl border border-gray-100 bg-white">
                        <div>
                            <p class="font-medium text-sm text-gray-800">{{ $service->name }}</p>
                            @if($service->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $service->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <x-badge value="{{ ucfirst($service->status) }}"
                                class="{{ $service->status === 'active' ? 'badge-success' : 'badge-error' }}" />
                            <x-button icon="o-trash" class="btn-xs btn-error btn-outline"
                                wire:click="deleteservice({{ $service->id }})" wire:confirm="Remove?" spinner />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-alert class="alert-warning" title="No services yet. Add one above." />
        @endif

        <x-slot:actions>
            <x-button label="Close" @click="$wire.servicesmodal = false" />
        </x-slot:actions>
    </x-modal>
</div>