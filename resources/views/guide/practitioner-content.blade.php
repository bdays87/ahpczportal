{{-- Practitioner guide text. Used by the public page (/guide) and by the PDF download, so they always match.
     Styling is embedded so it does not depend on the site's compiled CSS. Variables: $forPdf (bool) --}}
@php
    $council = config('app.title');
    $short = config('app.name');
    $portal = rtrim(config('generalutils.base_url'), '/');
    $helpEmail = config('app.email');
    $whatsappOn = (bool) config('services.whatsapp.enabled');
    $whatsappNumber = config('services.whatsapp.display_number');
@endphp
<style>
    .guide { font-family: {{ ($forPdf ?? false) ? 'Helvetica, Arial, sans-serif' : 'inherit' }}; color: #0f172a; line-height: 1.6; font-size: {{ ($forPdf ?? false) ? '11px' : '16px' }}; }
    .guide h1 { font-size: 26px; margin: 0 0 6px; color: #0f172a; }
    .guide h2 { font-size: 20px; margin: 30px 0 8px; padding-bottom: 6px; border-bottom: 2px solid #0ea5e9; color: #0f172a; }
    .guide h3 { font-size: 16px; margin: 20px 0 6px; color: #0369a1; }
    .guide p { margin: 8px 0; }
    .guide ul, .guide ol { margin: 8px 0 8px 22px; padding: 0; }
    .guide li { margin: 4px 0; }
    .guide .note { border-left: 4px solid #0ea5e9; background: #f0f9ff; padding: 10px 14px; margin: 14px 0; }
    .guide .warn { border-left: 4px solid #f59e0b; background: #fffbeb; padding: 10px 14px; margin: 14px 0; }
    .guide table { border-collapse: collapse; width: 100%; margin: 12px 0; }
    .guide th, .guide td { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; vertical-align: top; }
    .guide th { background: #f1f5f9; }
    .guide .toc { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 18px; margin: 16px 0 6px; }
    .guide .toc ol { margin: 6px 0 0 18px; }
    .guide a { color: #0369a1; }
    .guide .sub { color: #475569; }
</style>

<div class="guide">
    <h1>Practitioner Guide</h1>
    <p class="sub">{{ $council }} ({{ $short }}) online portal: how to create an account, register, renew, pay and download your certificates.</p>
    <p class="sub">Portal address: <strong>{{ $portal }}</strong></p>

    <div class="toc">
        <strong>In this guide</strong>
        <ol>
            <li><a href="#before">Before you start</a></li>
            <li><a href="#account">Create your account and sign in</a></li>
            <li><a href="#first">Your first sign-in</a></li>
            <li><a href="#register">Register for a profession</a></li>
            <li><a href="#renew">Renew or maintain your registration</a></li>
            <li><a href="#pay">Pay your invoices</a></li>
            <li><a href="#certificates">Download and verify certificates</a></li>
            <li><a href="#cpd">CPD points and other services</a></li>
            @if ($whatsappOn)
                <li><a href="#whatsapp">Using WhatsApp instead</a></li>
            @endif
            <li><a href="#help">Questions and problems</a></li>
        </ol>
    </div>

    <h2 id="before">1. Before you start</h2>
    <p>You need:</p>
    <ul>
        <li>A phone, tablet or computer with internet and a recent browser (Chrome, Edge, Firefox or Safari).</li>
        <li>An <strong>email address you can open</strong>. Notices and codes are sent to it.</li>
        <li>Your <strong>national ID or passport number</strong>. If you were registered before, your <strong>registration number</strong> too.</li>
        <li>Your documents as clear scans or photos. PDF is best; JPG and PNG also work. Keep each file under <strong>10 MB</strong>.</li>
    </ul>
    <div class="note">The documents you must upload depend on your profession and register. The portal lists exactly what is needed on screen, so you do not need to guess.</div>

    <h2 id="account">2. Create your account and sign in</h2>
    <p>Open <strong>{{ $portal }}</strong> and choose <strong>Register</strong>.</p>

    <h3>If you are already registered with the Council</h3>
    <ol>
        <li>Type your <strong>ID number or your registration number</strong> and tap Search.</li>
        <li>If we find your record, it shows your name. Enter your email, a phone number and a password (at least 8 characters, typed twice).</li>
        <li>Create the account, then sign in. Your existing record is joined to your new login.</li>
    </ol>
    <p>If the portal says the record <em>already has a login</em>, you have an account. Use <strong>Login</strong> instead.</p>

    <h3>If you are new</h3>
    <ol>
        <li>If your ID or registration number is not found, the form asks for your full details.</li>
        <li>Enter your first name, surname, email, phone number and a password (at least 8 characters).</li>
        <li>Create the account, then sign in.</li>
    </ol>

    <h3>Signing in</h3>
    <p>Choose <strong>Login</strong>. In the first box type your <strong>registration number</strong> (for example 254564) or your email address, then type your password. Either one works. If you forget your password, choose the forgotten-password link on the login page.</p>
    <div class="note">After five wrong passwords in a row you must wait a minute before trying again. This protects your account.</div>

    <h2 id="first">3. Your first sign-in</h2>
    <p>The first time you sign in, a <strong>Customer Registration</strong> window asks a few questions so the Council can set up your record.</p>
    <ol>
        <li><strong>"Do you have a valid registration certificate?"</strong>
            <ul>
                <li><strong>Yes</strong>: enter your National ID. If your record is found you confirm your details. If it is not found (for example you were registered on paper), you enter your past registration details (registration number, year, practising certificate number, register, last renewal) and upload your registration and practising certificates. The Council checks them and approves them. You are told when it is done.</li>
                <li><strong>No</strong>: fill in your personal details (name, date of birth, gender, marital status, ID, nationality, address, place of birth). A new registration number is created for you.</li>
            </ul>
        </li>
        <li>When this is done you see your <strong>Dashboard</strong>.</li>
    </ol>
    <div class="note">Your registration number is also your customer number for accounts and receipts. Keep it safe.</div>

    <h2 id="register">4. Register for a profession</h2>
    <p>On your Dashboard, add a profession and follow the steps in order. Your progress is saved, so you can stop and continue later.</p>
    <ol>
        <li><strong>Add the profession.</strong> Choose your type (practitioner, intern, student), the profession, the register, your employment status, where you work and whether it is the public or private sector.</li>
        <li><strong>Upload the required documents.</strong> The list is shown. Upload each one.</li>
        <li><strong>Add your qualification.</strong> Choose the institution and qualification, where it was obtained, the level and the year, and upload the certificate. Qualifications obtained abroad may need an assessment, and then an assessment fee is shown first.</li>
        <li><strong>Council review.</strong> Your documents and qualifications go to the Council. You are told when your registration is approved.</li>
        <li><strong>Pay the application fee</strong> once your registration is approved (see section 6).</li>
        <li><strong>Final approval.</strong> When the application is approved you can download your certificates (section 7).</li>
    </ol>
    <div class="warn">If the Council rejects a submission it tells you why. Correct the documents, replace the wrong ones and resubmit. You do not have to start again.</div>
    <p>Students register under the student option and also enter placement details.</p>

    <h2 id="renew">5. Renew or maintain your registration</h2>
    <p>Renewal is only possible while the <strong>renewal period</strong> is open. If it is closed, the portal says so.</p>
    <ol>
        <li>On your Dashboard, open your profession and choose <strong>Renew</strong>.</li>
        <li>Choose the renewal period (the year) and the type:
            <ul>
                <li><strong>Renew</strong>: to get a valid practising certificate.</li>
                <li><strong>Maintain</strong>: to stay on the maintenance register.</li>
            </ul>
        </li>
        <li>You need enough <strong>CPD points</strong>. If you do not have enough, the portal tells you your points and the minimum needed. Add activities (section 8) and try again.</li>
        <li>Upload the renewal documents that are listed.</li>
        <li>Pay the renewal invoice (section 6). The invoice shows the fee, and any late renewal charge or discount that applies to you.</li>
        <li>When the Council approves, download your new practising certificate.</li>
    </ol>
    <p>If a renewal is rejected, the reason is shown. Fix the documents and choose <strong>Resubmit</strong>.</p>

    <h2 id="pay">6. Pay your invoices</h2>
    <p>Each step that has a fee creates an <strong>invoice</strong> with its own number. You can pay in three ways:</p>
    <table>
        <tr><th>How</th><th>What to do</th></tr>
        <tr><td><strong>From your wallet</strong></td><td>If your wallet balance covers the invoice, choose to pay from the wallet. The invoice is settled straight away.</td></tr>
        <tr><td><strong>Online</strong> (when switched on)</td><td>Top up your wallet through the online payment option, then pay the invoice from the wallet.</td></tr>
        <tr><td><strong>At the bank</strong></td><td>Pay into the Council's bank account shown on the screen. Use the <strong>invoice number</strong> (or your registration number) as the payment reference. Then upload a photo or PDF of your deposit slip and submit it for verification. Finance checks it and approves it. Deposits that carry your invoice or registration number are also matched when the Council imports the bank statement.</td></tr>
    </table>
    <div class="warn">Always put the invoice number or your registration number in the bank reference. Without it, finance cannot tell who paid.</div>
    <p>Your <strong>Statements</strong>, <strong>Online Payments</strong> and <strong>Manual Payments</strong> pages show what you have paid and your wallet balance.</p>

    <h2 id="certificates">7. Download and verify certificates</h2>
    <ul>
        <li><strong>Registration certificate</strong>: available once your registration is approved.</li>
        <li><strong>Practising certificate</strong>: available once your renewal (or application) is approved. It shows its expiry date.</li>
    </ul>
    <p>To download, open your profession on the Dashboard and choose <strong>Certificates</strong>, then <strong>Download</strong> next to the certificate you need.</p>
    <p><strong>Anyone</strong> can check that a certificate is genuine without signing in: open <strong>{{ $portal }}/verifycertificate</strong> and enter the certificate number, or scan the QR code printed on the certificate.</p>

    <h2 id="cpd">8. CPD points and other services</h2>
    <p>The menu on the left of your Dashboard gives you:</p>
    <table>
        <tr><th>Menu</th><th>What it is for</th></tr>
        <tr><td>Dashboard</td><td>Your professions, registration status, CPD points and certificates.</td></tr>
        <tr><td>Statements</td><td>Your account statement: invoices and payments.</td></tr>
        <tr><td>Online Payments</td><td>Your online payment history and wallet top-ups.</td></tr>
        <tr><td>Manual Payments</td><td>Payments made at the bank and verified by finance.</td></tr>
        <tr><td>My Activities</td><td>Learning activities and quizzes that earn CPD points.</td></tr>
        <tr><td>Elections &amp; Voting</td><td>Council elections when they are open.</td></tr>
        <tr><td>Journals, Newsletters, Resources</td><td>Council publications and useful documents.</td></tr>
    </table>
    <p>Your CPD points count towards the minimum you need to renew. Keep an eye on them during the year so you are ready when the renewal period opens.</p>

    @if ($whatsappOn)
        <h2 id="whatsapp">9. Using WhatsApp instead</h2>
        <p>You can do most of this in a WhatsApp chat with the Council{{ $whatsappNumber ? ' on '.$whatsappNumber : '' }}. Send <strong>hi</strong> to start.</p>
        <ul>
            <li><strong>New here?</strong> Tap <em>New registration</em>. The assistant asks for your details, confirms your email with a code, creates your account, and takes you through the profession, documents and qualification.</li>
            <li><strong>Already registered?</strong> Tap <em>I have a reg number</em>, send your registration number and reply with the code emailed to you.</li>
            <li>Then use the menu: registration status, certificates (sent to you as PDF), wallet and invoices, renew, register a profession, your applications and CPD points.</li>
            <li>Send your documents and deposit slips as a PDF or photo in the chat.</li>
            <li>You are messaged when something is approved or rejected.</li>
        </ul>
        <p>At any time send <strong>menu</strong> to go back. Your progress is saved. Student registration and a few other services are only on the portal.</p>
    @endif

    <h2 id="help">{{ $whatsappOn ? '10' : '9' }}. Questions and problems</h2>
    <table>
        <tr><th>Problem</th><th>What to do</th></tr>
        <tr><td>"This record already has a login"</td><td>You already have an account. Use <strong>Login</strong>. If you forgot the password, use the reset link.</td></tr>
        <tr><td>I did not get the email</td><td>Check your spam or junk folder. Make sure the address was typed correctly. If it still does not arrive, contact us.</td></tr>
        <tr><td>My registration number was not found</td><td>Check it for typing mistakes, or search with your ID number instead. If you were registered before the portal, choose "Yes" at the registration question and give your past details.</td></tr>
        <tr><td>"Renewal is not open"</td><td>The renewal period has not started or has ended. Try again when it opens.</td></tr>
        <tr><td>Not enough CPD points to renew</td><td>The message shows your points and the minimum. Add CPD activities and try again.</td></tr>
        <tr><td>The file will not upload</td><td>Use a PDF, JPG or PNG under 10 MB. Scan or photograph the page clearly and try again.</td></tr>
        <tr><td>I paid but my invoice is still unpaid</td><td>Bank payments are checked by finance. Make sure you uploaded the deposit slip and submitted it for verification, and that the bank reference had your invoice number.</td></tr>
        <tr><td>My application was rejected</td><td>Open it, read the reason, replace the documents that were wrong and resubmit.</td></tr>
        <tr><td>My details or name are wrong on my record</td><td>Contact the Council. Staff can correct them.</td></tr>
    </table>
    <p><strong>Contact:</strong> {{ $helpEmail }}. Please include your registration number and, if you can, a screenshot of the problem.</p>
    <p class="sub">Guide generated from {{ $portal }}/guide.</p>
</div>
