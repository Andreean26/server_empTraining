# Employee Training Management System - API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
This API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header for protected routes.

```
Authorization: Bearer {token}
```

## Response Format
All API responses follow this format:

```json
{
    "success": true|false,
    "message": "Response message",
    "data": {} // Response data (optional),
    "errors": {} // Validation errors (optional)
}
```

---

## Authentication Endpoints

### 1. Register
**POST** `/register`

Create a new user account.

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com", 
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": "uuid-role-id"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": "uuid",
            "name": "John Doe",
            "email": "john@example.com",
            "role": {
                "id": "uuid",
                "name": "Employee"
            }
        },
        "access_token": "token",
        "token_type": "Bearer"
    }
}
```

### 2. Login
**POST** `/login`

**Request Body:**
```json
{
    "email": "admin@company.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": "uuid",
            "name": "Admin User",
            "email": "admin@company.com",
            "role": {
                "id": "uuid",
                "name": "Administrator",
                "permissions": ["user_management", "employee_management"]
            }
        },
        "access_token": "token",
        "token_type": "Bearer"
    }
}
```

### 3. Get Profile
**GET** `/profile` (Protected)

Get current user profile information.

### 4. Update Profile
**PUT** `/profile` (Protected)

**Request Body:**
```json
{
    "name": "Updated Name",
    "email": "updated@example.com"
}
```

### 5. Change Password
**PUT** `/change-password` (Protected)

**Request Body:**
```json
{
    "current_password": "current_password",
    "new_password": "new_password123",
    "new_password_confirmation": "new_password123"
}
```

### 6. Logout
**POST** `/logout` (Protected)

Logout from current device.

### 7. Logout All
**POST** `/logout-all` (Protected)

Logout from all devices.

---

## Dashboard Endpoints

### 1. Get Dashboard Statistics
**GET** `/dashboard` (Protected)

**Response:**
```json
{
    "success": true,
    "message": "Dashboard statistics retrieved successfully",
    "data": {
        "total_employees": 25,
        "total_trainings": 15,
        "total_enrollments": 120,
        "total_users": 8,
        "active_trainings": 5,
        "completed_trainings": 85
    }
}
```

### 2. Get Recent Trainings
**GET** `/dashboard/recent-trainings` (Protected)

### 3. Get Upcoming Trainings
**GET** `/dashboard/upcoming-trainings` (Protected)

### 4. Get Trainings by Department
**GET** `/dashboard/trainings-by-department` (Protected)

### 5. Get Monthly Completions
**GET** `/dashboard/monthly-completions` (Protected)

### 6. Get Enrollment Status
**GET** `/dashboard/enrollment-status` (Protected)

### 7. Get Top Performers
**GET** `/dashboard/top-performers` (Protected)

### 8. Get Completion Rates
**GET** `/dashboard/completion-rates` (Protected)

---

## Employee Endpoints

### 1. List Employees
**GET** `/employees` (Protected)

**Query Parameters:**
- `department` (optional): Filter by department
- `position` (optional): Filter by position  
- `search` (optional): Search by name or email
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response:**
```json
{
    "success": true,
    "message": "Employees retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "uuid",
                "first_name": "John",
                "last_name": "Doe",
                "email": "john.doe@company.com",
                "phone": "+1234567890",
                "department": "IT",
                "position": "Software Engineer",
                "hire_date": "2023-01-15",
                "metadata": {},
                "training_enrollments": []
            }
        ],
        "total": 25,
        "per_page": 15,
        "last_page": 2
    }
}
```

### 2. Create Employee
**POST** `/employees` (Protected)

**Request Body:**
```json
{
    "first_name": "John",
    "last_name": "Doe", 
    "email": "john.doe@company.com",
    "phone": "+1234567890",
    "department": "IT",
    "position": "Software Engineer",
    "hire_date": "2023-01-15",
    "metadata": {
        "emergency_contact": "Jane Doe",
        "emergency_phone": "+0987654321"
    }
}
```

### 3. Get Employee
**GET** `/employees/{id}` (Protected)

### 4. Update Employee
**PUT** `/employees/{id}` (Protected)

### 5. Delete Employee
**DELETE** `/employees/{id}` (Protected)

### 6. Get Departments
**GET** `/employees-departments` (Protected)

**Response:**
```json
{
    "success": true,
    "message": "Departments retrieved successfully", 
    "data": ["IT", "HR", "Finance", "Marketing"]
}
```

### 7. Get Positions
**GET** `/employees-positions` (Protected)

---

## Training Endpoints

### 1. List Trainings
**GET** `/trainings` (Protected)

**Query Parameters:**
- `start_date` & `end_date` (optional): Filter by date range
- `status` (optional): upcoming|ongoing|completed
- `search` (optional): Search by title or description
- `per_page` (optional): Items per page
- `page` (optional): Page number

### 2. Create Training
**POST** `/trainings` (Protected)

**Request Body (multipart/form-data):**
```json
{
    "title": "Web Development Basics",
    "description": "Learn HTML, CSS, and JavaScript fundamentals",
    "training_date": "2024-02-15",
    "duration_hours": 8,
    "max_participants": 20,
    "location": "Conference Room A",
    "pdf_file": "file.pdf" // Optional PDF file
}
```

### 3. Get Training
**GET** `/trainings/{id}` (Protected)

### 4. Update Training
**PUT** `/trainings/{id}` (Protected)

### 5. Delete Training
**DELETE** `/trainings/{id}` (Protected)

### 6. Download Training PDF
**GET** `/trainings/{id}/download-pdf` (Protected)

---

## Training Enrollment Endpoints

### 1. List Enrollments
**GET** `/enrollments` (Protected)

**Query Parameters:**
- `training_id` (optional): Filter by training
- `employee_id` (optional): Filter by employee
- `status` (optional): enrolled|completed|cancelled

### 2. Enroll Employee
**POST** `/enrollments` (Protected)

**Request Body:**
```json
{
    "training_id": "uuid",
    "employee_id": "uuid", 
    "status": "enrolled" // Optional: enrolled|completed|cancelled
}
```

### 3. Get Enrollment
**GET** `/enrollments/{id}` (Protected)

### 4. Update Enrollment
**PUT** `/enrollments/{id}` (Protected)

**Request Body:**
```json
{
    "status": "completed",
    "completion_score": 85.5,
    "feedback": "Excellent training session"
}
```

### 5. Cancel Enrollment
**DELETE** `/enrollments/{id}` (Protected)

### 6. Get Training Enrollments
**GET** `/trainings/{training_id}/enrollments` (Protected)

**Response:**
```json
{
    "success": true,
    "message": "Training enrollments retrieved successfully",
    "data": {
        "training": {},
        "enrollments": [],
        "statistics": {
            "total_enrolled": 15,
            "total_completed": 10,
            "total_cancelled": 2,
            "available_slots": 5
        }
    }
}
```

### 7. Get Employee Enrollments
**GET** `/employees/{employee_id}/enrollments` (Protected)

---

## Error Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **404**: Not Found
- **409**: Conflict (e.g., duplicate enrollment)
- **422**: Validation Error
- **500**: Internal Server Error

## Demo Accounts

```json
{
    "admin": {
        "email": "admin@company.com",
        "password": "password123"
    },
    "hr_manager": {
        "email": "hr@company.com", 
        "password": "password123"
    },
    "supervisor": {
        "email": "supervisor@company.com",
        "password": "password123"
    },
    "employee": {
        "email": "employee@company.com",
        "password": "password123"
    }
}
```

## Database Configuration

### For MySQL (Current Setup)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=emp_training_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### For SQLite (Development Alternative)
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

## Installation & Setup

1. **Clone & Install**
```bash
git clone <repository>
cd server_empTraining
composer install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup**
```bash
# For MySQL (Current Setup)
mysql -u root -p -e "CREATE DATABASE emp_training_db;"

# For SQLite (Alternative)
touch database/database.sqlite
```

4. **Run Migrations**
```bash
php artisan migrate:fresh --seed
```

5. **Start Server**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## File Upload Configuration

PDF files are stored in `storage/app/public/training_materials/`

To make files accessible via URL:
```bash
php artisan storage:link
```

## Testing API

### Using cURL
```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@company.com","password":"password123"}'

# Use token in subsequent requests
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using Postman
1. Import the API endpoints
2. Set Authorization type to "Bearer Token"
3. Use the token from login response
