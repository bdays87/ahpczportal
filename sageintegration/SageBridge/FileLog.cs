using System;
using System.IO;

namespace SageBridge
{
    /// <summary>
    /// Minimal thread-safe file logger. No external dependency on purpose —
    /// this project should build with nothing beyond the .NET Framework +
    /// the two Pastel DLLs you were given.
    /// </summary>
    internal static class FileLog
    {
        private static readonly object Lock = new object();
        private static string _path;

        public static void Init(string path)
        {
            _path = path;
            var dir = Path.GetDirectoryName(path);
            if (!string.IsNullOrEmpty(dir) && !Directory.Exists(dir))
            {
                Directory.CreateDirectory(dir);
            }
        }

        public static void Info(string message)
        {
            Write("INFO", message);
        }

        public static void Error(string message, Exception ex = null)
        {
            Write("ERROR", ex == null ? message : message + " :: " + ex);
        }

        private static void Write(string level, string message)
        {
            var line = string.Format("{0:yyyy-MM-dd HH:mm:ss.fff} [{1}] {2}", DateTime.Now, level, message);
            Console.WriteLine(line);

            if (string.IsNullOrEmpty(_path))
            {
                return;
            }

            lock (Lock)
            {
                try
                {
                    File.AppendAllText(_path, line + Environment.NewLine);
                }
                catch
                {
                    // Never let logging bring the bridge down.
                }
            }
        }
    }
}
