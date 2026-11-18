<div>
   <x-card class="border-2 border-gray-200">
  
       
            <div class="grid lg:grid-cols-2 gap-2">
                <div class="flex justify-center">
                    @if($customer->profile)
                    <img src="@fileurl($customer->profile, '/imgs/noimage.jpg')" class="lg:w-100 lg:h-100 w-40 h-40" alt="">
                    @else
                    <img src="/imgs/noimage.jpg" class="lg:w-100 lg:h-100 w-40 h-40" alt="">
                    @endif
                </div>
                <div>
                    <x-card>
                    <table class="table table-xs table-compact">
                        <tbody>
                        <tr>
                            <td>Registration Number</td>
                            <td>{{ $customer->regnumber }}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td>Surname</td>
                            <td>
                                {{ $customer->surname }}
                                @can('customers.modify')
                                    <x-button label="Correct name" icon="o-pencil" class="btn-xs btn-outline ml-3" wire:click="openname" spinner />
                                @endcan
                            </td>
                        </tr>
                        <tr>
                            <td>Previous Name</td>
                            <td>{{ $customer->previousname }}</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>{{ $customer->dob }}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{{ $customer->gender }}</td>
                        </tr>
                        <tr>
                            <td>Marital Status</td>
                            <td>{{ $customer->maritalstatus }}</td>
                        </tr>
                        <tr>
                            <td>Identity Type</td>
                            <td>{{ $customer->identificationtype }}</td>
                        </tr>
                        <tr>
                            <td>Identity Number</td>
                            <td>{{ $customer->identificationnumber}}</td>
                        </tr>
                        <tr>
                            <td>Nationality</td>
                            <td>{{ $customer->nationality?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Province</td>
                            <td>{{ $customer->province?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>{{ $customer->city?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Employment Status</td>
                            <td>{{ $customer->employmentstatus?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Employment Location</td>
                            <td>{{ $customer->employmentlocation?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <td>Place of Birth</td>
                            <td>{{ $customer->place_of_birth }}</td>
                        </tr>
                        </tbody>
                    </table>
                    </x-card>
        </div>
    </div>
    
   </x-card>
   <x-modal wire:model="namemodal" title="Correct name" separator>
       <p class="text-sm mb-3">Fixes a mistake in the customer's name. It updates the profile and the login account straight away; the "previous name" is not touched.</p>
       <div class="space-y-3">
           <x-input label="Name" wire:model="edit_name" />
           <x-input label="Surname" wire:model="edit_surname" />
       </div>
       <x-slot:actions>
           <x-button label="Cancel" @click="$wire.namemodal = false" />
           <x-button label="Save" class="btn-primary" wire:click="savename" spinner="savename" />
       </x-slot:actions>
   </x-modal>
   <livewire:admin.components.customerprofessions :customer="$customer" />
   <livewire:components.otherapplications :customer="$customer" />
</div>
