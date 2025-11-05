<div>
    <x-modal wire:model="showModal" title="Select Renewal Type" class="backdrop-blur">
        <div class="space-y-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Choose Renewal Type
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Select the type of renewal process you want to proceed with
                </p>
            </div>

            <div class="space-y-4">
                @foreach($typeOptions as $option)
                    <div class="relative">
                        <input 
                            type="checkbox" 
                            id="type_{{ $option['id'] }}" 
                            value="{{ $option['id'] }}"
                            wire:click="toggleType('{{ $option['id'] }}')"
                            class="sr-only peer"
                            @if($this->isSelected($option['id'])) checked @endif
                        >
                        <label 
                            for="type_{{ $option['id'] }}" 
                            class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 dark:border-gray-700 dark:hover:border-blue-400"
                        >
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $option['name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $option['description'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            @if(count($selectedTypes) > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Selected Types: {{ count($selectedTypes) }}
                            </h5>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                @foreach($selectedTypes as $selectedType)
                                    @php
                                        $typeEnum = \App\RenewalType::from($selectedType);
                                    @endphp
                                    <span class="inline-block bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs mr-1 mb-1">
                                        {{ $typeEnum->label() }}
                                    </span>
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="closeModal" class="btn-outline" />
            <x-button 
                label="Proceed" 
                @click="proceedWithRenewal" 
                class="btn-primary" 
                :disabled="count($selectedTypes) === 0"
                spinner="proceedWithRenewal"
            />
        </x-slot:actions>
    </x-modal>
</div>

