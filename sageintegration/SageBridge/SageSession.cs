using System.Configuration;
using Pastel.Evolution;

namespace SageBridge
{
    /// <summary>
    /// Owns the connection to the Evolution company database for this process.
    ///
    /// <see cref="DatabaseContext"/> is a STATIC class in this SDK build (confirmed
    /// by compiling against the real DLL, not guessed) — there is exactly one
    /// ambient connection per process, set up once via DatabaseContext.Initialise/
    /// SetLicense, and every business object (Customer, SalesOrder, ...) implicitly
    /// uses it. That has two consequences this bridge is built around:
    ///   1. Initialise/SetLicense must run ONCE at process startup, not per request.
    ///   2. Because there is only one ambient connection for the whole process, it
    ///      is almost certainly not safe to run two Sage calls concurrently on
    ///      different threads. Every handler takes <see cref="Lock"/> before
    ///      touching any Pastel.Evolution type, and ApiServer dispatches requests
    ///      through that same lock — i.e. this bridge processes one Sage operation
    ///      at a time by design. If your SDK Developer Guide documents an official
    ///      multi-connection pattern, this is the place to change.
    ///
    /// IMPORTANT: confirm the exact Initialise(...) overload/parameter order
    /// against your guide's sample project before pointing this at a real
    /// database — DatabaseContext exposes a few overloads and this uses the most
    /// common (server, company, agent, password) shape as a starting point.
    /// </summary>
    internal static class SageSession
    {
        /// <summary>Hold this for the entire duration of any Sage SDK call.</summary>
        public static readonly object Lock = new object();

        private static bool _initialised;

        public static void EnsureInitialised()
        {
            if (_initialised)
            {
                return;
            }

            lock (Lock)
            {
                if (_initialised)
                {
                    return;
                }

                var companyName = Setting("Evolution.CompanyName");
                var agent = Setting("Evolution.Agent");
                var password = Setting("Evolution.Password");
                var server = Setting("Evolution.Server");
                var licenseSerial = Setting("Evolution.LicenseSerial");
                var licenseKey = Setting("Evolution.LicenseKey");

                // TODO confirm parameter order against your SDK Developer Guide —
                // DatabaseContext.Initialise has several overloads.
                DatabaseContext.Initialise(server, companyName, agent, password);

                if (!string.IsNullOrEmpty(licenseSerial))
                {
                    DatabaseContext.SetLicense(licenseSerial, licenseKey);
                }

                FileLog.Info(string.Format(
                    "Connected to Evolution company '{0}' as agent '{1}' (registered users: {2}, expiry: {3})",
                    DatabaseContext.CompanyName, agent, DatabaseContext.RegisteredUsers, DatabaseContext.ExpiryDate));

                _initialised = true;
            }
        }

        private static string Setting(string key)
        {
            return ConfigurationManager.AppSettings[key] ?? string.Empty;
        }
    }
}
