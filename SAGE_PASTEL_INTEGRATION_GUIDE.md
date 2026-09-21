# Sage Pastel Integration Guide

## 🎯 Overview

This guide explains how to integrate the AHPCZ Portal with Sage Pastel Evolution using the SageConnector middleware.

## 🏗️ Architecture

```
┌──────────────────┐         ┌──────────────────┐         ┌──────────────────┐
│  AHPCZ Portal    │◄───────►│  SageConnector   │◄───────►│  Sage Evolution  │
│  (Laravel)       │  HTTPS  │  (.NET Service)  │   LAN   │  (Desktop App)   │
│                  │         │  On Client LAN   │         │                  │
└──────────────────┘         └──────────────────┘         └──────────────────┘
```

### Flow:

1. **Portal → Connector (PULL)**
   - Connector periodically calls Portal APIs
   - Fetches pending customers & invoices
   - Creates them in Sage Evolution
   - Sends acknowledgment back to Portal

2. **Connector → Portal (PUSH)**
   - Connector reads invoices from Sage
   - Pushes invoice data to Portal
   - Pushes statement/balance data to Portal

## 📋 Required Portal APIs

The Portal must implement these 5 endpoints:

### 1. GET /api/connector/customers/pending
**Purpose:** Returns practitioners awaiting sync to Sage

### 2. GET /api/connector/orders/pending  
**Purpose:** Returns invoices awaiting creation in Sage

### 3. POST /api/connector/ack
**Purpose:** Receives confirmation of successful/failed sync

### 4. POST /api/connector/invoices
**Purpose:** Receives invoice data from Sage

### 5. POST /api/connector/statements
**Purpose:** Receives customer balance/statement data from Sage

## 🔐 Authentication

All requests carry an `X-Api-Key` header with a secret key.

```
X-Api-Key: your-secret-key-here
```

## 📊 Data Mapping

### Portal → Sage (Customers)

| Portal Field | Sage Field | Source Table |
|--------------|------------|--------------|
| externalId | Customer Code | customers.id or customers.uuid |
| name | Customer Name | customers.name + surname |
| email | Email | customers.email |
| phone | Phone | customers.phone |
| physicalAddress | Physical Address | customers.address |

### Portal → Sage (Orders/Invoices)

| Portal Field | Sage Field | Source Table |
|--------------|------------|--------------|
| externalId | Reference | invoices.invoice_number |
| customerExternalId | Customer Code | customers.id or uuid |
| orderDate | Invoice Date | invoices.created_at |
| reference | Description | invoices.description |
| lines | Invoice Lines | (constructed from invoice data) |

### Sage → Portal (Invoices)

| Sage Field | Portal Field | Target Table |
|------------|--------------|--------------|
| invoiceNumber | Invoice Number | invoices.sage_invoice_number |
| customerCode | Customer Code | Link to customers |
| amountOutstanding | Outstanding | invoices.sage_outstanding |
| status | Status | invoices.sage_status |

### Sage → Portal (Statements)

| Sage Field | Portal Field | Target Table |
|------------|--------------|--------------|
| customerCode | Customer Code | Link to customers |
| currentBalance | Current Balance | customer_statements.balance |
| current/30/60/90 | Aging buckets | customer_statements.* |

## 🚀 Implementation Steps

1. ✅ Create API routes
2. ✅ Create Controllers
3. ✅ Create Models (if needed)
4. ✅ Add database migrations for tracking
5. ✅ Implement authentication middleware
6. ✅ Test with SageConnector

## 📝 Notes

- **Idempotency:** Same record can be pulled multiple times - use externalId to de-duplicate
- **Error Handling:** Capture ack failures and display to admins
- **Monitoring:** Log all sync operations for audit trail
- **Security:** Store X-Api-Key in .env, never hardcode

## 🔄 Sync Status Tracking

Add a `sync_status` field to relevant tables:
- `PENDING` - Awaiting sync to Sage
- `SYNCED` - Successfully synced to Sage
- `FAILED` - Sync failed (check error message)
- `SYNCING` - Currently being synced

## 📚 Reference

See `PORTAL_API_CONTRACT.md` in sageconnector project for complete API specification.
