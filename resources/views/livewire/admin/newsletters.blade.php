<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Newsletters Management" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-plus" 
                label="Create Newsletter" 
                class="btn-primary" 
                wire:click="openCreateModal" />
        </x-slot:menu>

        <div class="mb-4 flex gap-4 items-end">
            <div class="flex-1">
                <x-input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search newsletters..." 
                    icon="o-magnifying-glass" />
            </div>
            <div class="w-48">
                <x-input 
                    type="number" 
                    wire:model.live="selectedYear" 
                    label="Year" 
                    placeholder="Year" />
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Published Date</th>
                        <th>Broadcasted</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($newsletters as $newsletter)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $newsletter->title }}</div>
                        </td>
                        <td>{{ $newsletter->published_date ? $newsletter->published_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            @if($newsletter->is_broadcasted)
                            <span class="badge badge-success">Yes</span>
                            @else
                            <span class="badge badge-warning">No</span>
                            @endif
                        </td>
                        <td>{{ $newsletter->creator?->name ?? 'N/A' }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <x-button 
                                    icon="o-eye" 
                                    class="btn-sm btn-ghost" 
                                    wire:click="openViewModal({{ $newsletter->id }})" />
                                @if(!$newsletter->is_broadcasted)
                                <x-button 
                                    icon="o-paper-airplane" 
                                    class="btn-sm btn-info" 
                                    wire:click="broadcast({{ $newsletter->id }})" 
                                    wire:confirm="Are you sure you want to broadcast this newsletter to all customers?" />
                                @endif
                                <x-button 
                                    icon="o-pencil" 
                                    class="btn-sm btn-warning" 
                                    wire:click="openEditModal({{ $newsletter->id }})" />
                                <x-button 
                                    icon="o-trash" 
                                    class="btn-sm btn-error" 
                                    wire:click="delete({{ $newsletter->id }})" 
                                    wire:confirm="Are you sure you want to delete this newsletter?" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8">
                            <div class="text-gray-500">
                                <x-icon name="o-envelope" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                <p>No newsletters found. Create your first newsletter!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $newsletters->links() }}
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal wire:model="createModal" title="Create Newsletter" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input wire:model="title" label="Title" placeholder="Enter newsletter title" />
            <x-input wire:model="link" label="Newsletter Link" placeholder="https://example.com/newsletter" />
            <x-input type="date" wire:model="published_date" label="Published Date" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button label="Save" wire:click="save" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="editModal" title="Edit Newsletter" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input wire:model="title" label="Title" placeholder="Enter newsletter title" />
            <x-input wire:model="link" label="Newsletter Link" placeholder="https://example.com/newsletter" />
            <x-input type="date" wire:model="published_date" label="Published Date" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button label="Update" wire:click="save" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Modal -->
    <x-modal wire:model="viewModal" title="Newsletter Details" box-class="max-w-2xl">
        @if($selectedNewsletter)
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <div class="p-3 bg-gray-50 rounded-lg font-semibold">{{ $selectedNewsletter->title }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Published Date</label>
                    <div class="p-3 bg-gray-50 rounded-lg">{{ $selectedNewsletter->published_date ? $selectedNewsletter->published_date->format('Y-m-d') : 'N/A' }}</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Newsletter Link</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <a href="{{ $selectedNewsletter->link }}" target="_blank" class="text-blue-600 hover:underline break-all">
                        {{ $selectedNewsletter->link }}
                    </a>
                </div>
            </div>

            <div class="text-sm text-gray-500">
                Created by: {{ $selectedNewsletter->creator?->name ?? 'N/A' }} on {{ $selectedNewsletter->created_at->format('Y-m-d H:i') }}
            </div>
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="closeModals" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>
</div>

