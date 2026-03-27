<div style="padding:56px 0;">
    <div style="text-align:center;margin-bottom:36px;">
        <div style="display:inline-block;background:#eff6ff;color:#0ea5e9;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:5px 14px;border-radius:50px;margin-bottom:12px;">Payments</div>
        <h2 style="font-size:clamp(22px,3vw,32px);font-weight:800;color:#0f172a;margin:0 0 8px;">Banking Details</h2>
        <p style="color:#64748b;font-size:15px;margin:0;">Use the following bank accounts for payments and registration fees</p>
    </div>

    @if($banks->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px;">
        @foreach($banks as $bank)
        <div style="background:#fff;border-radius:18px;padding:24px;box-shadow:0 2px 16px rgba(0,0,0,.07);border:1px solid #e2e8f0;position:relative;overflow:hidden;">
            {{-- top accent --}}
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#0ea5e9,#7ec8e3);"></div>

            {{-- bank header --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-top:4px;">
                <div style="width:40px;height:40px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">&#127981;</div>
                <div>
                    <p style="font-weight:800;font-size:14px;color:#0f172a;margin:0;line-height:1.3;">{{ $bank->bank->name }}</p>
                    <span style="font-size:11px;background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $bank->currency->name ?? '' }}</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;border-top:1px solid #f1f5f9;padding-top:14px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;flex-shrink:0;">Account Name</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;text-align:right;">{{ $bank->bank->account_name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">Account No.</span>
                    <span style="font-size:13px;font-weight:800;color:#0ea5e9;font-family:monospace;">{{ $bank->account_number }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">Branch</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;">{{ $bank->branch_name }}</span>
                </div>
                @if($bank->branch_code)
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">Branch Code</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;">{{ $bank->branch_code }}</span>
                </div>
                @endif
                @if($bank->swift_code && $bank->swift_code !== 'N/A')
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">Swift Code</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;">{{ $bank->swift_code }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:40px 0;color:#94a3b8;font-size:14px;">No banking details available.</div>
    @endif
</div>