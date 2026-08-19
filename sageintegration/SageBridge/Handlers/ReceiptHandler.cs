using System;
using System.Collections.Generic;
using Pastel.Evolution;
using Pastel.Evolution.Common.Exception;
using SageBridge.Models;

namespace SageBridge.Handlers
{
    /// <summary>
    /// Posts a receipt and allocates it against the matching invoice.
    ///
    /// This is the one part of the bridge you should NOT trust blind — verify
    /// every step against your SDK Developer Guide's "posting a receipt" sample
    /// before running this against a real company database, and test against a
    /// COPY of the company database first.
    ///
    /// What's confirmed by compiling against your actual DLL:
    ///  - CustomerBatch + BatchDetail exist and BatchDetail takes Customer,
    ///    Date, Reference, Description, AmountInclusive, IsDebit via object
    ///    initializer — that part compiles.
    ///  - CustomerTransaction has NO public Save() method, so a receipt's
    ///    ledger transaction is not persisted by calling .Save() on it directly
    ///    — it must come from processing the batch (batch.Save() /
    ///    a dedicated post/process call) and the resulting transaction has to be
    ///    looked up afterwards, not constructed by hand.
    ///  - AllocationEntry.Transaction / .BalancingTransaction / .Amount / .Date
    ///    are READ-ONLY — allocation is not "new it up and set properties",
    ///    there's a supported allocation API (likely a method on
    ///    AllocationCollection or CustomerTransaction) that your guide's sample
    ///    demonstrates.
    /// None of this is safe to guess further from reflection — copy the exact
    /// sequence from your guide's receipt-posting sample into
    /// <see cref="PostAndAllocate"/> below.
    /// </summary>
    internal static class ReceiptHandler
    {
        public static object Create(ReceiptDto dto)
        {
            if (string.IsNullOrWhiteSpace(dto.CustomerCode))
            {
                throw new ApiException(400, "CustomerCode is required.");
            }

            if (string.IsNullOrWhiteSpace(dto.InvoiceReference))
            {
                throw new ApiException(400, "InvoiceReference is required.");
            }

            lock (SageSession.Lock)
            {
                SageSession.EnsureInitialised();

                Customer customer;
                try
                {
                    customer = new Customer(dto.CustomerCode);
                }
                // RecordNotExistsException itself is internal to the SDK (confirmed by compiling

                // against the real DLL), so we catch its public base instead. That means this

                // also catches other Evolution errors (e.g. a dropped connection) as if the

                // record were simply missing. If you need to tell those apart, check

                // ex.GetType().Name == "RecordNotExistsException" inside the catch block.

                catch (Pastel.Evolution.EvolutionException)
                {
                    throw new ApiException(404, "Unknown CustomerCode.");
                }

                PostAndAllocate(customer, dto);

                FileLog.Info(string.Format(
                    "Posted receipt {0} for customer {1}, allocated {2} against invoice {3}",
                    dto.Reference, customer.Code, dto.Amount, dto.InvoiceReference));

                return new Dictionary<string, object>
                {
                    { "reference", dto.Reference },
                    { "allocatedAmount", dto.Amount },
                };
            }
        }

        private static void PostAndAllocate(Customer customer, ReceiptDto dto)
        {
            throw new NotImplementedException(
                "Copy the receipt-posting + allocation sequence from your SDK Developer Guide sample here — see the " +
                "class remarks above for exactly what was and wasn't confirmed to compile against your DLL.");

            // Reference for whoever fills this in — the batch/line shape that DID
            // compile against the real SDK:
            //
            // var batch = new CustomerBatch();
            // var line = new BatchDetail
            // {
            //     Customer = customer,
            //     Date = dto.ReceiptDate,
            //     Reference = dto.Reference,
            //     Description = "Receipt — " + dto.Reference,
            //     AmountInclusive = dto.Amount,
            //     IsDebit = false, // a receipt reduces the debtor balance
            //     // TransactionCode = <lookup the company's "Receipt" transaction code>,
            // };
            // batch.Detail.Add(line);
            // batch.Save();
            //
            // ...then find the outstanding invoice CustomerTransaction for
            // dto.InvoiceReference and the CustomerTransaction batch.Save() just
            // created, and allocate one against the other using whatever API your
            // guide's sample shows (AllocationCollection likely exposes an Add/
            // Allocate method rather than a settable AllocationEntry).
        }
    }
}
