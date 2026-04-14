# CI4 Configuration Migration Templates

This folder contains CI4-compatible configuration file templates based on the existing CI3 configuration.

## Files Included

| File | Description | CI3 Source |
|------|-------------|------------|
| `App.php` | Main application configuration | `config.php` |
| `Database.php` | Database connection settings | `database.php` |
| `Routes.php` | URL routing configuration | `routes.php` |
| `Filters.php` | Request filters/middleware | `autoload.php`, `hooks.php` |
| `Services.php` | Service container setup | `autoload.php` |
| `Constants.php` | Application constants | `constants.php` |

## How to Use

1. **Backup existing CI3 config files** before migration
2. **Copy files** from `ci4_migration/config/` to `app/Config/`
3. **Adjust namespaces** as needed for your CI4 installation
4. **Test thoroughly** in a development environment

## Key Changes from CI3 to CI4

### Configuration
- CI4 uses **PHP classes** instead of arrays
- Configuration files are in `app/Config/`
- Use `BaseConfig` as the base class

### Database
- `dbdriver` → `DBDriver`
- `db_debug` → `DBDebug`
- `char_set` → `charset`

### Routing
- CI4 uses **controller/method** instead of class/method
- Supports **namespace-based** routing
- Filters replace CI3 hooks

### Autoloading
- CI4 uses **Service Containers**
- Libraries loaded via `Services::library()`
- Helpers via `helper()` function

## Notes

- These templates maintain **backward compatibility** where possible
- Some CI3-specific features may require manual adjustment
- Always test in development before production deployment
