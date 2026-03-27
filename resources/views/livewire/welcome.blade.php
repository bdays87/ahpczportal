<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#fff;">

    {{--  HERO  --}}
    <section style="background:linear-gradient(135deg,#0ea5e9 0%,#7ec8e3 50%,#38bdf8 100%);min-height:88vh;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;padding:80px 24px 60px;position:relative;overflow:hidden;">

        {{-- decorative circles --}}
        <div style="position:absolute;top:-80px;right:-80px;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,.07);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-100px;left:-60px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

        <div style="position:relative;max-width:760px;margin:0 auto;">
            <img src="{{ asset(config('app.logo')) }}" alt="Logo" style="height:80px;width:auto;margin:0 auto 24px;display:block;filter:drop-shadow(0 4px 12px rgba(0,0,0,.2));">
            <div style="display:inline-block;background:rgba(255,255,255,.2);color:#fff;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:6px 16px;border-radius:50px;margin-bottom:20px;border:1px solid rgba(255,255,255,.3);">
                Official Portal
            </div>
            <h1 style="color:#fff;font-size:clamp(28px,5vw,52px);font-weight:900;line-height:1.15;margin:0 0 16px;letter-spacing:-1px;">
                Welcome to {{ config('app.name') }}
            </h1>
            <p style="color:rgba(255,255,255,.88);font-size:clamp(15px,2vw,19px);line-height:1.6;margin:0 0 36px;max-width:580px;margin-left:auto;margin-right:auto;">
                {{ config('app.title') }}
            </p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('register') }}" style="background:#fff;color:#0ea5e9;font-weight:700;font-size:15px;padding:14px 32px;border-radius:50px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,.15);transition:all .2s;">
                    Get Started &#8594;
                </a>
                <a href="{{ route('login') }}" style="background:rgba(255,255,255,.15);color:#fff;font-weight:700;font-size:15px;padding:14px 32px;border-radius:50px;text-decoration:none;border:2px solid rgba(255,255,255,.5);">
                    Sign In
                </a>
            </div>
        </div>

        {{-- wave --}}
        <div style="position:absolute;bottom:0;left:0;right:0;line-height:0;">
            <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:60px;display:block;">
                <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#ffffff"/>
            </svg>
        </div>
    </section>

    {{--  STATS  --}}
    <section style="background:#fff;padding:48px 24px;">
        <div style="max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:24px;text-align:center;">
            <div style="padding:24px;border-radius:16px;background:#f0f9ff;border:1px solid #e0f2fe;">
                <div style="font-size:36px;font-weight:900;color:#0ea5e9;margin-bottom:4px;">&#128100;</div>
                <div style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Registered Practitioners</div>
            </div>
            <div style="padding:24px;border-radius:16px;background:#f0fdf4;border:1px solid #dcfce7;">
                <div style="font-size:36px;font-weight:900;color:#16a34a;margin-bottom:4px;">&#127963;</div>
                <div style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Accredited Institutions</div>
            </div>
            <div style="padding:24px;border-radius:16px;background:#fefce8;border:1px solid #fef08a;">
                <div style="font-size:36px;font-weight:900;color:#ca8a04;margin-bottom:4px;">&#127941;</div>
                <div style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Professions Regulated</div>
            </div>
            <div style="padding:24px;border-radius:16px;background:#fdf4ff;border:1px solid #f3e8ff;">
                <div style="font-size:36px;font-weight:900;color:#9333ea;margin-bottom:4px;">&#128203;</div>
                <div style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Certificates Issued</div>
            </div>
        </div>
    </section>

    {{--  QUICK ACTIONS  --}}
    <section style="background:#f8fafc;padding:60px 24px;">
        <div style="max-width:960px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:40px;">
                <h2 style="font-size:clamp(22px,3vw,34px);font-weight:800;color:#0f172a;margin:0 0 10px;">Need Professional Assistance?</h2>
                <p style="color:#64748b;font-size:16px;margin:0;">Find registered practitioners and accredited institutions near you.</p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;">

                <div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #e2e8f0;text-align:center;">
                    <div style="width:60px;height:60px;border-radius:16px;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;">&#128100;</div>
                    <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 10px;">Find Practitioners</h3>
                    <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0 0 20px;">Search our directory of certified and compliant practitioners by profession, city and more.</p>
                    <a href="{{ route('practitionerlist.index') }}" style="display:inline-block;background:#16a34a;color:#fff;font-weight:700;font-size:14px;padding:11px 24px;border-radius:50px;text-decoration:none;">
                        Browse Practitioners &#8594;
                    </a>
                </div>

                <div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #e2e8f0;text-align:center;">
                    <div style="width:60px;height:60px;border-radius:16px;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;">&#127963;</div>
                    <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 10px;">Find Institutions</h3>
                    <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0 0 20px;">Discover accredited health institutions offering services by registered practitioners.</p>
                    <a href="{{ route('registeredinstitutions.index') }}" style="display:inline-block;background:#0ea5e9;color:#fff;font-weight:700;font-size:14px;padding:11px 24px;border-radius:50px;text-decoration:none;">
                        Browse Institutions &#8594;
                    </a>
                </div>

                <div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #e2e8f0;text-align:center;">
                    <div style="width:60px;height:60px;border-radius:16px;background:#fef9c3;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;">&#128196;</div>
                    <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 10px;">Verify Certificate</h3>
                    <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0 0 20px;">Instantly verify the authenticity of any certificate issued by the council.</p>
                    <a href="{{ route('certificateverification.index') }}" style="display:inline-block;background:#ca8a04;color:#fff;font-weight:700;font-size:14px;padding:11px 24px;border-radius:50px;text-decoration:none;">
                        Verify Now &#8594;
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{--  REGISTRATION STEPS  --}}
    <section style="background:#fff;padding:60px 24px;">
        <div style="max-width:960px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:40px;">
                <h2 style="font-size:clamp(22px,3vw,34px);font-weight:800;color:#0f172a;margin:0 0 10px;">Registration Process</h2>
                <p style="color:#64748b;font-size:16px;margin:0;">Five simple steps to become a certified practitioner</p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;">
                @foreach([
                    ['1','Account Creation','Register and create your personal account','#0ea5e9'],
                    ['2','Profession Selection','Choose your specific laboratory profession','#16a34a'],
                    ['3','Document Upload','Submit required certificates and identification','#f59e0b'],
                    ['4','Add Qualifications','Add your profession related qualifications','#8b5cf6'],
                    ['5','Payment','Complete registration fee payment','#ef4444'],
                ] as $step)
                <div style="background:#f8fafc;border-radius:16px;padding:24px 16px;text-align:center;border:1px solid #e2e8f0;">
                    <div style="width:44px;height:44px;border-radius:50%;background:{{ $step[3] }};color:#fff;font-size:18px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">{{ $step[0] }}</div>
                    <p style="font-weight:700;font-size:14px;color:#0f172a;margin:0 0 6px;">{{ $step[1] }}</p>
                    <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
            <div style="text-align:center;margin-top:32px;">
                <a href="{{ route('register') }}" style="display:inline-block;background:linear-gradient(135deg,#0ea5e9,#7ec8e3);color:#fff;font-weight:700;font-size:15px;padding:14px 36px;border-radius:50px;text-decoration:none;box-shadow:0 4px 16px rgba(14,165,233,.3);">
                    Start Registration &#8594;
                </a>
            </div>
        </div>
    </section>

    {{--  BANKING DETAILS  --}}
    <section style="background:#f8fafc;padding:40px 24px;">
        <div style="max-width:960px;margin:0 auto;">
            <livewire:bankdetails />
        </div>
    </section>

    {{--  VERIFY CERTIFICATE  --}}
    <section style="background:#fff;padding:40px 24px;">
        <div style="max-width:960px;margin:0 auto;">
            <livewire:components.verifycertificate />
        </div>
    </section>

    {{--  FOOTER  --}}
    <footer style="background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;padding:56px 24px 32px;">
        <div style="max-width:960px;margin:0 auto;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:32px;margin-bottom:40px;">
                <div>
                    <img src="{{ asset(config('app.logo')) }}" alt="Logo" style="height:48px;width:auto;margin-bottom:12px;filter:brightness(0) invert(1);">
                    <p style="font-size:13px;color:rgba(255,255,255,.75);line-height:1.6;margin:0;">{{ config('app.vision') }}</p>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;margin:0 0 14px;text-transform:uppercase;letter-spacing:.5px;">Contact Us</p>
                    <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;color:rgba(255,255,255,.8);">
                        <span>&#128231; {{ config('app.email') }}</span>
                        <span>&#128222; {{ config('app.phone') }}</span>
                        <span>&#128205; {{ config('app.address') }}</span>
                    </div>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;margin:0 0 14px;text-transform:uppercase;letter-spacing:.5px;">Quick Links</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <a href="{{ route('practitionerlist.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;">Practitioners</a>
                        <a href="{{ route('registeredinstitutions.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;">Institutions</a>
                        <a href="{{ route('certificateverification.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;">Verify Certificate</a>
                        <a href="https://www.mohcc.gov.zw/" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;">MOHCC</a>
                        <a href="https://hpa.co.zw/" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;">HPA</a>
                    </div>
                </div>
            </div>
            <div style="border-top:1px solid rgba(255,255,255,.2);padding-top:24px;text-align:center;font-size:13px;color:rgba(255,255,255,.6);">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

</div>