<div style="min-height:100vh;background:#f8fafc;">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#7ec8e3,#5ab4d4);padding:36px 16px 28px;">
        <div style="max-width:1200px;margin:0 auto;text-align:center;">
            <h1 style="color:#fff;font-size:26px;font-weight:800;margin:0 0 6px;">Compliant Practitioners</h1>
            <p style="color:rgba(255,255,255,.8);font-size:13px;margin:0 0 20px;">Registered and currently valid practitioners on the MLCSCZ portal</p>
            <div style="max-width:500px;margin:0 auto;position:relative;">
                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;">&#128269;</span>
                <input wire:model.live.debounce.400ms="search" placeholder="Search by name or surname..."
                    style="width:100%;padding:11px 16px 11px 40px;border-radius:50px;border:none;font-size:14px;outline:none;box-shadow:0 2px 12px rgba(0,0,0,.15);box-sizing:border-box;" />
            </div>
        </div>
    </div>

    <div style="max-width:1200px;margin:0 auto;padding:24px 16px;">

        {{-- Filters --}}
        <div style="background:#fff;border-radius:14px;padding:16px 20px;box-shadow:0 1px 4px rgba(0,0,0,.07);border:1px solid #f1f5f9;margin-bottom:20px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;align-items:end;">

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">Province</label>
                    <select wire:model.live="province_id" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;">
                        <option value="">All</option>
                        @foreach($provinces as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">City</label>
                    <select wire:model.live="city_id" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;">
                        <option value="">All</option>
                        @foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">Profession</label>
                    <select wire:model.live="profession_id" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;">
                        <option value="">All</option>
                        @foreach($professions as $pr)<option value="{{ $pr->id }}">{{ $pr->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">Register Type</label>
                    <select wire:model.live="registertype_id" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;">
                        <option value="">All</option>
                        @foreach($registertypes as $rt)<option value="{{ $rt->id }}">{{ $rt->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">Gender</label>
                    <select wire:model.live="gender" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;">
                        <option value="">All</option>
                        @foreach($genderOptions as $g)<option value="{{ $g['id'] }}">{{ $g['name'] }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;">Year</label>
                    <input wire:model.live="year" type="number" placeholder="{{ date('Y') }}"
                        style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#f9fafb;outline:none;box-sizing:border-box;" />
                </div>

                <div style="display:flex;align-items:flex-end;">
                    <button wire:click="$set('province_id',null);$set('city_id',null);$set('profession_id',null);$set('registertype_id',null);$set('gender',null);$set('search',null);$set('year',null)"
                        style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;background:#fff;color:#6b7280;cursor:pointer;font-weight:600;">
                        &#10005; Clear
                    </button>
                </div>

            </div>
        </div>

        <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">
            Showing <strong style="color:#111827;">{{ $applications->total() }}</strong> practitioner(s)
        </p>

        @if($applications->count())
        <div style="background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.07);border:1px solid #f1f5f9;overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Reg No.</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Name</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Gender</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Profession</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Register Type</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Province</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">City</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Expiry</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Status</th>
                            <th style="padding:12px 16px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $i => $app)
                        <tr style="border-bottom:1px solid #f3f4f6;{{ $i % 2 == 0 ? '' : 'background:#fafafa;' }}">
                            <td style="padding:11px 16px;color:#374151;font-weight:600;">{{ $app->customerprofession?->customer?->regnumber ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;font-weight:600;color:#1f2937;">{{ $app->customerprofession?->customer?->name ?? '' }} {{ $app->customerprofession?->customer?->surname ?? '' }}</td>
                            <td style="padding:11px 16px;color:#6b7280;">{{ $app->customerprofession?->customer?->gender ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;color:#374151;">{{ $app->customerprofession?->profession?->name ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;color:#374151;">{{ $app->customerprofession?->registertype?->name ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;color:#374151;">{{ $app->customerprofession?->customer?->province?->name ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;color:#374151;">{{ $app->customerprofession?->customer?->city?->name ?? 'N/A' }}</td>
                            <td style="padding:11px 16px;color:#374151;">{{ $app->certificate_expiry_date ? $app->certificate_expiry_date->format('d M Y') : 'N/A' }}</td>
                            <td style="padding:11px 16px;">
                                @if($app->isValid())
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:#dcfce7;color:#16a34a;">Valid</span>
                                @else
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:#fee2e2;color:#dc2626;">Invalid</span>
                                @endif
                            </td>
                            <td style="padding:11px 16px;">
                                <button wire:click="viewProfile({{ $app->id }})"
                                    style="padding:5px 12px;border-radius:7px;border:1px solid #e0f4fb;background:#f0faff;color:#5ab4d4;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">
                                    View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top:24px;">{{ $applications->links() }}</div>

        @else
        <div style="text-align:center;padding:70px 0;">
            <div style="font-size:52px;margin-bottom:10px;">&#128100;</div>
            <p style="color:#9ca3af;font-size:15px;font-weight:500;margin:0;">No practitioners found</p>
            <p style="color:#d1d5db;font-size:13px;margin-top:4px;">Try adjusting your filters or search</p>
        </div>
        @endif
    </div>

    {{-- Profile modal --}}
    @if($profilemodal && $selectedapp)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto;">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto;">
            <div style="height:5px;background:linear-gradient(90deg,#7ec8e3,#5ab4d4);"></div>
            <div style="padding:24px;">

                {{-- Header --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:48px;height:48px;border-radius:12px;background:#e0f4fb;display:flex;align-items:center;justify-content:center;font-size:22px;">&#128100;</div>
                        <div>
                            <p style="font-weight:800;font-size:16px;color:#1f2937;margin:0;">{{ $selectedapp->customerprofession?->customer?->name }} {{ $selectedapp->customerprofession?->customer?->surname }}</p>
                            <p style="font-size:12px;color:#9ca3af;margin:2px 0 0;">{{ $selectedapp->customerprofession?->customer?->regnumber }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('profilemodal',false)" style="background:none;border:none;font-size:20px;cursor:pointer;color:#9ca3af;line-height:1;">&#10005;</button>
                </div>

                {{-- Details --}}
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                    @php $row = 'display:flex;gap:10px;padding:8px 0;border-bottom:1px solid #f3f4f6;'; $lbl = 'font-size:12px;color:#9ca3af;width:140px;flex-shrink:0;padding-top:1px;'; $val = 'font-size:13px;font-weight:600;color:#1f2937;'; @endphp

                    <div style="{{ $row }}"><span style="{{ $lbl }}">Gender</span><span style="{{ $val }}">{{ $selectedapp->customerprofession?->customer?->gender ?? 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">Profession</span><span style="{{ $val }}">{{ $selectedapp->customerprofession?->profession?->name ?? 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">Register Type</span><span style="{{ $val }}">{{ $selectedapp->customerprofession?->registertype?->name ?? 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">Province</span><span style="{{ $val }}">{{ $selectedapp->customerprofession?->customer?->province?->name ?? 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">City</span><span style="{{ $val }}">{{ $selectedapp->customerprofession?->customer?->city?->name ?? 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">Certificate Expiry</span><span style="{{ $val }}">{{ $selectedapp->certificate_expiry_date ? $selectedapp->certificate_expiry_date->format('d M Y') : 'N/A' }}</span></div>
                    <div style="{{ $row }}"><span style="{{ $lbl }}">Status</span>
                        <span>
                            @if($selectedapp->isValid())
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:#dcfce7;color:#16a34a;">Valid</span>
                            @else
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:#fee2e2;color:#dc2626;">Invalid</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Qualifications --}}
                @php $quals = $selectedapp->customerprofession?->qualifications ?? collect(); @endphp
                <div style="margin-bottom:16px;">
                    <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin:0 0 8px;">
                        Qualifications @if($quals->count()) <span style="font-weight:400;text-transform:none;font-size:11px;">({{ $quals->count() }})</span> @endif
                    </p>
                    @if($quals->count())
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        @foreach($quals as $q)
                        <div style="background:#f8fafc;border-radius:10px;padding:10px 14px;border:1px solid #e5e7eb;">
                            <div style="font-weight:700;font-size:13px;color:#1f2937;margin-bottom:4px;">
                                &#127891; {{ $q->qualification?->name ?? 'N/A' }}
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                @if($q->qualificationcategory)
                                <span style="font-size:11px;background:#eff6ff;color:#3b82f6;padding:2px 8px;border-radius:20px;font-weight:600;">
                                    {{ $q->qualificationcategory->name }}
                                </span>
                                @endif
                                @if($q->qualificationlevel)
                                <span style="font-size:11px;background:#f0fdf4;color:#16a34a;padding:2px 8px;border-radius:20px;font-weight:600;">
                                    {{ $q->qualificationlevel->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size:13px;color:#9ca3af;margin:0;">No qualifications on record.</p>
                    @endif
                </div>

                <button wire:click="$set('profilemodal',false)"
                    style="width:100%;padding:10px;border-radius:10px;border:none;background:linear-gradient(135deg,#7ec8e3,#5ab4d4);color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>