# Migrating the live database safely

Do NOT run a bare `php artisan migrate` on live. Run only the migrations below, one at a time,
in this order. Each command touches only its own file.

Back up the database first (cPanel > phpMyAdmin > Export, or `mysqldump`).

To preview the SQL of any step without changing anything, add `--pretend`:
`php artisan migrate --path=database/migrations/<file>.php --pretend`

| # | File | What it does |
|---|------|--------------|
| 1 | `2026_10_29_000001_add_renewal_window_to_applicationsessions_table.php` | Renewal window columns on sessions |
| 2 | `2026_10_29_000002_change_renewal_window_to_dates_on_applicationsessions.php` | Changes them to dates |
| 3 | `2026_11_16_000001_create_practitionermachines_table.php` | NEW table `practitionermachines` |
| 4 | `2026_11_16_000002_add_machines_submodule.php` | Adds the "Machines" menu row |
| 5 | `2026_11_16_000003_create_accreditations_tables.php` | NEW tables `accreditations`, `accreditationlevels`; adds 2 nullable columns to `otherapplicationinstaccreditations`; adds "Accreditations" menu row |
| 6 | `2026_11_16_000004_create_invoice_sequences_table.php` | NEW table `invoice_sequences` (counter for invoice numbers) |
| 7 | `2026_11_21_000001_create_whatsapp_tables.php` | NEW tables `whatsapp_links`, `whatsapp_sessions`, `whatsapp_messages` (WhatsApp bot) |

Skip any step whose table/columns already exist on live (check with `php artisan migrate:status`;
steps 1-2 may already have been run).

```
php artisan migrate --path=database/migrations/2026_10_29_000001_add_renewal_window_to_applicationsessions_table.php --force
php artisan migrate --path=database/migrations/2026_10_29_000002_change_renewal_window_to_dates_on_applicationsessions.php --force
php artisan migrate --path=database/migrations/2026_11_16_000001_create_practitionermachines_table.php --force
php artisan migrate --path=database/migrations/2026_11_16_000002_add_machines_submodule.php --force
php artisan migrate --path=database/migrations/2026_11_16_000003_create_accreditations_tables.php --force
php artisan migrate --path=database/migrations/2026_11_16_000004_create_invoice_sequences_table.php --force
php artisan migrate --path=database/migrations/2026_11_21_000001_create_whatsapp_tables.php --force
```

Then:
```
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan view:clear
```

## Invoice numbers (separate decision, changes existing data)

Only after step 6, and only when you are ready to renumber every invoice to `INV20261114` + 1, 2, 3...

```
php artisan invoices:renumber --dry-run     # preview, changes nothing
php artisan invoices:renumber               # applies; saves old->new CSV in storage/logs
```

Until you run it, new invoices already get `INV20261114` + the next number from the counter
(the counter starts at 1 and will collide in meaning with old numbers, so run the renumber soon
after step 6).

## Sage connector settings in the live `.env`

```
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=<same value as Portal:ApiKey in the connector's appsettings.Production.json>
```
