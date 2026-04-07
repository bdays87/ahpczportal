<div style="min-height:100vh;background:#f1f5f9;font-family:'Segoe UI',system-ui,sans-serif;">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%);padding:48px 16px 36px;">
        <div style="max-width:1100px;margin:0 auto;text-align:center;">
            <p style="color:rgba(255,255,255,.6);font-size:12px;text-transform:uppercase;letter-spacing:2px;margin:0 0 8px;">Official Registry</p>
            <h1 style="color:#fff;font-size:30px;font-weight:800;margin:0 0 8px;letter-spacing:.3px;">Registered Institutions</h1>
            <p style="color:rgba(255,255,255,.7);font-size:14px;margin:0 0 28px;">Accredited and approved institutions on the portal</p>
            <div style="max-width:540px;margin:0 auto;position:relative;">
                <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px;">&#128269;</span>
                <input wire:model.live.debounce.300ms="search"
                    placeholder="Search institution name, owner or practitioner..."
                    style="width:100%;padding:14px 16px 14px 46px;border-radius:50px;border:none;font-size:14px;outline:none;box-shadow:0 4px 20px rgba(0,0,0,.25);box-sizing:border-box;color:#1e293b;" />
            </div>
        </div>
    </div>

    <div style="max-width:1100px;margin:0 auto;padding:28px 16px;">

        {{-- Filter bar --}}
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:18px 22px;margin-bottom:24px;display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <div style="flex:1;min-width:150px;">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;">Province</label>
                <select wire:model.live="province_id" style="width:100%;padding:9px 12px;border-radius:9px;border:1.5px solid #e2e8f0;font-size:13px;color:#374151;background:#fff;outline:none;">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:150px;">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;">Service Type</label>
                <select wire:model.live="servicefilter" style="width:100%;padding:9px 12px;border-radius:9px;border:1.5px solid #e2e8f0;font-size:13px;color:#374151;background:#fff;outline:none;">
                    <option value="">All Services</option>
                    @foreach($serviceoptions as $svc)
                        <option value="{{ $svc }}">{{ $svc }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2;min-width:180px;">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:6px;">Search Practitioner</label>
                <input wire:model.live.debounce.300ms="practitioner" placeholder="Name or reg number..."
                    style="width:100%;padding:9px 12px;border-radius:9px;border:1.5px solid #e2e8f0;font-size:13px;color:#374151;outline:none;box-sizing:border-box;" />
            </div>
            <button wire:click="clearfilters"
                style="padding:9px 18px;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:600;color:#64748b;cursor:pointer;white-space:nowrap;">
                &#10005; Clear
            </button>
        </div>

        <p style="font-size:13px;color:#64748b;margin:0 0 20px;">
            Showing <strong style="color:#1e293b;">{{ $institutions->total() }}</strong> institution(s)
        </p>

        @if($institutions->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:20px;">
            @foreach($institutions as $inst)
            @php $empCount = $inst->instcustomers->count(); $svcCount = $inst->instservices->count(); @endphp
            <div style="background:#fff;border-radius:18px;box-shadow:0 2px 10px rgba(0,0,0,.06);border:1px solid #e2e8f0;overflow:hidden;display:flex;flex-direction:column;">
                <div style="height:5px;background:linear-gradient(90deg,#1e3a5f,#2563eb);"></div>
                <div style="padding:20px;flex:1;display:flex;flex-direction:column;">

                    {{-- Title --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">&#127963;</div>
                            <div>
                                <p style="font-weight:700;font-size:15px;color:#1e293b;margin:0;line-height:1.3;">{{ $inst->tradename }}</p>
                                <p style="font-size:11px;color:#64748b;margin:2px 0 0;">{{ $inst->otherservice->name }}</p>
                            </div>
                        </div>
                        <span style="font-size:10px;padding:3px 10px;border-radius:20px;font-weight:700;background:#dcfce7;color:#15803d;white-space:nowrap;flex-shrink:0;border:1px solid #bbf7d0;">APPROVED</span>
                    </div>

                    {{-- Owner + Province --}}
                    <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#475569;">
                            <span style="width:16px;text-align:center;">&#128100;</span>
                            <span>{{ $inst->customer?->name }} {{ $inst->customer?->surname }}</span>
                        </div>
                        @if($inst->customer?->province)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#475569;">
                            <span style="width:16px;text-align:center;">&#128205;</span>
                            <span>{{ $inst->customer->province->name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Stats row --}}
                    <div style="display:flex;gap:8px;margin-bottom:16px;">
                        <div style="flex:1;background:#f0f9ff;border-radius:10px;padding:10px;text-align:center;border:1px solid #bae6fd;">
                            <p style="font-size:20px;font-weight:800;color:#0369a1;margin:0;">{{ $svcCount }}</p>
                            <p style="font-size:10px;color:#0369a1;margin:2px 0 0;font-weight:600;">Services</p>
                        </div>
                        <div style="flex:1;background:#f0fdf4;border-radius:10px;padding:10px;text-align:center;border:1px solid #bbf7d0;">
                            <p style="font-size:20px;font-weight:800;color:#15803d;margin:0;">{{ $empCount }}</p>
                            <p style="font-size:10px;color:#15803d;margin:2px 0 0;font-weight:600;">Practitioners</p>
                        </div>
                    </div>

                    {{-- Service pills --}}
                    @if($svcCount)
                    <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px;">
                        @foreach($inst->instservices->take(3) as $svc)
                        <span style="font-size:10px;background:#eff6ff;color:#2563eb;padding:3px 9px;border-radius:20px;font-weight:600;border:1px solid #bfdbfe;">{{ $svc->name }}</span>
                        @endforeach
                        @if($svcCount > 3)
                        <span style="font-size:10px;background:#f1f5f9;color:#64748b;padding:3px 9px;border-radius:20px;border:1px solid #e2e8f0;">+{{ $svcCount - 3 }} more</span>
                        @endif
                    </div>
                    @endif

                    <div style="flex:1;"></div>

                    <button wire:click="viewdetail({{ $inst->id }})"
                        style="width:100%;padding:10px;border-radius:10px;border:none;background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;font-size:13px;font-weight:700;cursor:pointer;letter-spacing:.3px;">
                        View Full Details &#8594;
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:32px;">{{ $institutions->links() }}</div>

        @else
        <div style="text-align:center;padding:90px 0;">
            <div style="font-size:60px;margin-bottom:14px;">&#127963;</div>
            <p style="color:#94a3b8;font-size:16px;font-weight:600;margin:0;">No registered institutions found</p>
            <p style="color:#cbd5e1;font-size:13px;margin-top:6px;">Try adjusting your search or filters</p>
        </div>
        @endif
    </div>

    {{-- ── DETAIL MODAL ── --}}
    @if($detailmodal && $selectedinstitution)
    <div style="position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;"
        wire:click.self="$set('detailmodal',false)">
        <div style="background:#f8fafc;border-radius:22px;width:100%;max-width:720px;max-height:92vh;overflow-y:auto;box-shadow:0 32px 80px rgba(0,0,0,.3);">

            {{-- Modal top bar --}}
            <div style="background:linear-gradient(135deg,#1e3a5f,#2563eb);border-radius:22px 22px 0 0;padding:24px 28px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:22px;">&#127963;</div>
                    <div>
                        <h3 style="font-size:19px;font-weight:800;color:#fff;margin:0;">{{ $selectedinstitution->tradename }}</h3>
                        <p style="font-size:12px;color:rgba(255,255,255,.7);margin:3px 0 0;">{{ $selectedinstitution->otherservice->name }}</p>
                    </div>
                </div>
                <button wire:click="$set('detailmodal',false)"
                    style="background:rgba(255,255,255,.15);border:none;width:36px;height:36px;border-radius:50%;font-size:16px;cursor:pointer;color:#fff;display:flex;align-items:center;justify-content:center;">&#10005;</button>
            </div>

            <div style="padding:24px 28px;display:flex;flex-direction:column;gap:22px;">

                {{-- Institution info --}}
                <div style="background:#fff;border-radius:14px;padding:18px;border:1px solid #e2e8f0;">
                    <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:0 0 14px;">&#127970; Institution Info</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div>
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Owner / Main Practitioner</p>
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0;">{{ $selectedinstitution->customer?->name }} {{ $selectedinstitution->customer?->surname }}</p>
                        </div>
                        @if($selectedinstitution->customer?->province)
                        <div>
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Province</p>
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0;">{{ $selectedinstitution->customer->province->name }}</p>
                        </div>
                        @endif
                        @if($selectedinstitution->registration_date)
                        <div>
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Registration Date</p>
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0;">{{ \Carbon\Carbon::parse($selectedinstitution->registration_date)->format('d M Y') }}</p>
                        </div>
                        @endif
                        <div>
                            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Status</p>
                            <span style="font-size:11px;padding:3px 12px;border-radius:20px;font-weight:700;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">APPROVED</span>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                @if($selectedinstitution->instservices->count())
                <div style="background:#fff;border-radius:14px;padding:18px;border:1px solid #e2e8f0;">
                    <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:0 0 14px;">&#9881; Services Offered ({{ $selectedinstitution->instservices->count() }})</p>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;">
                        @foreach($selectedinstitution->instservices as $svc)
                        <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:12px;padding:14px;border:1px solid #bfdbfe;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                <span style="width:28px;height:28px;border-radius:8px;background:#2563eb;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">&#128203;</span>
                                <p style="font-size:13px;font-weight:700;color:#1e40af;margin:0;">{{ $svc->name }}</p>
                            </div>
                            @if($svc->description)
                            <p style="font-size:11px;color:#64748b;margin:6px 0 0;line-height:1.5;">{{ $svc->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Practitioners --}}
                @if($selectedinstitution->instcustomers->count())
                <div style="background:#fff;border-radius:14px;padding:18px;border:1px solid #e2e8f0;">
                    <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:0 0 14px;">&#128101; Practitioners ({{ $selectedinstitution->instcustomers->count() }})</p>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($selectedinstitution->instcustomers as $emp)
                        @php
                            $prof = $emp->customer->customerprofessions->first();
                            $isCompliant = $prof ? $prof->isCompliant() : false;
                            $registertype = $prof?->registertype?->name ?? '—';
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #e2e8f0;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">&#128100;</div>
                                <div>
                                    <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0;">{{ $emp->customer->name }} {{ $emp->customer->surname }}</p>
                                    <div style="display:flex;align-items:center;gap:6px;margin-top:4px;flex-wrap:wrap;">
                                        @if($emp->customer->regnumber)
                                        <span style="font-size:10px;color:#64748b;background:#f1f5f9;padding:2px 7px;border-radius:6px;border:1px solid #e2e8f0;">{{ $emp->customer->regnumber }}</span>
                                        @endif
                                        <span style="font-size:10px;color:#7c3aed;background:#f5f3ff;padding:2px 7px;border-radius:6px;border:1px solid #ddd6fe;font-weight:600;">{{ $registertype }}</span>
                                        <span style="font-size:10px;padding:2px 8px;border-radius:6px;font-weight:700;
                                            {{ $isCompliant ? 'background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;' : 'background:#fee2e2;color:#dc2626;border:1px solid #fecaca;' }}">
                                            {{ $isCompliant ? '✓ Compliant' : '✗ Non-Compliant' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;margin-left:10px;">
                                <span style="font-size:10px;padding:3px 9px;border-radius:20px;font-weight:600;background:#fef3c7;color:#b45309;border:1px solid #fde68a;">{{ $emp->employmenttype }}</span>
                                @if($emp->date_employed)
                                <p style="font-size:10px;color:#94a3b8;margin:4px 0 0;">Since {{ \Carbon\Carbon::parse($emp->date_employed)->format('M Y') }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!$selectedinstitution->instservices->count() && !$selectedinstitution->instcustomers->count())
                <div style="text-align:center;padding:30px;color:#94a3b8;font-size:13px;">No services or practitioners on record yet.</div>
                @endif

                <button wire:click="$set('detailmodal',false)"
                    style="width:100%;padding:13px;border-radius:12px;border:none;background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;font-weight:700;font-size:14px;cursor:pointer;letter-spacing:.3px;">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
