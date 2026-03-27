<div style="padding:60px 24px;background:linear-gradient(180deg,#f0f9ff 0%,#fff 100%);">
    <div style="max-width:640px;margin:0 auto;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:36px;">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#0ea5e9,#7ec8e3);margin-bottom:16px;box-shadow:0 8px 24px rgba(14,165,233,.3);">
                <span style="font-size:28px;">&#128196;</span>
            </div>
            <h2 style="font-size:clamp(22px,3vw,30px);font-weight:800;color:#0f172a;margin:0 0 8px;">Certificate Verification</h2>
            <p style="color:#64748b;font-size:15px;margin:0;max-width:420px;margin-left:auto;margin-right:auto;line-height:1.6;">
                Instantly verify the authenticity of any {{ config('app.name') }} professional certificate
            </p>
        </div>

        {{-- Search card --}}
        <div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #e2e8f0;">
            <form wire:submit.prevent="verifyCertificate">
                <label style="display:block;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">
                    Certificate Number
                </label>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <input
                        wire:model="certificate_number"
                        type="text"
                        placeholder="e.g. MLCSCZ/2024/001234"
                        style="flex:1;min-width:200px;padding:12px 16px;border-radius:12px;border:2px solid #e2e8f0;font-size:14px;color:#0f172a;outline:none;background:#f8fafc;box-sizing:border-box;transition:border-color .2s;"
                        onfocus="this.style.borderColor='#0ea5e9';this.style.background='#fff'"
                        onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'"
                    />
                    <button
                        type="submit"
                        style="padding:12px 28px;border-radius:12px;border:none;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;font-weight:700;font-size:14px;cursor:pointer;white-space:nowrap;box-shadow:0 4px 12px rgba(14,165,233,.3);"
                    >
                        <span wire:loading.remove wire:target="verifyCertificate">&#128269; Verify</span>
                        <span wire:loading wire:target="verifyCertificate">Verifying...</span>
                    </button>
                </div>
                @error('certificate_number')
                    <p style="color:#ef4444;font-size:12px;margin:6px 0 0;">{{ $message }}</p>
                @enderror
            </form>
        </div>

        {{-- Result --}}
        @if($certificate)
        <div style="margin-top:20px;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #e2e8f0;">

            {{-- Status banner --}}
            @if($certificate->isValid())
            <div style="background:linear-gradient(135deg,#16a34a,#22c55e);padding:20px 28px;display:flex;align-items:center;gap:14px;">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">&#9989;</div>
                <div>
                    <p style="color:#fff;font-weight:800;font-size:16px;margin:0;">Certificate is Valid</p>
                    <p style="color:rgba(255,255,255,.8);font-size:13px;margin:2px 0 0;">This certificate is authentic and currently active</p>
                </div>
            </div>
            @else
            <div style="background:linear-gradient(135deg,#dc2626,#ef4444);padding:20px 28px;display:flex;align-items:center;gap:14px;">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">&#10060;</div>
                <div>
                    <p style="color:#fff;font-weight:800;font-size:16px;margin:0;">Certificate Expired</p>
                    <p style="color:rgba(255,255,255,.8);font-size:13px;margin:2px 0 0;">This certificate is no longer valid</p>
                </div>
            </div>
            @endif

            {{-- Details --}}
            <div style="padding:24px 28px;">
                <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin:0 0 16px;">Certificate Details</p>
                @php
                    $row = 'display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;';
                    $lbl = 'font-size:13px;color:#64748b;font-weight:500;';
                    $val = 'font-size:13px;font-weight:700;color:#0f172a;text-align:right;';
                @endphp

                <div style="{{ $row }}">
                    <span style="{{ $lbl }}">Full Name</span>
                    <span style="{{ $val }}">{{ $certificate->customerprofession->customer->name }} {{ $certificate->customerprofession->customer->surname }}</span>
                </div>
                <div style="{{ $row }}">
                    <span style="{{ $lbl }}">Profession</span>
                    <span style="{{ $val }}">{{ $certificate->customerprofession->profession->name }}</span>
                </div>
                <div style="{{ $row }}">
                    <span style="{{ $lbl }}">Register Type</span>
                    <span style="{{ $val }}">{{ $certificate->registertype->name }}</span>
                </div>
                <div style="{{ $row }}">
                    <span style="{{ $lbl }}">Application Type</span>
                    <span style="{{ $val }}">{{ $certificate->applicationtype?->name ?? 'N/A' }}</span>
                </div>
                <div style="{{ $row }}">
                    <span style="{{ $lbl }}">Certificate Number</span>
                    <span style="font-size:13px;font-weight:700;color:#0ea5e9;font-family:monospace;">{{ $certificate->certificate_number }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                    <span style="{{ $lbl }}">Expiry Date</span>
                    <span style="{{ $val }}">{{ $certificate->certificate_expiry_date }}</span>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>