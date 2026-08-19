using System;

namespace SageBridge.Models
{
    /// <summary>Wire shape for POST /api/receipts.</summary>
    public class ReceiptDto
    {
        /// <summary>Must match a Customer.Code already posted via /api/customers.</summary>
        public string CustomerCode { get; set; }

        /// <summary>The Sage invoice document's Reference (the same value you sent
        /// as InvoiceDto.Reference) — used to look up the outstanding
        /// CustomerTransaction to allocate this receipt against.</summary>
        public string InvoiceReference { get; set; }

        public string Reference { get; set; }

        public double Amount { get; set; }

        public DateTime ReceiptDate { get; set; }
    }
}
