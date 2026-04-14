# CI3 to CI4 Migration Guide

## Quick Start

```bash
# Install dependencies
composer install

# Run migration scanner
php migrate.php scan

# Run full migration
php migrate.php all

# Or use Makefile
make status
make test
```

## Migration Commands

### Makefile Commands
```bash
make install          # Install CI4 dependencies
make migrate-config   # Generate CI4 config files
make migrate-models   # Add namespaces to models
make migrate-controllers # Add namespaces to controllers
make migrate-helpers  # Convert helpers to services
make migrate-libraries # Update libraries
make migrate-routes   # Generate CI4 routes
make test            # Run syntax check
make clean           # Clear cache
make status          # Show migration status
```

### PHP Script Commands
```bash
php migrate.php scan          # Scan for CI3 patterns
php migrate.php replace-post  # Replace $_POST
php migrate.php replace-get   # Replace $_GET
php migrate.php add-namespace # Add namespaces
php migrate.php fix-routes    # Generate routes
php migrate.php all           # Run all
```

## Common Migration Patterns

### 1. Controllers

**Before (CI3):**
```php
class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $data['users'] = $this->db->get('users')->result();
        $email = $_POST['email'];
        $id = $_GET['id'];
    }
}
```

**After (CI4):**
```php
namespace App\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $users = $this->userModel->findAll();
        $email = $this->request->getPost('email');
        $id = $this->request->getGet('id');
    }
}
```

### 2. Models

**Before (CI3):**
```php
class User_model extends CI_Model {
    public function get_users() {
        return $this->db->get('users')->result();
    }
}
```

**After (CI4):**
```php
namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    public function get_users() {
        return $this->find();
    }
}
```

### 3. Helpers to Services

**Before (CI3):**
```php
// application/helpers/common_helper.php
function school_id() {
    return $_SESSION['school_id'];
}
```

**After (CI4):**
```php
// app/Services/CommonService.php
namespace App\Services;

class CommonService {
    public static function school_id() {
        return session()->get('school_id');
    }
}

// Backward compatible helper
if (!function_exists('school_id')) {
    function school_id() {
        return \App\Services\CommonService::school_id();
    }
}
```

### 4. Loading Libraries

**Before (CI3):**
```php
$this->load->library('email');
$this->email->send();
```

**After (CI4):**
```php
// Option 1: Service
$email = \Config\Services::email();

// Option 2: DI
public function __construct(Email $email) {
    $this->email = $email;
}
```

### 5. Database Queries

**Before (CI3):**
```php
$this->db->where('id', $id);
$query = $this->db->get('users');
```

**After (CI4):**
```php
// Using Model
$user = $userModel->find($id);

// Using Query Builder
$users = $this->db->table('users')
    ->where('id', $id)
    ->get()
    ->getResult();
```

### 6. Routing

**Before (CI3):** application/config/routes.php
```php
$route['admin/users'] = 'admin/user';
$route['admin/users/(:num)'] = 'admin/user/index/$1';
```

**After (CI4):** app/Config/Routes.php
```php
$routes->get('admin/users', 'Admin\User::index');
$routes->get('admin/users/(:num)', 'Admin\User::index/$1');
```

## Files Created

- **Makefile** - Common migration tasks
- **migrate.php** - Automated migration script
- **app/Config/App.php** - CI4 App configuration

## Next Steps

1. Run `php migrate.php scan` to identify issues
2. Fix superglobal access ($_POST, $_GET)
3. Add namespaces to all files
4. Convert helpers to services
5. Update routes
6. Test thoroughly

## Known Issues

- Session handling differences
- Email library changes
- Database query builder syntax
- Form validation changes
- File upload handling
