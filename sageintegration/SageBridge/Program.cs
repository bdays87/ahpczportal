using System;
using System.Configuration;

namespace SageBridge
{
    internal class Program
    {
        private static void Main(string[] args)
        {
            FileLog.Init(ConfigurationManager.AppSettings["LogPath"]);

            try
            {
                SageSession.EnsureInitialised();
            }
            catch (Exception ex)
            {
                FileLog.Error("Failed to connect to Evolution on startup. Check App.config.", ex);
                Console.WriteLine("Press Enter to exit...");
                Console.ReadLine();
                return;
            }

            var server = new ApiServer();

            try
            {
                server.Run();
            }
            catch (Exception ex)
            {
                FileLog.Error("SageBridge crashed.", ex);
                Console.WriteLine("Press Enter to exit...");
                Console.ReadLine();
            }
        }
    }
}
