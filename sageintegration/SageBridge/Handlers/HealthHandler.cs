using System.Collections.Generic;
using Pastel.Evolution;

namespace SageBridge.Handlers
{
    internal static class HealthHandler
    {
        public static object Get()
        {
            lock (SageSession.Lock)
            {
                SageSession.EnsureInitialised();

                return new Dictionary<string, object>
                {
                    { "status", "ok" },
                    { "company", DatabaseContext.CompanyName },
                    { "connectionOpen", DatabaseContext.IsConnectionOpen },
                    { "registeredUsers", DatabaseContext.RegisteredUsers },
                    { "expiryDate", DatabaseContext.ExpiryDate },
                };
            }
        }
    }
}
