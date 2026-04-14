# Models Superglobal Access Audit Report

## Summary

This report identifies all instances where models access PHP superglobals (`$_POST`, `$_GET`, `$_REQUEST`) directly, which is an anti-pattern in CodeIgniter and causes issues when migrating to CI4.

---

## 🔍 FINDINGS BY MODEL

### 1. api/Admin_model.php (HIGH PRIORITY)

**Total Occurrences:** 19

| Line | Method | Superglobal | Issue |
|------|--------|-------------|-------|
| 109 | `update_profile()` | `$_POST['user_id']` | Direct POST access |
| 120-126 | `update_profile()` | `$_POST['name']`, `$_POST['email']`, etc. | Multiple POST fields |
| 152 | `change_password()` | `$_POST['user_id']` | Direct POST access |
| 153 | `change_password()` | `$_POST['new_password']` | Direct POST access |
| 226 | `login()` | `$_POST['email']`, `$_POST['password']` | Critical - auth data |
| 255 | `forgot_password()` | `$_POST['email']` | Direct POST access |
| 340 | `get_class_subjects()` | `$_GET['class_id']` | Direct GET access |
| 353 | `get_class_students()` | `$_GET['class_id']` | Direct GET access |
| 431 | `get_student_attendance()` | `$_GET['student_id']` | Direct GET access |

---

### 2. Crud_model.php (MEDIUM PRIORITY)

**Total Occurrences:** 2

| Line | Method | Superglobal | Issue |
|------|--------|-------------|-------|
| 2328 | `check_recaptcha()` | `$_POST["g-recaptcha-response"]` | Form validation |
| 2332 | `check_recaptcha()` | `$_POST["g-recaptcha-response"]` | API call data |

---

### 3. User_model.php (LOW PRIORITY)

**Total Occurrences:** 2 (1 commented)

| Line | Method | Superglobal | Issue |
|------|--------|-------------|-------|
| 698 | (commented) | `$_POST['email']` | Already commented out |
| 1660 | `change_password()` | `$_POST['current_password']`, `$_POST['new_password']`, `$_POST['confirm_password']` | Password change |

---

## 🔧 REFACTORING PLAN

### CI3 Approach (Current - Anti-Pattern)
```php
// ❌ BAD: Model accessing superglobals directly
public function update_profile() {
    $user_id = $_POST['user_id'];  // Anti-pattern
    $name = $_POST['name'];
    // ...
}
```

### CI3 Recommended Approach
```php
// ✅ BETTER: Model receives data as parameters
public function update_profile($user_id, $data) {
    $this->db->where('id', $user_id);
    $this->db->update('users', $data);
}
```

### CI4 Approach
```php
// ✅ BEST: Use Request object injection
public function update_profile($user_id, $data) {
    $this->db->table('users')->where('id', $user_id)->update($data);
}
```

---

## 📋 MIGRATION STEPS

### Step 1: api/Admin_model.php

**Current:**
```php
public function update_profile() {
    $user_id = $_POST['user_id'] ?? null;
    $data = [
        'name' => $_POST['name'] ?? null,
        'email' => $_POST['email'] ?? null,
        // ...
    ];
}
```

**After:**
```php
public function update_profile($user_id, array $data) {
    $this->db->table('users')->where('id', $user_id)->update($data);
}
```

### Step 2: api/Admin_model.php - GET parameters

**Current:**
```php
public function get_class_subjects() {
    $class_id = $_GET['class_id'];
}
```

**After:**
```php
public function get_class_subjects($class_id) {
    // ...
}
```

### Step 3: Crud_model.php - Recaptcha

**Current:**
```php
public function check_recaptcha() {
    $recaptcha_response = $_POST["g-recaptcha-response"];
}
```

**After:**
```php
public function check_recaptcha($recaptcha_response) {
    // ...
}
```

### Step 4: User_model.php - Password Change

**Current:**
```php
public function change_password() {
    if (!empty($_POST['current_password']) && ...) {
        // ...
    }
}
```

**After:**
```php
public function change_password($user_id, $current_password, $new_password) {
    // ...
}
```

---

## 🎯 IMPACT ASSESSMENT

| Model | Methods Affected | Risk Level | Effort | Status |
|-------|-----------------|------------|--------|--------|
| api/Admin_model.php | 7 | High | Medium | ✅ REFACTORED |
| Crud_model.php | 1 | Medium | Low | ⏳ Pending |
| User_model.php | 1 | Low | Low | ⏳ Pending |

---

## ✅ REFACTORING COMPLETED - api/Admin_model.php

The following methods have been refactored with backward-compatible parameter-based access:

| Method | Lines Changed |
|--------|---------------|
| `login()` | 224-237 |
| `forgot_password()` | 267-279 |
| `editProfile()` | 103-130 |
| `updatePassword()` | 150-163 |
| `get_subjects()` | 368-385 |
| `get_students()` | 392-409 |
| `get_student_wise_marks()` | 481-498 |

### Pattern Applied:

```php
public function login($email = null, $password = null) {
    // Backward compatibility: fall back to superglobals if params not provided
    if ($email === null) {
      $email = $_POST['email'] ?? '';
    }
    // ... validation and rest of method
}
```

---

## ✅ QUICK FIXES (Backward Compatible)

For immediate CI3 compatibility without changing all controller calls:

```php
// In model - accept both parameter AND superglobal fallback
public function update_profile($user_id = null, array $data = []) {
    // If called without params, fall back to superglobals (legacy)
    if ($user_id === null) {
        $user_id = $_POST['user_id'] ?? null;
    }
    if (empty($data)) {
        $data = [
            'name' => $_POST['name'] ?? null,
            'email' => $_POST['email'] ?? null,
            // ...
        ];
    }
    // ... rest of method
}
```

This approach allows:
1. **CI3**: Works with existing controller calls (superglobal fallback)
2. **CI4**: Works with new parameter-based calls
3. **Gradual migration**: Can update controllers one by one

---

## 📁 CONTROLLERS TO UPDATE

After refactoring models, these controllers need updates:

| Controller | Model Method Calls |
|------------|-------------------|
| `api/Admin.php` | `update_profile()`, `change_password()`, `login()` |
| `api/Wall.php` | Various API calls |
| `Admin.php` | `change_password()` |
| `User_model.php` | Internal calls |

---

*Generated: 2026-02-23*
