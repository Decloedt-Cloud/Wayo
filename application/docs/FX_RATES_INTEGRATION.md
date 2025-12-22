# FX Rates Integration Documentation

## Overview

This integration fetches daily exchange rates from ExchangeRate-API (v6) and stores them in the database for historical tracking. It provides:

- Daily automatic rate fetching via cron
- REST API endpoints for retrieving rates
- Caching to reduce database load
- Currency conversion utility
- Health monitoring

## Base Currency

- **Base**: USD (USD = 1)
- **Tracked Currencies**: EUR, MAD, AED

---

## Installation

### 1. Database Setup

Run the SQL migration to create the `fx_rates_daily` table:

```bash
mysql -u root -p formation_db_1 < application/sql/fx_rates_daily.sql
```

Or run directly in phpMyAdmin.

### 2. Environment Configuration

Add these environment variables to your server or `.env` file:

```env
# Required: Get your API key from https://www.exchangerate-api.com/
EXCHANGE_RATE_API_KEY=your-api-key-here

# Required: Secure token for API access (generate with: openssl rand -hex 32)
FXRATES_API_TOKEN=your-secure-api-token

# Required: Secure token for cron access (generate with: openssl rand -hex 32)
FXRATES_CRON_TOKEN=your-secure-cron-token
```

**For CI3 without Dotenv**, edit `application/config/fxrates.php` directly:

```php
$config['fxrates_api_key'] = 'your-api-key-here';
$config['fxrates_api_token'] = 'your-secure-api-token';
$config['fxrates_cron_token'] = 'your-secure-cron-token';
```

### 3. Cron Job Setup

Add this to your crontab (`crontab -e`):

```bash
# Fetch FX rates daily at 02:05 UTC
5 2 * * * cd /path/to/SchoolManagement && php index.php cron fx_fetch_daily >> /var/log/fxrates.log 2>&1
```

Or use HTTP with token (if CLI not available):

```bash
5 2 * * * curl -s "https://yoursite.com/cron/fx_fetch_daily?cron_token=YOUR_CRON_TOKEN" >> /var/log/fxrates.log 2>&1
```

---

## API Endpoints

All endpoints require authentication via `X-API-Token` header or `api_token` query parameter.

### Get Today's Rates

```
GET /api/fx/today
```

**Response:**
```json
{
    "base": "USD",
    "date": "2025-12-18",
    "rates": {
        "USD": 1,
        "EUR": 0.923456,
        "MAD": 10.052134,
        "AED": 3.672500
    },
    "stale": false,
    "source": "exchange-rate-api"
}
```

If today's rates are not available, returns latest with `stale: true`.

### Get Latest Available Rates

```
GET /api/fx/latest
```

### Get Rates for Specific Date

```
GET /api/fx/date/2025-12-15
```

### Get Rates for Date Range

```
GET /api/fx/range?start=2025-12-01&end=2025-12-18
```

**Response:**
```json
{
    "base": "USD",
    "start_date": "2025-12-01",
    "end_date": "2025-12-18",
    "count": 18,
    "rates": [
        {
            "base": "USD",
            "date": "2025-12-01",
            "rates": {...},
            "stale": false,
            "source": "exchange-rate-api"
        },
        ...
    ]
}
```

### Currency Conversion

```
GET /api/fx/convert?amount=100&from=USD&to=EUR
GET /api/fx/convert?amount=100&from=USD&to=EUR&date=2025-12-15
```

**Response:**
```json
{
    "amount": 100,
    "from": "USD",
    "to": "EUR",
    "converted": 92.3456,
    "date": "2025-12-18",
    "rate": 0.923456
}
```

### Health Check

```
GET /api/fx/health
```

**Response:**
```json
{
    "status": "healthy",
    "timestamp": "2025-12-18T10:30:00+00:00",
    "details": {
        "api_key_configured": true,
        "has_today_rates": true,
        "latest_rate_date": "2025-12-18",
        "latest_status": "ok",
        "total_records": 365,
        "cache_duration": 600,
        "timezone": "UTC"
    }
}
```

---

## CLI Commands

Run from project root:

```bash
# Fetch and store today's rates
php index.php cron fx_fetch_daily

# Check service health
php index.php cron fx_health

# Test API connection (without storing)
php index.php cron fx_test_api

# Clear cache
php index.php cron fx_clear_cache

# Cleanup old records (keep last 365 days)
php index.php cron fx_cleanup 365
```

---

## Usage in Application Code

### Get Rates in Controller/Model

```php
// Load the service
$this->load->library('FxRatesService', null, 'fxService');

// Get today's rates
$rates = $this->fxService->getTodayRates();
echo $rates['rates']['EUR']; // 0.923456

// Get rates for specific date
$rates = $this->fxService->getRatesByDate('2025-12-15');

// Convert currency
$eur_amount = $this->fxService->convert(100, 'USD', 'EUR');
echo "100 USD = {$eur_amount} EUR";

// Get date range
$history = $this->fxService->getRange('2025-12-01', '2025-12-18');
```

### Direct Model Access

```php
$this->load->model('FxRates_model', 'fxrates_model');

// Get raw database record
$record = $this->fxrates_model->get_by_date('2025-12-18');

// Get latest record
$latest = $this->fxrates_model->get_latest();

// Check if today's rates exist
$has_today = $this->fxrates_model->has_today_rates();
```

---

## Error Handling

The service handles these error scenarios:

1. **API Temporarily Down**: Returns last known rates with `stale: true`
2. **Invalid API Key**: Logs error, returns stale data if available
3. **Network Timeout**: Configurable timeout (default 30s)
4. **Invalid Response**: Validates JSON structure and required fields
5. **Missing Currency**: Logs error if tracked currency missing from response

---

## Security

1. **API Key**: Never exposed to frontend, server-side only
2. **API Token**: Required for all API endpoints
3. **Cron Token**: Separate token for scheduled tasks
4. **HTTPS**: Always use HTTPS in production

---

## Configuration Options

Edit `application/config/fxrates.php`:

| Option | Default | Description |
|--------|---------|-------------|
| `fxrates_api_key` | env var | ExchangeRate-API key |
| `fxrates_base_currency` | USD | Base currency for rates |
| `fxrates_currencies` | [USD,EUR,MAD,AED] | Currencies to track |
| `fxrates_cache_duration` | 600 | Cache TTL in seconds |
| `fxrates_api_timeout` | 30 | API request timeout |
| `fxrates_store_raw_json` | true | Store raw API response |
| `fxrates_timezone` | UTC | Timezone for dates |

---

## Database Schema

```sql
CREATE TABLE fx_rates_daily (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rate_date DATE NOT NULL,
    base_code VARCHAR(3) DEFAULT 'USD',
    usd DECIMAL(12,6) DEFAULT 1.000000,
    eur DECIMAL(12,6) NOT NULL,
    mad DECIMAL(12,6) NOT NULL,
    aed DECIMAL(12,6) NOT NULL,
    source VARCHAR(32) DEFAULT 'exchange-rate-api',
    fetched_at DATETIME NOT NULL,
    status VARCHAR(16) DEFAULT 'ok',
    raw_json LONGTEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_rate_date_base (rate_date, base_code)
);
```

---

## Troubleshooting

### Rates Not Updating

1. Check cron is running: `grep fxrates /var/log/syslog`
2. Test API manually: `php index.php cron fx_test_api`
3. Check logs: `tail -f application/logs/log-*.php | grep -i fx`

### API Returns Stale Data

1. Verify API key is valid
2. Check if today's cron has run
3. Manually fetch: `php index.php cron fx_fetch_daily`

### Permission Errors

1. Ensure cache directory is writable
2. Check file permissions on `application/cache/`

---

## ExchangeRate-API Limits

- **Free Tier**: 1,500 requests/month
- **Paid Plans**: Higher limits available
- **Rate Limit**: One daily fetch is sufficient

---

## Files Created

```
application/
├── config/
│   └── fxrates.php           # Configuration
├── controllers/
│   ├── api/
│   │   └── FxRates.php       # API endpoints
│   └── Cron.php              # CLI/Cron commands
├── docs/
│   └── FX_RATES_INTEGRATION.md  # This documentation
├── libraries/
│   └── FxRatesService.php    # Service layer
├── models/
│   └── FxRates_model.php     # Database model
├── sql/
│   └── fx_rates_daily.sql    # Database migration
└── tests/
    └── FxRatesTest.php       # Unit tests
```

