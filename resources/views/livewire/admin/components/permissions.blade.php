<div>
    <x-button icon="o-lock-closed" class="btn-sm btn-outline btn-success" 
    wire:click="getPermissions()" spinner />

    <x-modal title="Permissions" wire:model="modal">
        <x-form wire:submit.prevent="save">
        <x-input wire:model="name">
            <x-slot:append>
                <x-button label="{{ $id ? 'Update' : 'Save' }}" class="join-item btn-primary" type="submit" spinner="save"/>
            </x-slot:append>
        </x-input>
        </x-form>
        <table class="table table-bordered table-sm mt-2">
            <thead>
                <tr>
                    <th>Permission</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ($permissions)
                @forelse ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td class="flex justify-end items-center gap-2">
                            <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                wire:click="edit({{ $permission->id }})" spinner />
            <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                wire:click="delete({{ $permission->id }})" confirm="Are you sure?" spinner />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No permissions found.</td>
                    </tr>
                @endforelse
                @else
                <tr>
                    <td colspan="2">No permissions found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </x-modal>
</div>
