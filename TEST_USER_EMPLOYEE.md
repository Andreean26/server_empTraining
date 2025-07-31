# Test User-Employee Auto Registration

## Test Case: Register User dengan Role Employee

### 1. Get Employee Role ID
GET http://localhost:8000/api/roles

### 2. Register User dengan Role Employee
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "Test Employee User",
    "email": "test.employee@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 4
}

### 3. Check if Employee record was created
GET http://localhost:8000/api/employees

### 4. Verify User-Employee Relationship
GET http://localhost:8000/api/users/{user_id}/employee

## Expected Results:
1. User created successfully with Employee role
2. Employee record automatically created with user_id link
3. Employee has same name and email as User
4. Employee_id generated automatically (EMP + timestamp format)
5. Default values set: department='General', position='Employee', status='active'
