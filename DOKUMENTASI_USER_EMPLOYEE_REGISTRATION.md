# DOKUMENTASI ALUR REGISTRASI USER-EMPLOYEE

## ✅ IMPLEMENTASI BERHASIL SEMPURNA

### **Alur Registrasi User dengan Role Employee:**

#### 1. **Pendaftaran User Baru**
- User melakukan registrasi melalui `POST /api/register`
- Data yang dikirim:
```json
{
    "name": "Nama User",
    "email": "email@example.com", 
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3  // 3 = Employee Role
}
```

#### 2. **Proses Auto-Creation Employee Record**
Ketika user register dengan `role_id = 3` (Employee):

1. **User record dibuat** di tabel `users`
2. **System otomatis mendeteksi** bahwa user memiliki Employee role
3. **Employee record otomatis dibuat** di tabel `employees` dengan:
   - `user_id`: ID dari user yang baru dibuat
   - `employee_id`: Auto-generated dengan format `EMP + timestamp`
   - `name`: Sama dengan nama user
   - `email`: Sama dengan email user  
   - `department`: "General" (default)
   - `position`: "Employee" (default)
   - `hire_date`: Tanggal registrasi
   - `is_active`: true

#### 3. **Data Tersimpan Di:**

**Tabel `users`:**
- `id`, `uuid`, `name`, `email`, `password`, `role_id`, `is_active`, dll

**Tabel `employees` (auto-created):**
- `id`, `uuid`, `user_id` (FK), `employee_id`, `name`, `email`, `department`, `position`, `hire_date`, `is_active`, dll

**Tabel `roles`:**
- Role "Employee" dengan permissions: `trainings.read`, `enrollments.read`

### **Relationship Database:**

```
users (1) -----> (1) employees
  |                    |
  └─ role_id ────> roles
```

- **User hasOne Employee** (via user_id foreign key)
- **Employee belongsTo User** 
- **User belongsTo Role**

### **Contoh Data Hasil Registrasi:**

**User Data:**
```json
{
    "id": 10,
    "name": "Success Employee Test", 
    "email": "success.employee.test.2025@example.com",
    "role_id": 3,
    "role": {
        "name": "Employee",
        "permissions": ["trainings.read", "enrollments.read"]
    }
}
```

**Employee Data (Auto-Created):**
```json
{
    "id": 14,
    "user_id": 10,
    "employee_id": "EMP20250731014815",
    "name": "Success Employee Test",
    "email": "success.employee.test.2025@example.com", 
    "department": "General",
    "position": "Employee",
    "hire_date": "2025-07-31",
    "is_active": true
}
```

### **API Endpoints untuk Testing:**

1. **Register User:** `POST /api/register`
2. **Check Users with Employees:** `GET /api/users-with-employees`
3. **Check Specific User-Employee:** `GET /api/users/{id}/employee`
4. **List All Employees:** `GET /api/employees`

### **Implementasi Code:**

1. **AuthController.php** - Method `createEmployeeIfNeeded()` untuk auto-create
2. **UserObserver.php** - Observer pattern untuk lifecycle events
3. **User.php Model** - Relationship `hasOne(Employee::class)`
4. **Employee.php Model** - Relationship `belongsTo(User::class)`
5. **Migration** - Foreign key `user_id` di tabel employees

### **Keuntungan Implementasi:**

✅ **Automatic**: Employee record otomatis terbuat saat registrasi
✅ **Consistent**: Data user dan employee selalu sinkron
✅ **Relational**: Database relationship yang proper
✅ **Scalable**: Bisa di-extend untuk role lain
✅ **Maintainable**: Clean code dengan Observer pattern

### **Status: IMPLEMENTASI SELESAI DAN BERFUNGSI SEMPURNA! 🎉**
