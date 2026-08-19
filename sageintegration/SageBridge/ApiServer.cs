using System;
using System.Collections.Generic;
using System.Configuration;
using System.IO;
using System.Net;
using System.Text;
using System.Threading;
using SageBridge.Handlers;
using SageBridge.Models;

namespace SageBridge
{
    /// <summary>Thrown by a handler to send a specific HTTP status + message back
    /// to Laravel instead of a generic 500.</summary>
    internal class ApiException : Exception
    {
        public int StatusCode { get; private set; }

        public ApiException(int statusCode, string message) : base(message)
        {
            StatusCode = statusCode;
        }
    }

    internal class ApiServer
    {
        private readonly HttpListener _listener = new HttpListener();
        private readonly string _apiKey;

        public ApiServer()
        {
            var prefix = ConfigurationManager.AppSettings["ListenPrefix"] ?? "http://127.0.0.1:8990/";
            _apiKey = ConfigurationManager.AppSettings["ApiKey"] ?? string.Empty;
            _listener.Prefixes.Add(prefix);
        }

        public void Run()
        {
            _listener.Start();
            FileLog.Info("SageBridge listening on " + string.Join(", ", _listener.Prefixes));

            while (true)
            {
                var context = _listener.GetContext();
                ThreadPool.QueueUserWorkItem(_ => Handle(context));
            }
        }

        private void Handle(HttpListenerContext context)
        {
            var request = context.Request;
            var response = context.Response;

            try
            {
                if (!string.IsNullOrEmpty(_apiKey) && request.Headers["X-Api-Key"] != _apiKey)
                {
                    WriteJson(response, 401, new Dictionary<string, object> { { "error", "Invalid or missing X-Api-Key." } });
                    return;
                }

                object result = Dispatch(request);
                WriteJson(response, 200, result);
            }
            catch (ApiException apiEx)
            {
                FileLog.Error("API error on " + request.Url, apiEx);
                WriteJson(response, apiEx.StatusCode, new Dictionary<string, object> { { "error", apiEx.Message } });
            }
            catch (Exception ex)
            {
                FileLog.Error("Unhandled error on " + request.Url, ex);
                WriteJson(response, 500, new Dictionary<string, object> { { "error", "Internal error. See bridge log for details." } });
            }
            finally
            {
                response.Close();
            }
        }

        private static object Dispatch(HttpListenerRequest request)
        {
            var path = request.Url.AbsolutePath.TrimEnd('/');
            var method = request.HttpMethod.ToUpperInvariant();

            if (method == "GET" && path == "/api/health")
            {
                return HealthHandler.Get();
            }

            if (method == "POST" && path == "/api/customers")
            {
                return CustomerHandler.Upsert(Json.Deserialize<CustomerDto>(ReadBody(request)));
            }

            if (method == "GET" && path.StartsWith("/api/customers/"))
            {
                var code = Uri.UnescapeDataString(path.Substring("/api/customers/".Length));
                return CustomerHandler.Get(code);
            }

            if (method == "POST" && path == "/api/invoices")
            {
                return InvoiceHandler.Create(Json.Deserialize<InvoiceDto>(ReadBody(request)));
            }

            if (method == "POST" && path == "/api/receipts")
            {
                return ReceiptHandler.Create(Json.Deserialize<ReceiptDto>(ReadBody(request)));
            }

            throw new ApiException(404, "No route for " + method + " " + path);
        }

        private static string ReadBody(HttpListenerRequest request)
        {
            using (var reader = new StreamReader(request.InputStream, request.ContentEncoding ?? Encoding.UTF8))
            {
                return reader.ReadToEnd();
            }
        }

        private static void WriteJson(HttpListenerResponse response, int statusCode, object payload)
        {
            var json = Json.Serialize(payload);
            var bytes = Encoding.UTF8.GetBytes(json);

            response.StatusCode = statusCode;
            response.ContentType = "application/json";
            response.ContentLength64 = bytes.Length;
            response.OutputStream.Write(bytes, 0, bytes.Length);
        }
    }
}
