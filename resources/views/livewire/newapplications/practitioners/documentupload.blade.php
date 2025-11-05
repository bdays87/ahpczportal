<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />

    <x-card  separator class="mt-5 border-2 border-gray-200">
        <x-steps wire:model="step" stepper-classes="w-full p-5 bg-base-200">
            <x-step step="1" text="Required documents">
                <x-card class="border-2 mt-2 border-gray-200">

                    @php  
            
                    $countpendingupload = collect($uploaddocuments)->where('upload', false)->count();
                    
                    @endphp
                    @if ($countpendingupload > 0)
                    <div class="text-center text-red-500 rounded-2xl p-5 font-bold bg-red-100">
                        You have {{ $countpendingupload }} pending documents to upload. Please upload them to continue.
                    </div>
                    @else
                    <x-alert title="Documents uploaded" description="All documents have been uploaded successfully." icon="o-check" class="alert-success">
                        <x-slot:actions>
                            <x-button label="Proceed" icon="o-arrow-right" link="{{ route('newapplications.practitioners.qualificationscapture', $uuid) }}"/>
                        </x-slot:actions>
                    </x-alert>
                    @endif
                 <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Upload</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($uploaddocuments as $uploaddocument)
                        <tr>
                            <td>{{ $uploaddocument['document_name'] }}</td>
                            <td>
                                @if ($uploaddocument['upload'])
                                <x-icon name="o-check" class="text-green-500" />
                                @else
                                <x-icon name="o-x-mark" class="text-red-500" />
                                @endif
                            </td>
                            <td class="flex justify-end items-center space-x-2">
                                @if ($uploaddocument['upload'])
                                <x-button wire:click="removeDocument({{ $uploaddocument['document_id'] }})" icon="o-trash" class="btn btn-xs btn-error">Remove</x-button>
                           
                                   @else
                                <x-button wire:click="openuploadmodal({{ $uploaddocument['document_id'] }})" icon="o-arrow-up-tray" class="btn btn-xs btn-primary">Upload</x-button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-2 bg-red-400 text-red">No documents found</td>
                        </tr>
                        @endforelse
                    </tbody>
                 </table>
                
                </x-card>


            </x-step>
            <x-step step="2" text="Qualifications" />
            <x-step step="3" text="Assessment invoice" />
            <x-step step="4" text="Registration invoice" />
            <x-step step="5" text="Practitioner certificate invoice" />

        </x-steps>
    </x-card>

    
<x-modal wire:model="uploadmodal" title="Upload Document" separator>
    <x-form wire:submit="uploadDocument">
        <div class="grid  gap-2">
            <x-input label="File" wire:model="file" type="file" />
            @if(auth()->user()->accounttype_id == 1)
            <x-checkbox label="Document verified" wire:model="verified" />
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.uploadmodal = false" />
            <x-button label="Upload" type="submit" class="btn-primary" spinner="uploadDocument" />
        </x-slot:actions>
    </x-form>
</x-modal>

</div>
