
<div class="fixed inset-0 top-[65px] overflow-y-auto px-4 pb-10"
    style="background: linear-gradient(135deg, #7ec8e3 0%, #a8d8ea 50%, #d0eaf5 100%);padding-top:120px;"
    style="background: linear-gradient(135deg, #7ec8e3 0%, #a8d8ea 50%, #d0eaf5 100%);">

    <div class="w-full max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8">

            @php
                $field = 'width:100%; border:1.5px solid #e5e7eb; border-radius:12px; background:#f9fafb; padding:10px 16px; font-size:14px; color:#1f2937; outline:none;';
            @endphp

            {{--  STEP 1: SEARCH  --}}
            @if($step == 1)
                <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 6px;">Create Account</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 24px;">First, let us check if you are already in our system.</p>

                <form wire:submit.prevent="search" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">National ID or Registration Number</label>
                        <input type="text" wire:model="searchquery"
                            placeholder="e.g. 12345678A00 or MLCSCZ/2020/001"
                            style="{{ $field }}" />
                        @error('searchquery') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @if($searcherror)
                            <div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:13px;color:#dc2626;">
                                {{ $searcherror }}
                            </div>
                        @endif
                    </div>

                    <button type="submit"
                        style="width:100%;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;"
                    >
                        <span wire:loading.remove wire:target="search">Search &#8594;</span>
                        <span wire:loading wire:target="search">Searching...</span>
                    </button>
                </form>

                <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;text-align:center;font-size:13px;color:#64748b;">
                    Already have an account?
                    <a href="{{ route('login') }}" style="color:#5ab4d4;font-weight:600;text-decoration:none;">Sign in</a>
                </div>
            @endif

            {{--  STEP 2: EXISTING CUSTOMER  CREDENTIALS ONLY  --}}
            @if($step == 2)
                {{-- Found banner --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:16px 18px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
                    <span style="font-size:28px;">&#9989;</span>
                    <div>
                        <p style="font-weight:700;font-size:14px;color:#15803d;margin:0;">Found in our system</p>
                        <p style="font-size:13px;color:#166534;margin:2px 0 0;">{{ $foundcustomer->name }} {{ $foundcustomer->surname }} &mdash; {{ $foundcustomer->regnumber }}</p>
                    </div>
                </div>

                <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Set up your login</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Just provide your email and a password to activate your account.</p>

                <form wire:submit.prevent="linkaccount" class="space-y-4">
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
                                <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="Min 8 characters" style="{{ $field }} padding-right:2.75rem;" />
                                <button type="button" @click="show=!show" tabindex="-1" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                    <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                            <div style="position:relative;">
                                <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" placeholder="Repeat password" style="{{ $field }} padding-right:2.75rem;" />
                                <button type="button" @click="show=!show" tabindex="-1" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                    <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
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

                    <div style="display:flex;gap:10px;">
                        <button type="button" wire:click="backtosearch"
                            style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;color:#64748b;font-weight:600;font-size:14px;cursor:pointer;">
                            &#8592; Back
                        </button>
                        <button type="submit"
                            style="flex:2;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                            <span wire:loading.remove wire:target="linkaccount">Activate Account &#8594;</span>
                            <span wire:loading wire:target="linkaccount">Activating...</span>
                        </button>
                    </div>
                </form>
            @endif

            {{--  STEP 3: NOT FOUND  FULL FORM  --}}
            @if($step == 3)
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:14px 18px;margin-bottom:24px;font-size:13px;color:#1d4ed8;">
                    &#8505; No existing record found. Please complete the full registration below.
                </div>

                <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Create an account</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Fill in your details to get started.</p>

                <form wire:submit.prevent="register" class="space-y-4">
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
                                <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="Min 8 characters" style="{{ $field }} padding-right:2.75rem;" />
                                <button type="button" @click="show=!show" tabindex="-1" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                    <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                            <div style="position:relative;">
                                <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" placeholder="Repeat password" style="{{ $field }} padding-right:2.75rem;" />
                                <button type="button" @click="show=!show" tabindex="-1" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                    <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
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

                    <div style="display:flex;gap:10px;">
                        <button type="button" wire:click="backtosearch"
                            style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;color:#64748b;font-weight:600;font-size:14px;cursor:pointer;">
                            &#8592; Back
                        </button>
                        <button type="submit"
                            style="flex:2;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                            <span wire:loading.remove wire:target="register">Create Account &#8594;</span>
                            <span wire:loading wire:target="register">Creating...</span>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>
