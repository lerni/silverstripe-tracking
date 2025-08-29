# Silverstripe Analytics, Tag Manager, Clarity & Bing Site-Verification Module
A Silverstripe module for Google Analytics, Google Tag Manager, Microsoft Clarity and Bing site verification. Features Consent Mode v2 support for privacy-compliant tracking, managed through Silverstripe's admin interface.

## Requirements
-   silverstripe/cms ^6
-   silverstripe/siteconfig ^6

## Suggested
-   lerni/klaro-cookie-consent

## Installation
[Composer](https://getcomposer.org/) is the recommended way to install Silverstripe modules.

```bash
composer require lerni/silverstripe-tracking
```

Run `dev/build`

This module allows XML files to be uploaded for Bing site verification.

## Configuration

### Setup
1. Go to **Settings > Tracking** in the CMS admin
2. Add your tracking IDs:
   - **Google Tag Manager**: `GTM-XXXXXXX` 
   - **Google Analytics v4**: `G-XXXXXXXXXX` (one per line for multiple properties)
   - **Microsoft Clarity**: Your Clarity project ID

### Consent Mode v2 Setup

1. **Enable Consent Mode**: Check "Enable Consent Mode v2" in Settings > Tracking
2. **Cookie Consent Integration**: Install `lerni/klaro-cookie-consent` for consent management
3. **Setup callback events**: onAccept & onDecline for each service as needed

### Additional Configuration
```yaml
Kraftausdruck\Extensions\PageTrackingExtension:
  # Include logged-in members in tracking (default: false)
  track_members: true
  # Enable preconnect links for improved performance (default: false)
  # Enabling preconnect may leak user IP addresses to Google/Microsoft even before consent is given, as DNS lookups and TCP connections are established early. Consider this when implementing strict privacy requirements.
  preconnect: true
```

## How Consent Mode Works

### Without Cookie Consent Manager
When no consent management is active, the module automatically sets conservative consent defaults:

```javascript
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'analytics_storage': 'denied', 
    'ad_user_data': 'denied',
    'ad_personalization': 'denied'
});
```

### With Klaro Cookie Consent
When integrated with the Klaro module, consent is dynamically updated based on user choices:

```javascript
// User accepts Analytics service
gtag('consent', 'update', {'analytics_storage': 'granted'});

// User accepts Advertising service  
gtag('consent', 'update', {
    'ad_storage': 'granted',
    'ad_user_data': 'granted', 
    'ad_personalization': 'granted'
});
```

### Multiple GA4 Properties
Add multiple Google Analytics 4 properties (one per line):
```
G-XXXXXXXXXX
G-YYYYYYYYYY
G-ZZZZZZZZZZ
```

### Custom Configuration
The module automatically includes enhanced security settings when Consent Mode is enabled:

```javascript
gtag('config', 'GA_MEASUREMENT_ID', {
    'anonymize_ip': true,
    'cookie_flags': 'secure;samesite=lax'
});
```

## Important Notes
-   **Live Mode Only**: Tracking codes are only shown in live mode (not in development)
-   **Member Tracking**: By default, logged-in members are excluded from tracking.
-   **Cookie Consent**: Use `lerni/klaro-cookie-consent` for GDPR-compliance


### Debug Mode
Enable debug mode to see consent state in browser console:

```javascript
// In browser console
dataLayer.forEach(item => {
    if (item[0] === 'consent') console.log(item);
});
```

## Resources
- [Google Consent Mode v2 Guide](https://developers.google.com/tag-platform/security/guides/consent)
- [Google Analytics 4 Documentation](https://developers.google.com/analytics/devguides/collection/ga4)
- [Microsoft Clarity Documentation](https://docs.microsoft.com/en-us/clarity/)
