using System;
using System.Collections.Generic;
using Pastel.Evolution;
using Pastel.Evolution.Common.Exception;
using SageBridge.Models;

namespace SageBridge.Handlers
{
    internal static class InvoiceHandler
    {
        public static object Create(InvoiceDto dto)
        {
            if (string.IsNullOrWhiteSpace(dto.CustomerCode))
            {
                throw new ApiException(400, "CustomerCode is required.");
            }

            if (dto.Lines == null || dto.Lines.Count == 0)
            {
                throw new ApiException(400, "At least one line is required.");
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
                    throw new ApiException(404, "Unknown CustomerCode — post the customer first via /api/customers.");
                }

                var order = BuildInvoiceDocument(customer, dto);

                FileLog.Info(string.Format(
                    "Created invoice for customer {0} (Sage ID {1}, ref {2}, total {3})",
                    customer.Code, order.ID, order.Reference, order.TotalIncl));

                return new Dictionary<string, object>
                {
                    { "sageId", order.ID },
                    { "reference", order.Reference },
                    { "totalExcl", order.TotalExcl },
                    { "totalTax", order.TotalTax },
                    { "totalIncl", order.TotalIncl },
                };
            }
        }

        /// <summary>
        /// Builds and saves the SalesOrder as a tax invoice.
        ///
        /// Verified by compiling against your actual DLL: SalesOrder.Type and
        /// SalesOrder.Reference are READ-ONLY from outside the SDK — object
        /// initializer syntax like `new SalesOrder { Type = DocumentType.Invoice }`
        /// does not compile. That means Document-Type-as-Invoice is chosen some
        /// other way than a simple property set (e.g. a specific constructor
        /// overload, a factory method, or a Process/Convert call after creating a
        /// plain order) — this is exactly the kind of thing that's spelled out in
        /// your SDK Developer Guide's "creating a Tax Invoice" sample but isn't
        /// safe to guess from reflection alone.
        ///
        /// Copy that sample's construction + save sequence in here. The rest of
        /// this handler (customer lookup, line building from InvoiceDto, response
        /// shape) is wired up and does not need to change.
        /// </summary>
        private static SalesOrder BuildInvoiceDocument(Customer customer, InvoiceDto dto)
        {
            throw new NotImplementedException(
                "Copy the SalesOrder/Invoice construction + Save() sequence from your SDK Developer Guide sample here. " +
                "SalesOrder.Type and .Reference have no public setter, so `new SalesOrder()` + property assignment (what " +
                "was tried first) does not compile — the guide's sample shows the actual supported way to create a " +
                "Type=Invoice document and add OrderDetail lines to it.");

            // Reference for whoever fills this in — building an OrderDetail line
            // for a non-stock/service item (this DID compile against the real
            // SDK) looks like this:
            //
            // var detail = new OrderDetail(line.GLAccountCode, line.Quantity, line.UnitPrice)
            // {
            //     Description = line.Description,
            // };
            // order.Detail.Add(detail);
            // order.Save();
        }
    }
}
