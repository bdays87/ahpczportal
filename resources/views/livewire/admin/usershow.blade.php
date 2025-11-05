<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="{{ $user->name }} {{ $user->surname }}" separator class="mt-5 border-2 border-gray-200">
        <x-tabs wire:model="selectedTab">
            <x-tab name="users-tab" label="Roles" icon="o-cog-6-tooth">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td class="flex justify-end items-center gap-2">
                                   
                                    @if (in_array($role->name, $assignedroles))
                                    @if($user->id != auth()->user()->id)
                                        <x-button icon="o-x-circle" label="Remove" class="btn-sm btn-outline btn-error" 
                                            wire:click="removerole({{ $role->id }})" spinner />
                                            @endif
                                    @else
                                        <x-button icon="o-check-circle" label="Assign" class="btn-sm btn-outline btn-success" 
                                            wire:click="assignrole({{ $role->id }})" spinner />
                                    @endif
                                   
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-tab>
            <x-tab name="tricks-tab" label="Custom Permissions" icon="o-cog-6-tooth">
               <x-input wire:model.live="search" placeholder="Search" class="mt-2" />
                @forelse ($menulist as $menu)
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
                                    @if($user->id != auth()->user()->id)
                                    <x-button label="{{ $permission->name }}" icon-right="o-x-circle" icon-color="success" class="btn btn-xs btn-success" wire:click="removepermission({{ $permission->id }})" spinner />
                                     @else
                                     <x-button label="{{ $permission->name }}" icon-right="o-x-circle" icon-color="success" class="btn btn-xs btn-success" wire:click="removepermission({{ $permission->id }})" disabled spinner />
                                     @endif
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
            </x-tab>
            
        </x-tabs>
    </x-card>
</div>
