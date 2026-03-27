<div class="fixed inset-0 top-[65px] overflow-y-auto px-4 pt-16 pb-10"
    style="background: linear-gradient(135deg, #7ec8e3 0%, #a8d8ea 50%, #d0eaf5 100%);">

    <div class="w-full max-w-2xl mx-auto">
        <p><br/></p>
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8">

           
            @php
                $field = 'width:100%; border:1.5px solid #e5e7eb; border-radius:12px; background:#f9fafb; padding:10px 16px; font-size:14px; color:#1f2937; outline:none; transition:border-color .2s;';
            @endphp

            <form wire:submit.prevent="register" class="space-y-4">
 <h2 class="text-xl font-bold text-gray-800 mb-1">Create an account</h2>
            <p class="text-sm text-gray-400 mb-6">Fill in your details to get started</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text" wire:model.blur="name" placeholder="First name" style="{{ $field }}" />
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Surname</label>
                        <input type="text" wire:model.blur="surname" placeholder="Last name" style="{{ $field }}" />
                        @error('surname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" wire:model.blur="email" placeholder="you@example.com" style="{{ $field }}" />
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="tel" wire:model.blur="phone" placeholder="0771234567" style="{{ $field }}" />
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" wire:model="password"
                                placeholder="Min 8 characters"
                                style="{{ $field }} padding-right:2.75rem;" />
                            <button type="button" @click="show = !show" tabindex="-1"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#9ca3af;line-height:0;">
                                <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" wire:model="password_confirmation"
                                placeholder="Repeat password"
                                style="{{ $field }} padding-right:2.75rem;" />
                            <button type="button" @click="show = !show" tabindex="-1"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#9ca3af;line-height:0;">
                                <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Account Type</label>
                    <select wire:model="accounttype_id" style="{{ $field }}">
                        <option value="">Select account type</option>
                        @foreach($accounttypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('accounttype_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl font-semibold text-sm text-white shadow-lg transition-all active:scale-[.98] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #5ab4d4, #7ec8e3);">
                    <svg wire:loading.remove wire:target="register" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <svg wire:loading wire:target="register" style="width:16px;height:16px;" class="animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="register">Create Account</span>
                    <span wire:loading wire:target="register">Creating...</span>
                </button>

            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-[#5ab4d4] hover:underline">Sign in</a>
                </p>
            </div>

        </div>
    </div>
</div>
