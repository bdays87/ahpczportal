<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    @can('role.assign')
    <x-card title="Role Show" separator class="mt-5 border-2 border-gray-200" separator>
        <x-slot:menu>
            <x-input  wire:model.live="search" placeholder="Search" />
        </x-slot:menu>
        @forelse ($fullmenu as $menu)
        <x-collapse collapse-plus-minus>
            <x-slot:heading class="bg-warning/20 mt-2">
                {{ $menu->name }}
            </x-slot:heading>
            <x-slot:content class="bg-primary/10">
              <table class="table table-xs table-zebra">
                <thead>
                    <tr>
                        <th>Submodule</th>
                        <th>Permissions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menu->submodules as $submodule)
                    <tr>
                        <td>{{ $submodule->name }}</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                            @forelse ($submodule->permissions as $permission)
                            @if (in_array($permission->id, $assignedpermissions))
                            <x-button label="{{ $permission->name }}" icon-right="o-x-circle" icon-color="success" class="btn btn-xs btn-success" wire:click="removepermission({{ $permission->id }})" spinner />
                            @else
                            <x-button label="{{ $permission->name }}" icon-right="o-check-circle" icon-color="error" class="btn btn-xs btn-error" wire:click="assignpermission({{ $permission->id }})" spinner />
                            @endif
                            @empty
                            <span class="badge badge-primary">No permissions found.</span>
                            @endforelse
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2">No submodules found.</td>
                    </tr>
                    @endforelse
                </tbody>
              </table>
            </x-slot:content>
        </x-collapse>
        @empty
        <x-alert class="alert-error" title="No menu found." />
        @endforelse
    </x-card>
    @else
    <x-alert class="alert-error mt-3" title="You do not have permission to view roles." />
    @endcan
</div>
