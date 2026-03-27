<div style="min-height:100vh;background:#f8fafc;">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#7ec8e3,#5ab4d4);padding:40px 16px 28px;">
        <div style="max-width:960px;margin:0 auto;text-align:center;">
            <h1 style="color:#fff;font-size:26px;font-weight:800;margin:0 0 6px;">Registered Institutions</h1>
            <p style="color:rgba(255,255,255,.8);font-size:13px;margin:0 0 20px;">Accredited and approved institutions on the MLCSCZ portal</p>
            <div style="max-width:480px;margin:0 auto;position:relative;">
                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;">&#128269;</span>
                <input wire:model.live.debounce.300ms="search"
                    placeholder="Search by trade name or practitioner..."
                    style="width:100%;padding:11px 16px 11px 40px;border-radius:50px;border:none;font-size:14px;outline:none;box-shadow:0 2px 12px rgba(0,0,0,.15);box-sizing:border-box;" />
            </div>
        </div>
    </div>

    <div style="max-width:960px;margin:0 auto;padding:24px 16px;">

        {{-- Filters + count --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
            <p style="font-size:13px;color:#6b7280;margin:0;">
                Showing <strong style="color:#111827;">{{ $institutions->total() }}</strong> institution(s)
            </p>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#6b7280;">Filter by service:</span>
                <select wire:model.live="servicefilter"
                    style="padding:6px 12px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#fff;outline:none;cursor:pointer;">
                    <option value="">All Services</option>
                    @foreach($serviceoptions as $svc)
                        <option value="{{ $svc }}">{{ $svc }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($institutions->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;">
            @foreach($institutions as $inst)
            <div style="background:#fff;border-radius:16px;box-shadow:0 1px 6px rgba(0,0,0,.07);border:1px solid #f1f5f9;overflow:hidden;">
                <div style="height:4px;background:linear-gradient(90deg,#7ec8e3,#5ab4d4);"></div>
                <div style="padding:16px;">
                    {{-- Title row --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:38px;height:38px;border-radius:9px;background:#e0f4fb;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;">&#127963;</div>
                            <div>
                                <p style="font-weight:700;font-size:14px;color:#1f2937;margin:0;line-height:1.3;">{{ $inst->tradename }}</p>
                                @if($inst->service)
                                    <span style="font-size:10px;background:#eff6ff;color:#3b82f6;padding:2px 7px;border-radius:20px;font-weight:600;">{{ $inst->service }}</span>
                                @endif
                            </div>
                        </div>
                        <span style="font-size:10px;padding:3px 8px;border-radius:20px;font-weight:700;background:#dcfce7;color:#16a34a;white-space:nowrap;flex-shrink:0;">APPROVED</span>
                    </div>

                    {{-- Details --}}
                    <div style="border-top:1px solid #f3f4f6;padding-top:10px;display:flex;flex-direction:column;gap:6px;">
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#4b5563;">
                            <span>&#128100;</span>
                            <span>{{ $inst->customer?->name }} {{ $inst->customer?->surname }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#4b5563;">
                            <span>&#128197;</span>
                            <span>Period: <strong>{{ $inst->period }}</strong></span>
                        </div>
                        @if($inst->registration_date)
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#4b5563;">
                            <span>&#9989;</span>
                            <span>Registered: <strong>{{ \Carbon\Carbon::parse($inst->registration_date)->format('d M Y') }}</strong></span>
                        </div>
                        @endif
                        @if($inst->certificate_expiry_date && $inst->certificate_expiry_date != 'LIFETIME')
                        <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:#4b5563;">
                            <span>&#128338;</span>
                            <span>Expires: <strong>{{ \Carbon\Carbon::parse($inst->certificate_expiry_date)->format('d M Y') }}</strong></span>
                        </div>
                        @endif
                    </div>

                    {{-- View more --}}
                    <button wire:click="viewdetail({{ $inst->id }})"
                        style="margin-top:12px;width:100%;padding:7px;border-radius:8px;border:1px solid #e0f4fb;background:#f0faff;color:#5ab4d4;font-size:12px;font-weight:600;cursor:pointer;">
                        View Details
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:28px;">{{ $institutions->links() }}</div>

        @else
        <div style="text-align:center;padding:70px 0;">
            <div style="font-size:52px;margin-bottom:10px;">&#127963;</div>
            <p style="color:#9ca3af;font-size:15px;font-weight:500;margin:0;">No registered institutions found</p>
            <p style="color:#d1d5db;font-size:13px;margin-top:4px;">Try adjusting your search or filter</p>
        </div>
        @endif
    </div>

    {{-- Detail modal --}}
    @if($detailmodal && $selectedinstitution)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div style="height:5px;background:linear-gradient(90deg,#7ec8e3,#5ab4d4);"></div>
            <div style="padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 style="font-size:17px;font-weight:800;color:#1f2937;margin:0;">{{ $selectedinstitution->tradename }}</h3>
                    <button wire:click="$set('detailmodal',false)"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:#9ca3af;line-height:1;">&#10005;</button>
                </div>

                <div style="display:flex;flex-direction:column;gap:10px;">
                    @if($selectedinstitution->service)
                    <div style="display:flex;gap:10px;align-items:center;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Service</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;background:#eff6ff;padding:2px 10px;border-radius:20px;">{{ $selectedinstitution->service }}</span>
                    </div>
                    @endif
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Practitioner</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;">{{ $selectedinstitution->customer?->name }} {{ $selectedinstitution->customer?->surname }}</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Period</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;">{{ $selectedinstitution->period }}</span>
                    </div>
                    @if($selectedinstitution->registration_date)
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Registration Date</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;">{{ \Carbon\Carbon::parse($selectedinstitution->registration_date)->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($selectedinstitution->certificate_expiry_date)
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Certificate Expiry</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;">
                            {{ $selectedinstitution->certificate_expiry_date == 'LIFETIME' ? 'Lifetime' : \Carbon\Carbon::parse($selectedinstitution->certificate_expiry_date)->format('d M Y') }}
                        </span>
                    </div>
                    @endif
                    @if($selectedinstitution->certificate_number)
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Certificate No.</span>
                        <span style="font-size:13px;font-weight:600;color:#1f2937;">{{ $selectedinstitution->certificate_number }}</span>
                    </div>
                    @endif
                    <div style="display:flex;gap:10px;">
                        <span style="font-size:13px;color:#6b7280;width:140px;flex-shrink:0;">Status</span>
                        <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:#dcfce7;color:#16a34a;">APPROVED</span>
                    </div>
                </div>

                <button wire:click="$set('detailmodal',false)"
                    style="margin-top:20px;width:100%;padding:10px;border-radius:10px;border:none;background:linear-gradient(135deg,#7ec8e3,#5ab4d4);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>