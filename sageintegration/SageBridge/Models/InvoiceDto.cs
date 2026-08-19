using System;
using System.Collections.Generic;

namespace SageBridge.Models
{
    /// <summary>Wire shape for POST /api/invoices.</summary>
    public class InvoiceDto
    {
        /// <summary>Must match a Customer.Code already posted via /api/customers.</summary>
        public string CustomerCode { get; set; }

        /// <summary>The portal's own invoice_number / uuid — stashed as the
        /// Evolution document's Reference so you can trace a Sage invoice back
        /// to the row that created it.</summary>
        public string Reference { get; set; }

        public DateTime InvoiceDate { get; set; }

        public List<InvoiceLineDto> Lines { get; set; }
    }

    public class InvoiceLineDto
    {
        /// <summary>GL account code to post this line against (this portal has no
        /// stock/inventory items — registration/application fees post straight to
        /// a revenue GL account). Confirm the exact OrderDetail constructor
        /// overload against your guide; the (string, double, double) overload
        /// this bridge uses is intended for exactly this non-stock case.</summary>
        public string GLAccountCode { get; set; }

        public string Description { get; set; }

        public double Quantity { get; set; }

        public double UnitPrice { get; set; }
    }
}
