# WhatsApp bot - setup

Phase 1 is built: practitioners link their WhatsApp number once, then can check registration status,
download certificates as PDF, see wallet balance and unpaid invoices, and see CPD points.
Renewal and new registration are not on WhatsApp yet (the bot points people to the portal).

## How a practitioner uses it

1. Sends "hi" to the MLCSCZ WhatsApp number.
2. Sends their registration number (the same number used as the Sage customer code).
3. Receives a 6-digit code **by email** (the address on their record) and replies with it. Codes expire after 10 minutes,
   5 wrong tries stops the attempt, and each phone or record can ask for at most 5 codes an hour.
4. From then on the menu is: My registration, My certificates, Wallet & invoices, CPD points, Help, Unlink.

A phone number alone is never trusted. Certificates are only sent to a number that passed the email-code check, and only
for the practitioner it is linked to.

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
```
Then: `php artisan config:clear && php artisan config:cache`.

## Register the webhook in Meta

WhatsApp > Configuration > Webhook:
* **Callback URL:** `https://portal.mlcscz.org.zw/api/whatsapp/webhook`
* **Verify token:** the same `WHATSAPP_VERIFY_TOKEN` string.
* Click Verify and save, then subscribe to the **messages** field.

The portal rejects any webhook call that is not signed by Meta with the app secret.

## Database

Run only this migration (it creates 3 new tables and changes nothing else):

```
php artisan migrate --path=database/migrations/2026_11_21_000001_create_whatsapp_tables.php --force
```

## Try it

1. In Meta's API setup, add your own phone as a test recipient.
2. Send "hi" to the test number from that phone and follow the steps above.
3. If nothing happens, check `storage/logs/laravel.log` (lines starting "WhatsApp ...") and the Meta webhook
   delivery log (WhatsApp > Configuration).

## Good to know

* **Replies and the 24-hour window:** the bot only ever answers a message the practitioner just sent, which Meta allows
  freely. Messages the bot starts itself (for example renewal reminders) need a template approved by Meta first. Not built yet.
* **Email must be on the record** for linking to work. Practitioners without one are told to contact MLCSCZ.
* Practising certificates are only offered while still valid. Registration certificates only when approved.
* Messages the user types while the bot waits for their verification code are not stored.
* The tables `whatsapp_links` (number to practitioner), `whatsapp_sessions` and `whatsapp_messages` hold the state and an
  audit log of messages. To remove someone's link: delete their row in `whatsapp_links`, or they send "unlink".
