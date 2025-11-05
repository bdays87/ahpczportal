<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="CPD Approvals" separator class="border-2 border-gray-200">
        <x-slot:menu>
            <x-input wire:model.live="year" placeholder="Select Year" />
            <x-select wire:model.live="status" placeholder="Select Status" :options="[['id'=>'AWAITING','name'=>'AWAITING'],['id'=>'APPROVED','name'=>'APPROVED'],['id'=>'REJECTED','name'=>'REJECTED']]" option-label="name" option-value="id" />
        </x-slot:menu>
        <table class="table table-compact">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Practitioner</th>
                    <th>Profession</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Points</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody> 
                @forelse ($cdps??[] as $cdp)
                <tr>
                    <td>{{ $cdp->year }}</td>
                    <td>{{ $cdp->customerprofession->customer->firstname }} {{ $cdp->customerprofession->customer->surname }}</td>
                    <td>{{ $cdp->customerprofession->profession->name }}</td>
                    <td>{{ $cdp->title }}</td>
                    <td>{{ $cdp->type }}</td>
                    <td>{{ $cdp->duration }} {{ $cdp->durationunit }}</td>
                    <td>{{ $cdp->points }}</td>
                    <td>{{ $cdp->status }}</td>
                    <td>
                      
                    </td>
                    <td>
                        <div class="flex items-center justify-end space-x-2">
                            <x-button icon="o-eye"  class="btn-sm btn-info btn-outline" 
                            wire:click="viewCdp({{ $cdp->id }})" />
                          
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No CDP found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-modal wire:model="viewmodal" title="CDP Details" box-class="max-w-6xl">
        <table class="table table-compact">
            <tbody>
                <tr>
                    <td>Year</td>
                    <td>{{ $cdp->year }}</td>
                </tr>
                <tr>
                    <td>Practitioner</td>
                    <td>{{ $cdp->customerprofession->customer->firstname }} {{ $cdp->customerprofession->customer->surname }}</td>
                </tr>
                <tr>
                    <td>Profession</td>
                    <td>{{ $cdp->customerprofession->profession->name }}</td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td>{{ $cdp->title }}</td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td>{{ $cdp->type }}</td>
                </tr>
                <tr>
                    <td>Duration</td>
                    <td>{{ $cdp->duration }} {{ $cdp->durationunit }}</td>
                </tr>
                <tr>
                    <td>Points</td>
                    <td>{{ $cdp->points }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{ $cdp->status }}</td>
                </tr>
                <tr>
                    <td>Comment</td>
                    <td>{{ $cdp->comment }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table table-compact">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cdp->attachments as $attachment)
                <tr>
                    <td>Attachments</td>
                    <td>{{ $attachment->type }}</td>
                    <td>{{ $attachment->created_at }}</td>
                    <td>
                        <x-button icon="o-eye"  class="btn-sm btn-info btn-outline" 
                        wire:click="viewattachment({{ $attachment->id }})" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">
                        <div class="text-center text-red-500 p-5 font-bold bg-red-50">
                       No attachments found.</div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-card title="Assign Points" class="mt-2 border-2 border-gray-200" separator>
        <x-form wire:submit="savepoints">
            <div class="grid grid-cols-3 gap-2">
                <x-input placeholder="Points" type="number" wire:model="points" />
                <x-input placeholder="Comment" wire:model="comment" />
                <x-button icon="o-plus" label="Save" class="btn-primary" spinner type="submit" />
            </div>
        </x-form>
        </x-card>
    </x-modal>

    <x-modal wire:model="viewattachmentmodal" title="Attachment Details" box-class="max-w-4xl">
        <div class="flex items-center justify-center">
            <iframe src="{{ $documenturl }}" width="100%" height="500px"></iframe>
              
          
        </div>
    </x-modal>
</div>
