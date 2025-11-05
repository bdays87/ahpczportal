<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    @can('roles.access')
    

    <x-card title="Roles" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            @can('role.modify')
            <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$roles">
       
            @scope('actions', $role)
            <div class="flex items-center space-x-2">
                @can('role.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $role->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $role->id }})" confirm="Are you sure?" spinner />
                <x-button icon="o-eye" class="btn-sm btn-outline btn-success" 
                    link="{{ route('roles.show', $role->id) }}" />
                @endcan
            </div>
            @endscope
            <x-slot:empty>
                <x-alert class="alert-error" title="No roles found." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view roles." />
    @endcan

    <x-modal wire:model="modal"  title="{{ $id ? 'Edit Role' : 'New Role' }}">
        <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
            <div class="grid gap-2">
            <x-input label="Name" wire:model="name" />
            <x-select label="Account Type" wire:model="accounttype_id" placeholder="Select Account Type" :options="$accounttypes" option-label="name" option-value="id" />
            </div>
         

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
