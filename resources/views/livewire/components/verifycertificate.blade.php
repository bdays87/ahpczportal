<div>
      <!-- Certificate Verification Section -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Certificate Verification</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Verify the authenticity of a {{ config('app.title') }} professional's certification</p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-8">
                        <x-form class="space-y-6" wire:submit="verifyCertificate">
                            <div>
                                <label for="certificate-number" class="block text-sm font-medium text-gray-700 mb-1">Certificate Number</label>
                                <x-input type="text" id="certificate-number" name="certificate-number" wire:model="certificate_number"  placeholder="Enter certificate number"/>
                            </div>
                    
                            <div class="flex justify-center">
                                <x-button type="submit" label="Verify Certificate" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center" spinner="verifycertificate"/>
                                 
                            </div>
                        </x-form>
                    </div>
                </div>
                @if($certificate)
                <div class="bg-white rounded-xl mt-2 shadow-md overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Certificate Details</h2>
                    </div>
                     <div class="p-8">
                        @if($certificate->isValid())
                            <x-alert class="alert-success" title="Certificate is valid" />
                        @else
                            <x-alert class="alert-error" title="Certificate is expired" />
                        @endif
                        <table class="table table-zebra">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $certificate->customerprofession->customer->name }} {{ $certificate->customerprofession->customer->surname }}</td>
                                </tr>
                            
                                <tr>
                                    <td>Profession</td>
                                    <td>{{ $certificate->customerprofession->profession->name }}</td>
                                </tr>

                                <tr>
                                    <td>Register type</td>
                                    <td>{{ $certificate->registertype->name }}</td>
                                </tr>

                                <tr>
                                    <td>Application type</td>
                                    <td>{{ $certificate->applicationtype?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Expiry date</td>
                                    <td>{{ $certificate->certificate_expiry_date }}</td>
                                </tr>
                                <tr>
                                    <td>Expiry Status</td>
                                    <td>{{ $certificate->isValid() ? 'Valid' : 'Expired' }}</td>
                                </tr>

                            </tbody>
                        </table>
                     </div>
                </div>
                @endif

              
            </div>
        </div>
    </section>
</div>
