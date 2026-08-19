namespace SageBridge.Models
{
    /// <summary>
    /// Wire shape for POST /api/customers. Deliberately generic (not tied to the
    /// portal's own column names) — the Laravel-side SageClient maps its Customer
    /// model onto this shape.
    /// </summary>
    public class CustomerDto
    {
        /// <summary>Unique account code in Evolution. The portal should send its
        /// regnumber here so re-posting the same customer updates rather than
        /// duplicates them.</summary>
        public string Code { get; set; }

        public string Description { get; set; }

        public string Email { get; set; }

        public string Telephone { get; set; }

        public string TaxNumber { get; set; }

        public string AddressLine1 { get; set; }

        public string AddressLine2 { get; set; }

        public string AddressLine3 { get; set; }

        public string AddressLine4 { get; set; }
    }
}
