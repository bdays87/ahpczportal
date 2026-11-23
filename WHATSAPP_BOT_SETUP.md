# WhatsApp bot - setup and what it does

The bot lets practitioners do on WhatsApp what they do on the web: create an account, register for a profession,
renew, pay, follow their applications and download certificates. It uses the portal's own logic (same fees,
documents, rules and approvals), so staff approve things in the portal exactly as before and the practitioner
is told on WhatsApp.

## What practitioners can do

**New to the Council**
1. Send "hi" and tap *New registration*.
2. Answer the personal-details questions (name, date of birth, gender, marital status, nationality, ID, province and
   city, address, place of birth, email, phone). The email is confirmed with a 6-digit code.
3. The portal account is created (login details are emailed, as on the web) and the WhatsApp number is linked.
4. Choose registration type and profession, then employment status, location and sector.
5. Send each required document as a PDF or photo, then add a qualification (institution, qualification, category, level,
   year, certificate).
6. The registration goes to staff for review. When they approve it the bot messages the practitioner, who then pays the
   application fee and waits for the final approval. (If the qualification needs an assessment, that fee comes first.)

**Already registered**
1. Send "hi", tap *I have a reg number*, send the registration number, and reply with the code emailed to the address on file.
2. Menu: My registration, My certificates, Wallet & invoices, Renew certificate, Register a profession, My applications,
   CPD points, Help, Unlink.

**Renewal**: choose the profession and renewal period, then Renew or Maintenance, send the required documents, pay,
and follow it under *My applications*. A rejected application can be corrected and resubmitted from the chat.

**Paying** (renewal and registration): pay from the wallet; pay online (Paynow, when switched on); or pay at the bank using the
invoice number as the reference and send a photo or PDF of the deposit slip, which goes to finance to verify. Bank
payments that carry the invoice or registration number are also matched automatically when the statement is imported.

**Updates**: the bot messages the practitioner when an application or registration is approved or rejected, when a
qualification assessment is approved, and when finance verifies a payment.

Anyone can leave a step by sending *menu*. Progress is saved; they carry on from *applications*.

## Not on WhatsApp yet
* **Student registration** (it needs placement details): the bot sends them to the portal.
* **Practitioners with old paper records who are not in the system** (the web "historical data" capture), institutions and
  other services, CPD activities, profile edits, changing the registration number.
* Paying a settlement that is split across two currencies: the bot points to the portal.

## What you need from Meta (one-time)

1. A **Meta Business account** (business.facebook.com), verified with MLCSCZ's details.
2. A **WhatsApp Business app** in developers.facebook.com (type: Business) with the **WhatsApp** product added.
3. A **phone number** for the bot. It must not be registered on the normal WhatsApp or WhatsApp Business phone app.
   (Meta gives a free test number first, enough to try this with your own phone.)
4. A **permanent access token**: Business Settings > Users > System users > add a system user > Generate token, with the
   `whatsapp_business_messaging` and `whatsapp_business_management` permissions. (The temporary token expires in 24 hours.)
5. Note three values: **Phone number ID** (WhatsApp > API setup), the **token**, and the **App secret**
   (App settings > Basic).

## Settings in the live `.env`

```
WHATSAPP_ENABLED=true
WHATSAPP_TOKEN=<permanent system-user token>
WHATSAPP_PHONE_NUMBER_ID=<phone number id>
WHATSAPP_APP_SECRET=<app secret>
WHATSAPP_VERIFY_TOKEN=<any long random string you make up>
WHATSAPP_API_VERSION=v21.0
WHATSAPP_STATUS_TEMPLATE=<name of an approved template, see below (optional)>
WHATSAPP_DISPLAY_NUMBER=<the number practitioners should message, e.g. +263 77 123 4567 (shown in the public guide)>
```
Then: `php artisan config:clear && php artisan config:cache`.

### Messages outside the 24-hour window
WhatsApp only lets the bot write freely within 24 hours of the practitioner's last message. A status update sent after
that (for example an approval three days later) must use a template Meta has approved.
Create a **utility template** in WhatsApp Manager with a single variable, for example the body
`Update from MLCSCZ: {{1}}`, wait for approval, and put its name in `WHATSAPP_STATUS_TEMPLATE`.
Without it, late updates are not sent (they are logged), and the practitioner still sees the status when they
next message the bot or on the portal.

## Register the webhook in Meta

WhatsApp > Configuration > Webhook:
* **Callback URL:** `https://portal.mlcscz.org.zw/api/whatsapp/webhook`
* **Verify token:** the same `WHATSAPP_VERIFY_TOKEN` string.
* Click Verify and save, then subscribe to the **messages** field.

The portal rejects any webhook call that is not signed by Meta with the app secret.

## Database

Run only this migration (3 new tables, nothing else changes). Nothing new was added to the database for registration,
renewal or payments.

```
php artisan migrate --path=database/migrations/2026_11_21_000001_create_whatsapp_tables.php --force
```

## Files the bot saves

Documents and proofs of payment that practitioners send are saved on the disk the portal already uses
(`FILESYSTEM_DISK`: the public folder locally, the bucket on live), in the same folders as web uploads, as PDF, JPG or PNG up to 10 MB.
The server's upload limits do not apply to these, because the files come from WhatsApp directly.

## Try it

1. In Meta's API setup, add your own phone as a test recipient.
2. Send "hi" to the test number. Try a renewal with a test practitioner first (it creates a real application and invoice).
3. If nothing happens, check `storage/logs/laravel.log` (lines starting "WhatsApp ...") and the Meta webhook
   delivery log (WhatsApp > Configuration).

## Good to know

* The bot acts as the practitioner's own web account, so the portal records who did what. A practitioner with no web login
  cannot renew or pay on WhatsApp (they are told to contact MLCSCZ).
* Practising certificates are only offered while still valid. Registration certificates only when approved.
* Messages typed while waiting for a verification code are not stored.
* The tables `whatsapp_links` (number to practitioner), `whatsapp_sessions` and `whatsapp_messages` hold the state and an
  audit log. To remove a link: delete the row in `whatsapp_links`, or the practitioner sends "unlink".
