<div>
    <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 rounded-box mt-2" />
    
    <x-card title="Journals Management" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-button 
                icon="o-plus" 
                label="Create Journal" 
                class="btn-primary" 
                wire:click="openCreateModal" />
        </x-slot:menu>

        <div class="mb-4">
            <x-input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search journals..." 
                icon="o-magnifying-glass" />
        </div>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published Date</th>
                        <th>Link</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($journals as $journal)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $journal->title }}</div>
                        </td>
                        <td>{{ $journal->author ?? 'N/A' }}</td>
                        <td>{{ $journal->published_date ? $journal->published_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            <a href="{{ $journal->link }}" target="_blank" class="text-blue-600 hover:underline truncate max-w-xs block">
                                {{ Str::limit($journal->link, 40) }}
                            </a>
                        </td>
                        <td>{{ $journal->creator?->name ?? 'N/A' }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <x-button 
                                    icon="o-eye" 
                                    class="btn-sm btn-ghost" 
                                    wire:click="openViewModal({{ $journal->id }})" />
                                <x-button 
                                    icon="o-pencil" 
                                    class="btn-sm btn-warning" 
                                    wire:click="openEditModal({{ $journal->id }})" />
                                <x-button 
                                    icon="o-trash" 
                                    class="btn-sm btn-error" 
                                    wire:click="delete({{ $journal->id }})" 
                                    wire:confirm="Are you sure you want to delete this journal?" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8">
                            <div class="text-gray-500">
                                <x-icon name="o-document" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                <p>No journals found. Create your first journal!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $journals->links() }}
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal wire:model="createModal" title="Create Journal" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input wire:model="title" label="Title" placeholder="Enter journal title" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model="author" label="Author" placeholder="Enter author name" />
                <x-input type="date" wire:model="published_date" label="Published Date" />
            </div>
            <x-input wire:model="link" label="Journal Link" placeholder="https://example.com/journal" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button label="Save" wire:click="save" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="editModal" title="Edit Journal" box-class="max-w-2xl">
        <div class="space-y-4">
            <x-input wire:model="title" label="Title" placeholder="Enter journal title" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model="author" label="Author" placeholder="Enter author name" />
                <x-input type="date" wire:model="published_date" label="Published Date" />
            </div>
            <x-input wire:model="link" label="Journal Link" placeholder="https://example.com/journal" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModals" class="btn-ghost" />
            <x-button label="Update" wire:click="save" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Modal -->
    <x-modal wire:model="viewModal" title="Journal Details" box-class="max-w-2xl">
        @if($selectedJournal)
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <div class="p-3 bg-gray-50 rounded-lg font-semibold">{{ $selectedJournal->title }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                    <div class="p-3 bg-gray-50 rounded-lg">{{ $selectedJournal->author ?? 'N/A' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Published Date</label>
                    <div class="p-3 bg-gray-50 rounded-lg">{{ $selectedJournal->published_date ? $selectedJournal->published_date->format('Y-m-d') : 'N/A' }}</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Journal Link</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <a href="{{ $selectedJournal->link }}" target="_blank" class="text-blue-600 hover:underline break-all">
                        {{ $selectedJournal->link }}
                    </a>
                </div>
            </div>

            <div class="text-sm text-gray-500">
                Created by: {{ $selectedJournal->creator?->name ?? 'N/A' }} on {{ $selectedJournal->created_at->format('Y-m-d H:i') }}
            </div>
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="closeModals" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>
</div>
