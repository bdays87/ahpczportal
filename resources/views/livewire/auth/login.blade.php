<div class="fixed inset-0 top-[65px] flex items-center justify-center px-4 pt-16"
    style="background: linear-gradient(135deg, #7ec8e3 0%, #a8d8ea 50%, #d0eaf5 100%);">

    <div class="w-full max-w-xl mt-5">

       
       <p><br/></p>
        <p><br/></p>
         <p><br/></p>
        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8 pt-10">

            <h2 class="text-gray-800 text-xl font-bold mb-1">Sign in to your account</h2>
            <p class="text-gray-400 text-sm mb-6">Enter your credentials below</p>

            @if ($error)
                <div class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-100 rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    {{ $error }}
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-5">

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <input
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        placeholder="you@example.com"
                        style="width:100%; border:1.5px solid #e5e7eb; border-radius:12px; background:#f9fafb; padding:10px 16px; font-size:14px; color:#1f2937; outline:none;"
                    />
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative flex items-center">
                        <input
                            :type="show ? 'text' : 'password'"
                            wire:model="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            style="width:100%; border:1.5px solid #e5e7eb; border-radius:12px; background:#f9fafb; padding:10px 16px; font-size:14px; color:#1f2937; outline:none; padding-right:2.75rem;"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            tabindex="-1"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; padding:0; cursor:pointer; color:#9ca3af; line-height:0;"
                        >
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

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-gray-300 accent-[#7ec8e3]" />
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('forget') }}" class="text-sm font-medium text-[#5ab4d4] hover:text-[#3a9abf] hover:underline transition-colors">
                        Forgot password?
                    </a>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-3 rounded-xl font-semibold text-sm text-white shadow-lg transition-all active:scale-[.98] flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #5ab4d4, #7ec8e3);"
                >
                    <svg wire:loading.remove wire:target="login" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <svg wire:loading wire:target="login" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="login">Sign In</span>
                    <span wire:loading wire:target="login">Signing in...</span>
                </button>

            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-[#5ab4d4] hover:underline">Create one</a>
                </p>
            </div>

        </div>

    </div>
</div>
