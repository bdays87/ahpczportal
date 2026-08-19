# SageBridge

A small local HTTP service that sits between the AHPCZ Laravel portal and the
Sage Evolution SDK (`Pastel.Evolution.dll`). Laravel never talks to Sage
directly — it can't; the SDK is a .NET library that needs a live SQL Server
connection to the Evolution company database and a licensed session, both of
which only make sense running in-process on this Windows box. This service
is that process.

```
Laravel (PHP) --HTTP + X-Api-Key--> SageBridge (this project) --SQL--> Evolution company DB
```

## What's implemented vs. what needs your SDK Developer Guide

Everything here was checked by actually compiling against the DLLs you
provided in `../sagedll` (`csc.exe` against `Pastel.Evolution.dll` +
`Pastel.Evolution.Common.dll`), not guessed. That caught several wrong
assumptions along the way — see the comments in the source for specifics.

**Working / compiles cleanly:**
- Connecting: `DatabaseContext.Initialise(...)` + `.SetLicense(...)` (`SageSession.cs`). `DatabaseContext` is a **static class** — one ambient connection per process, confirmed by the compiler.
- Customers: full create/update/lookup via `Customer` (`Handlers/CustomerHandler.cs`).
- The HTTP layer, JSON, auth, logging, routing (`ApiServer.cs`, `Json.cs`, `FileLog.cs`, `Program.cs`).

**Deliberately left as a stub, because guessing further would be irresponsible:**
- `Handlers/InvoiceHandler.cs` → `BuildInvoiceDocument(...)`. `SalesOrder.Type` and `.Reference` have no public setter (confirmed by the compiler), so creating a `DocumentType.Invoice` document isn't as simple as `new SalesOrder { Type = ... }`. Your guide's "create a tax invoice" sample will show the actual supported construction path — copy it in here. Building `OrderDetail` lines from `InvoiceDto.Lines` **is** wired up and compiles.
- `Handlers/ReceiptHandler.cs` → `PostAndAllocate(...)`. `CustomerTransaction` has no public `Save()`, and `AllocationEntry`'s properties are read-only — so receipt posting + allocation goes through some other supported API than what a first pass assumed. Copy your guide's "post a receipt" sample in here.

Both stubs throw `NotImplementedException` with a message pointing back here, so calling `/api/invoices` or `/api/receipts` before you've filled them in fails loudly and immediately instead of doing something wrong to the ledger.

## Configuring

Edit `App.config`:

- `ListenPrefix` — keep this bound to `127.0.0.1` (loopback). Laravel calls in over localhost/LAN; nothing about this service should be internet-facing.
- `ApiKey` — generate a long random value (e.g. `openssl rand -hex 32`). Laravel sends it as `X-Api-Key` on every request.
- `Evolution.*` — the same company/agent/password you'd use to log into Evolution for this company, plus your SDK license serial/key.
- `LogPath` — where the bridge writes its own request/error log.

## Building

You'll need the .NET Framework 4.8 targeting pack (usually already present on a
Windows dev box) and MSBuild — either via Visual Studio, or the standalone
Build Tools. From a Developer Command Prompt:

```
msbuild SageBridge.csproj /p:Configuration=Release
```

`PlatformTarget` is set to `x86` in the `.csproj` — match this to whatever
bitness your Evolution install / the DLLs you were given actually are. If
they're x64, change `<PlatformTarget>` accordingly.

## Running

For development, just run the built exe from a console — it logs to both the
console and `LogPath`, and stays in the foreground:

```
bin\Release\SageBridge.exe
```

`HttpListener` needs either an elevated console the first time, or a URL ACL
reservation so it can bind without elevation:

```
netsh http add urlacl url=http://127.0.0.1:8990/ user=Everyone
```

For production, wrap it as a Windows Service so it survives reboots and
logons — e.g. with [NSSM](https://nssm.cc/):

```
nssm install SageBridge "C:\path\to\SageBridge.exe"
nssm set SageBridge AppDirectory "C:\path\to\SageBridge\folder"
nssm start SageBridge
```

## Smoke test

```
curl -H "X-Api-Key: <your key>" http://127.0.0.1:8990/api/health
```

Should return the connected company name and license expiry. If this fails,
fix the connection before touching the Laravel side at all.

```
curl -X POST http://127.0.0.1:8990/api/customers ^
  -H "X-Api-Key: <your key>" -H "Content-Type: application/json" ^
  -d "{\"code\":\"TEST001\",\"description\":\"Test Customer\",\"email\":\"test@example.com\"}"
```

## Before this touches a real company database

1. Point `Evolution.Database`/company settings at a **copy** of the company database first, not production.
2. Fill in the two `NotImplementedException` stubs using your SDK Developer Guide's sample project.
3. Confirm the `GLAccountCode` you'll send on invoice lines (registration/application fee revenue account) and, for receipts, the correct `TransactionCode` for a Debtors receipt in this company's setup — both are configuration specific to this Evolution company, not something a generic bridge can assume.
4. Post one customer, one invoice, one receipt by hand through this API and check them in the Evolution UI before wiring up automatic pushes from Laravel.
