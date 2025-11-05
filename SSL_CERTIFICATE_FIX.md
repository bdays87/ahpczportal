# SSL Certificate Fix for Paynow Integration

## Problem
The application was encountering SSL certificate errors when making requests to the Paynow API:
```
error setting certificate file: C:\laragon\etc\ssl\cacert.pem
```

## Solution
Created a custom HTTP client and Paynow wrapper to handle SSL certificate issues properly.

### Files Created/Modified:

1. **`app/Http/PaynowHttpClient.php`** - Custom HTTP client with improved SSL handling
2. **`app/Http/CustomPaynow.php`** - Custom Paynow class that allows injecting the custom HTTP client
3. **`app/implementations/_paynowRepository.php`** - Updated to use the custom Paynow class
4. **`config/ssl.php`** - Configuration file for SSL settings

### Configuration Options

Add these environment variables to your `.env` file if needed:

```env
# SSL Configuration
SSL_VERIFY=true                                    # Set to false only in development
SSL_CA_BUNDLE_PATH=                               # Path to custom CA bundle (optional)
SSL_CONNECT_TIMEOUT=10                            # Connection timeout in seconds
SSL_REQUEST_TIMEOUT=30                            # Request timeout in seconds
```

### How It Works

1. The custom HTTP client tries multiple approaches to handle SSL certificates:
   - First tries to use a custom CA bundle if specified in config
   - Falls back to system CA bundle detection
   - Uses Paynow's bundled certificate as final fallback
   - Only disables SSL verification if explicitly configured (not recommended for production)

2. The custom Paynow class extends the original Paynow class and uses reflection to inject our custom HTTP client.

3. All Paynow API calls now use this enhanced SSL handling.

### Testing

To test if the fix works:
1. Try making a Paynow transaction through the application
2. Check the application logs for any SSL-related errors
3. The transaction should now proceed without SSL certificate errors

### Troubleshooting

If you still encounter SSL issues:

1. **For Development Only**: Set `SSL_VERIFY=false` in your `.env` file
2. **For Production**: Ensure your server has up-to-date CA certificates
3. **Custom Certificate**: Set `SSL_CA_BUNDLE_PATH` to point to your certificate file

### Security Notes

- Never disable SSL verification in production environments
- Keep your CA certificates updated
- Use the custom CA bundle path option if you have specific certificate requirements

