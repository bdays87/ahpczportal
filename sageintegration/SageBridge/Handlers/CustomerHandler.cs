using System;
using System.Collections.Generic;
using Pastel.Evolution;
using Pastel.Evolution.Common.Exception;
using SageBridge.Models;

namespace SageBridge.Handlers
{
    internal static class CustomerHandler
    {
        /// <summary>Create-or-update by Code — safe to call repeatedly for the
        /// same customer (e.g. whenever the portal's Customer record changes).</summary>
        public static object Upsert(CustomerDto dto)
        {
            if (string.IsNullOrWhiteSpace(dto.Code))
            {
                throw new ApiException(400, "Code is required.");
            }

            lock (SageSession.Lock)
            {
                SageSession.EnsureInitialised();

                Customer customer;
                try
                {
                    // Customer(string code) throws RecordNotExistsException when
                    // there's no match — that's our "does it already exist" check.
                    customer = new Customer(dto.Code);
                }
                // RecordNotExistsException itself is internal to the SDK (confirmed by compiling

                // against the real DLL), so we catch its public base instead. That means this

                // also catches other Evolution errors (e.g. a dropped connection) as if the

                // record were simply missing. If you need to tell those apart, check

                // ex.GetType().Name == "RecordNotExistsException" inside the catch block.

                catch (Pastel.Evolution.EvolutionException)
                {
                    customer = new Customer { Code = dto.Code };
                }

                customer.Description = dto.Description;
                customer.EmailAddress = dto.Email;
                customer.Telephone = dto.Telephone;
                customer.TaxNumber = dto.TaxNumber;

                var address = new Address(
                    dto.AddressLine1,
                    dto.AddressLine2,
                    dto.AddressLine3,
                    dto.AddressLine4,
                    string.Empty,
                    string.Empty);
                customer.PhysicalAddress = address;
                customer.PostalAddress = address;

                customer.Save();

                FileLog.Info(string.Format("Upserted customer {0} (Sage ID {1})", customer.Code, customer.ID));

                return new Dictionary<string, object>
                {
                    { "sageId", customer.ID },
                    { "code", customer.Code },
                };
            }
        }

        public static object Get(string code)
        {
            lock (SageSession.Lock)
            {
                SageSession.EnsureInitialised();

                try
                {
                    var customer = new Customer(code);
                    return new Dictionary<string, object>
                    {
                        { "sageId", customer.ID },
                        { "code", customer.Code },
                        { "description", customer.Description },
                        { "email", customer.EmailAddress },
                    };
                }
                catch (Pastel.Evolution.EvolutionException)
                {
                    throw new ApiException(404, "No customer with that code.");
                }
            }
        }
    }
}
