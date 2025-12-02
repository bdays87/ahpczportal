<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card  separator class="mt-5 border-2 border-gray-200">
        <x-steps wire:model="step" stepper-classes="w-full p-5 bg-base-200">
            <x-step step="1" text="Required documents" />
            <x-step step="2" text="Qualifications">
                <x-card title="Profession Related Qualifications" separator class="border-2 mt-2 border-gray-200">
                    <x-slot:menu>
                        <x-button label="Add Qualification" wire:click="$set('qualificationmodal',true)" icon="o-plus" responsive class="btn btn-primary"/>
                    </x-slot:menu>
                    @if ($customerprofession?->qualifications->count() > 0)
                    <x-alert title="Qualifications added" description=" Qualification have been added successfully." icon="o-check" class="alert-success">
                        <x-slot:actions>
                            <x-button label="Continue" wire:click="nextstep" spinner="nextstep"/>
                        </x-slot:actions>
                    </x-alert>
            
                    @else
                    <div class="text-center text-red-500 rounded-2xl p-5 font-bold bg-red-100">
                        No qualifications found for this customer profession. Please add related qualifications to continue.
                    </div>
                    @endif
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customerprofession?->qualifications??[] as $qualification)
                            <tr>
                                <td>
                                    <div><b>Name:</b> <small><i>{{ $qualification->qualification->name }}</i></small></div>
                                    <div><b>Category:</b> <small><i>{{ $qualification->qualificationcategory->name }}</i></small></div>
                                    <div><b>Level:</b> <small><i>{{ $qualification->qualificationlevel->name }}</i></small></div>
                                    <div><b>Institution:</b> <small><i>{{ $qualification->qualification->institution->name }}</i></small></div>
                                    <div><b>Year:</b> <small><i>{{ $qualification->year }}</i></small></div>
                                </td>
                                <td class="flex justify-end items-center space-x-2">
                                    @if ($qualification->status == "PENDING")
                                    <x-button wire:click="editQualification({{ $qualification->id }})" icon="o-pencil" label="Edit" responsive class="btn btn-info btn-xs"/>
                                    <x-button wire:click="removeQualification({{ $qualification->id }})" icon="o-trash" label="Delete" responsive class="btn btn-error btn-xs" wire:confirm="Are you sure you want to delete this qualification?"/>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center p-2  text-red">No qualifications found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <x-slot:actions>
                        <x-button label="Previous" icon="o-arrow-left" class="btn btn-primary btn-outline" link="{{ route('customer.profession.show',$uuid) }}"/>
                        @if ($customerprofession?->qualifications->count() > 0)
                        <x-button label="Proceed" icon="o-arrow-right" class="btn btn-primary btn-outline" wire:click="nextstep"/>
                        @endif
                    </x-slot:actions>
                </x-card> 
            </x-step>
            <x-step step="3" text="Assessment invoice" />
            <x-step step="4" text="Registration invoice" />
            <x-step step="5" text="Practitioner certificate invoice" />

        </x-steps>
    </x-card>

    <x-modal wire:model="qualificationmodal" title="{{ $customerprofessionqualification_id ? 'Edit' : 'Add' }} Qualification" separator>
        <x-form wire:submit="savequalification">
            <div class="grid grid-cols-2  gap-2">
                <x-select label="Qualification" wire:model="qualification_id" :options="$qualifications" option-label="name" option-value="id" placeholder="Select" />
                <x-select label="Category" wire:model="qualificationcategory_id" :options="$categories" option-label="name" option-value="id" placeholder="Select" />
                <x-select label="Level" wire:model="qualificationlevel_id" :options="$levels" option-label="name" option-value="id" placeholder="Select" />
                <x-input label="Year" wire:model="year" type="number" />
                <x-input label="File" wire:model="qualificationfile" type="file" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.qualificationmodal = false" />
                <x-button label="{{ $customerprofessionqualification_id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="savequalification" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
