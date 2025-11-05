<div>
  <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
  <x-card title="Users" separator class="mt-5 border-2 border-gray-200">
    <x-slot:menu>
        <x-input placeholder="Search" wire:model.live="search" />
        <x-select  wire:model.live="accounttypefilter" placeholder="Filter by type" :options="$accounttypes" option-label="name" option-value="id" />
        <x-button label="New" responsive icon="o-plus" class="btn-outline" @click="$wire.modal = true" />
    </x-slot:menu>

    <x-table :headers="$headers" :rows="$users" class="table table-zebra"   with-pagination>
        @scope('cell_status', $user)
        <x-badge :value="$user->status" :class="$user->status == 'active' ? 'badge-success' : 'badge-error'" />
        @endscope
        @scope('actions', $user)
        <div class="flex items-center space-x-2">
            @can('users.modify')
            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $user->id }})" spinner />
            @endcan
            @can('users.delete')
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $user->id }})" confirm="Are you sure?" spinner />
            @endcan
            @can('users.view')
            <x-button icon="o-eye" class="btn-sm btn-outline btn-success" 
                link="{{ route('users.show', $user->uuid) }}" />
            @endcan
        </div>
        @endscope
        <x-slot:empty>
            <x-alert class="alert-error" title="No users found." />
        </x-slot:empty>
    </x-table>
</x-card>

<x-modal wire:model="modal"  title="{{ $id ? 'Edit User' : 'New User' }}">
    <x-form wire:submit="{{ $id ? 'update' : 'save' }}">
        <div class="grid grid-cols-2 gap-2">
        <x-input label="Name" wire:model="name" />
        <x-input label="Surname" wire:model="surname" />
        <x-input label="Email" wire:model="email" />
        <x-input label="Phone" wire:model="phone" />
        @if($id)
        <x-select label="Status" wire:model="status" placeholder="Select Status" :options="[['id'=>'active', 'name'=>'Active'], ['id'=>'inactive', 'name'=>'Inactive']]" option-label="name" option-value="id" />
        @endif
        <x-select label="Account Type" wire:model="accounttype_id" placeholder="Select Account Type" :options="$accounttypes" option-label="name" option-value="id" />
        </div>
     

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.modal = false" />
            <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>
