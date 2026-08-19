using System.Web.Script.Serialization;

namespace SageBridge
{
    /// <summary>
    /// Thin wrapper around the built-in JavaScriptSerializer (System.Web.Extensions)
    /// so the rest of the project doesn't need a NuGet restore just to talk JSON.
    /// </summary>
    internal static class Json
    {
        private static readonly JavaScriptSerializer Serializer = new JavaScriptSerializer
        {
            MaxJsonLength = 10 * 1024 * 1024,
        };

        public static string Serialize(object value)
        {
            return Serializer.Serialize(value);
        }

        public static T Deserialize<T>(string json)
        {
            return Serializer.Deserialize<T>(json);
        }

        public static System.Collections.Generic.Dictionary<string, object> ToDictionary(string json)
        {
            return Serializer.Deserialize<System.Collections.Generic.Dictionary<string, object>>(json);
        }
    }
}
