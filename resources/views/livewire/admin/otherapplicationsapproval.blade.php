<div>
    <x-breadcrumbs :items="$breadcrumbs"    class="bg-base-300 p-3 rounded-box mt-2" />
    <x-card title="Other Applications Approvals" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-input type="text" placeholder="Search" wire:model.live="search" />
                    <x-select wire:model.live="year" placeholder="Select Year" :options="$applicationsessions" option-label="year" option-value="year" />
        </x-slot:menu>
        <x-hr/>
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Other Service</th>
                    <th>Period</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($otherapplications as $otherapplication)
                <tr>
                    <td>{{ $otherapplication?->customer?->name }} {{ $otherapplication?->customer?->surname }}</td>
                    <td>{{ $otherapplication?->otherservice?->name }}</td>
                    <td>{{ $otherapplication?->period }}</td>
                    <td>{{ $otherapplication?->status }}</td>
                    <td class="flex justify-end">
                           <x-button icon="o-eye" label="Make decision" class="btn-sm btn-info" wire:click="getotherapplication('{{ $otherapplication->uuid }}')"  spinner />
                    </td>
                </tr>
            </tbody>
            @empty
            <tr>
                <td colspan="5" class="text-center">No other applications found</td>
            </tr>
            @endforelse
        </table>
    </x-card>
    <x-modal wire:model="viewmodal" title="Other Application" box-class="max-w-6xl h-screen overflow-y-auto" separator>
        <x-card title="Other Application" separator class="mt-5 border-2 border-gray-200">
            <table class="table table-compact">
              <tr>
                  <td>Other Service</td>
                  <td>{{ $otherapplication?->otherservice->name }}</td>
              </tr>
              <tr>
                  <td>Period</td>
                  <td>{{ $otherapplication?->period }}</td>
              </tr>
              <tr>
                  <td>Status</td>
                  <td>{{ $otherapplication?->status }}</td>
              </tr>
              <tr>
                  <td>Tradename</td>
                  <td>{{ $otherapplication?->tradename }}</td>
              </tr>
              <tr>
                  <td>Profession</td>
                  <td>{{ $otherapplication?->customerprofession?->profession?->name }}</td>
              </tr>
              <tr>
                <th>Action</th>
                <td><x-button icon="o-hand-thumb-up" label="Capture decision" class="btn-sm btn-success" wire:click="opendecisionmodal()" spinner />
                </td>
              </tr>
            </table>
           </x-card>
           <x-card title="Required Documents" separator class="mt-5 border-2 border-gray-200">
              <table class="table table-compact">
                      @forelse ($uploaddocuments as $uploaddocument)
                      <tr>
                          <td>{{ $uploaddocument["document_name"] }}</td>
                          <td><div class="{{ $uploaddocument["upload"] ? "text-green-500" : "text-red-500" }}">{{ $uploaddocument["upload"] ? "Uploaded" : "Not uploaded" }}</div></td>
                          <td>
                              <div class="flex items-center justify-end space-x-2">
                              @if($uploaddocument["upload"])
                                         <x-button icon="o-document-magnifying-glass" label="View" class="btn-sm btn-info " wire:click="viewdocument('{{ $uploaddocument['file'] }}')" spinner />
                 @endif
                              </div>
                          </td>
                      </tr>
                      @empty
                      <tr>
                          <td colspan="3" class="text-center">No documents found</td>
                      </tr>
                  </tbody>
                  @endforelse
              </table>
           </x-card>
           <x-card title="Invoice details" separator class="mt-5 border-2 border-gray-200">
            
            
                  <table class="table table-compact">
                      <thead>
                          <tr>
                              <th>Invoice Number</th>
                              <th>Amount</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                  <tr>
                      <td>Invoice Number</td>
                      <td>{{ $invoice?->invoice_number }}</td>
                  </tr>
                  <tr>
                      <td>Amount</td>
                      <td>{{ $invoice?->currency?->name }} {{ $invoice?->amount }}</td>
                  </tr>
                  <tr>
                      <td>Status</td>
                      <td>{{ $invoice?->status }}</td>
                  </tr>
                  </tbody>
              </table>
           
           </x-card>
    </x-modal>
    <x-modal wire:model="documentview" title="Document" box-class="max-w-4xl" separator>
       <!-- #region document view -->
       <iframe src="{{$documenturl}}" class="w-full h-screen"></iframe>
       <!-- #endregion -->
    </x-modal>
    <x-modal wire:model="decisionmodal" title="Decision"  separator>
        <x-form wire:submit="makedecision">
            <div class="grid gap-4">
                <x-select placeholder="Status" wire:model.live="decisionstatus" :options="[['id'=>'APPROVED','name'=>'Approved'],['id'=>'REJECTED','name'=>'Rejected']]" />
                @if($decisionstatus == "REJECTED")
                <x-textarea placeholder="Comment" wire:model="comment" />
                @endif
            </div>
  
        <x-slot:actions>
            <x-button icon="o-x-mark" label="Cancel" type="button" class="btn-sm btn-error" wire:click="closemodal" />
            <x-button icon="o-check" label="Save" type="submit" class="btn-sm btn-success"  spinner />
        </x-slot:actions>
        </x-form>
    </x-modal>
</div>
