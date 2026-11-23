<x-layouts.plain title="Practitioner Guide">
    <div style="max-width: 900px; margin: 0 auto; padding: 24px 16px 60px;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-bottom: 10px;">
            <a href="{{ route('welcome') }}" style="font-size: 14px; color: #0369a1; text-decoration: none;">&larr; Back to the portal</a>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('guide.download') }}" style="display:inline-block; background:#0ea5e9; color:#fff; font-weight:700; font-size:14px; padding:10px 18px; border-radius:8px; text-decoration:none;">
                    Download as PDF
                </a>
                <a href="{{ route('register') }}" style="display:inline-block; background:#0f172a; color:#fff; font-weight:700; font-size:14px; padding:10px 18px; border-radius:8px; text-decoration:none;">Create an account</a>
                <a href="{{ route('login') }}" style="display:inline-block; border:1px solid #0f172a; color:#0f172a; font-weight:700; font-size:14px; padding:9px 18px; border-radius:8px; text-decoration:none;">Login</a>
            </div>
        </div>

        @include('guide.practitioner-content', ['forPdf' => false])
    </div>
</x-layouts.plain>
