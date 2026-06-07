<div class="fixed inset-0 top-[65px] flex items-center justify-center px-4 pt-16"
    style="background: linear-gradient(135deg, #7ec8e3 0%, #a8d8ea 50%, #d0eaf5 100%);">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8">

            @php
                $field = 'width:100%; border:1.5px solid #e5e7eb; border-radius:12px; background:#f9fafb; padding:10px 16px; font-size:14px; color:#1f2937; outline:none;';
            @endphp

            {{-- Step indicator --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:24px;">
                @foreach([1,2,3] as $s)
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;
                        {{ $step >= $s ? 'background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;' : 'background:#f1f5f9;color:#94a3b8;border:1.5px solid #e2e8f0;' }}">
                        {{ $s }}
                    </div>
                    @if($s < 3)
                    <div style="width:40px;height:2px;border-radius:2px;{{ $step > $s ? 'background:#7ec8e3;' : 'background:#e2e8f0;' }}"></div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Error --}}
            @if($error)
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:16px;">
                {{ $error }}
            </div>
            @endif

            {{--  STEP 1: National ID  --}}
            @if($step == 1)
                <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px;">Reset Password</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Enter your National ID to get started.</p>

                <form wire:submit.prevent="searchByNationalId" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">National ID</label>
                        <input type="text" wire:model="nationalid" placeholder="e.g. 12345678A00" style="{{ $field }}" />
                        @error('nationalid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                        style="width:100%;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                        <span wire:loading.remove wire:target="searchByNationalId">Continue &#8594;</span>
                        <span wire:loading wire:target="searchByNationalId">Searching...</span>
                    </button>
                </form>
            @endif

            {{--  STEP 2: Email  --}}
            @if($step == 2)
                {{-- Found banner --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <span style="font-size:22px;">&#9989;</span>
                    <div>
                        <p style="font-weight:700;font-size:13px;color:#15803d;margin:0;">Customer found</p>
                        <p style="font-size:12px;color:#166534;margin:2px 0 0;">{{ $foundcustomer->name }} {{ $foundcustomer->surname }}</p>
                    </div>
                </div>

                <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Verify your email</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Enter the email address linked to your account.</p>

                <form wire:submit.prevent="verifyEmail" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" wire:model="email" placeholder="you@example.com" style="{{ $field }}" />
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="button" wire:click="back"
                            style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;color:#64748b;font-weight:600;font-size:14px;cursor:pointer;">
                            &#8592; Back
                        </button>
                        <button type="submit"
                            style="flex:2;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                            <span wire:loading.remove wire:target="verifyEmail">Verify &#8594;</span>
                            <span wire:loading wire:target="verifyEmail">Verifying...</span>
                        </button>
                    </div>
                </form>
            @endif

            {{--  STEP 3: New Password  --}}
            @if($step == 3)
                <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Set new password</h2>
                <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Choose a strong password of at least 8 characters.</p>

                <form wire:submit.prevent="resetPassword" class="space-y-4">
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" wire:model="new_password"
                                placeholder="Min 8 characters" style="{{ $field }} padding-right:2.75rem;" />
                            <button type="button" @click="show=!show" tabindex="-1"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" wire:model="new_password_confirmation"
                                placeholder="Repeat password" style="{{ $field }} padding-right:2.75rem;" />
                            <button type="button" @click="show=!show" tabindex="-1"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;line-height:0;">
                                <svg x-show="!show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.163-3.592M6.53 6.533A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('new_password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex;gap:10px;">
                        <button type="button" wire:click="back"
                            style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;color:#64748b;font-weight:600;font-size:14px;cursor:pointer;">
                            &#8592; Back
                        </button>
                        <button type="submit"
                            style="flex:2;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#5ab4d4,#7ec8e3);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                            <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                            <span wire:loading wire:target="resetPassword">Saving...</span>
                        </button>
                    </div>
                </form>
            @endif

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;text-align:center;font-size:13px;color:#64748b;">
                Remember your password?
                <a href="{{ route('login') }}" style="color:#5ab4d4;font-weight:600;text-decoration:none;">Sign in</a>
            </div>

        </div>
    </div>
</div>