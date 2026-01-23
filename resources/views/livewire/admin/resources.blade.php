<div>
    <x-card title="Resources Management" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input placeholder="Search resources..." wire:model.live="search" />
            @can('configurations.modify')
            <x-button label="Add Resource" responsive icon="o-plus" class="btn-primary" @click="$wire.modifymodal = true" />
            @endcan
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$resources">
            @scope('cell_is_active', $resource)
            @if($resource->is_active)
                <x-badge value="Active" class="badge-success" />
            @else
                <x-badge value="Inactive" class="badge-error" />
            @endif
            @endscope

            @scope('cell_created_at', $resource)
            {{ $resource->created_at->format('Y-m-d H:i') }}
            @endscope

            @scope('actions', $resource)
            <div class="flex items-center space-x-2">
                @can('configurations.modify')
                <x-button icon="o-pencil" class="btn-sm btn-info btn-outline" 
                    wire:click="edit({{ $resource->id }})" spinner />
                <x-button icon="o-arrow-down-tray" class="btn-sm btn-success btn-outline" 
                    wire:click="download({{ $resource->id }})" spinner />
                <x-button icon="o-trash" class="btn-sm btn-outline btn-error" 
                    wire:click="delete({{ $resource->id }})" wire:confirm="Are you sure?" spinner />
                @endcan
            </div>
            @endscope

            <x-slot:empty>
                <x-alert class="alert-error" title="No resources found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-modal title="{{ $id ? 'Edit Resource' : 'Add Resource' }}" wire:model="modifymodal" box-class="max-w-2xl">
        <x-form wire:submit="save">
            <div class="grid gap-4">
                <x-input label="Title" wire:model="title" placeholder="Enter resource title" />
                
                <x-textarea label="Description" wire:model="description" placeholder="Enter resource description (optional)" rows="3" />
                
                <x-input type="file" label="File" wire:model="file" 
                    hint="Maximum file size: 10MB" />
                
                @if($id && !$file)
                    @php
                        $currentResource = collect($resources)->firstWhere('id', $id);
                    @endphp
                    @if($currentResource)
                    <x-alert icon="o-information-circle" class="alert-info">
                        Current file: <strong>{{ $currentResource->file_name }}</strong>
                    </x-alert>
                    @endif
                @endif

                <x-checkbox label="Active" wire:model="is_active" />
            </div>

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modifymodal = false" />
                <x-button label="{{ $id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
