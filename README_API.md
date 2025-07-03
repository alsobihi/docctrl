# DocCtrl API Documentation

## Overview

The DocCtrl API provides comprehensive access to the document control system, allowing you to manage employees, documents, workflows, and plants programmatically.

## Base URL

```
https://your-domain.com/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication. You need to obtain a token by logging in and include it in subsequent requests.

### Login

```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "admin",
            "plant_id": null
        },
        "token": "1|abc123..."
    },
    "message": "Login successful"
}
```

### Using the Token

Include the token in the Authorization header for all protected endpoints:

```http
Authorization: Bearer 1|abc123...
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/login` | Login user |
| POST | `/auth/logout` | Logout user |
| GET | `/auth/me` | Get current user |

### Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard/stats` | Get dashboard statistics |
| GET | `/dashboard/activities` | Get recent activities |

### Plants

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/plants` | List all plants |
| POST | `/plants` | Create new plant |
| GET | `/plants/{id}` | Get plant details |
| PUT | `/plants/{id}` | Update plant |
| DELETE | `/plants/{id}` | Delete plant |
| GET | `/plants/{id}/employees` | Get plant employees |

### Employees

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/employees` | List all employees |
| POST | `/employees` | Create new employee |
| GET | `/employees/{id}` | Get employee details |
| PUT | `/employees/{id}` | Update employee |
| DELETE | `/employees/{id}` | Delete employee |
| GET | `/employees/{id}/documents` | Get employee documents |
| GET | `/employees/{id}/workflows` | Get employee workflows |

### Documents

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/documents` | List all documents |
| POST | `/documents` | Create new document |
| GET | `/documents/{id}` | Get document details |
| DELETE | `/documents/{id}` | Delete document |
| GET | `/documents/{id}/download` | Download document file |
| GET | `/reports/expiring-documents` | Get expiring documents report |

### Workflows

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/workflows` | List all workflows |
| POST | `/workflows` | Create new workflow |
| GET | `/workflows/{id}` | Get workflow details |
| PUT | `/workflows/{id}` | Update workflow |
| DELETE | `/workflows/{id}` | Delete workflow |
| POST | `/workflows/{id}/document-types` | Add document type to workflow |
| DELETE | `/workflows/{id}/document-types/{typeId}` | Remove document type from workflow |
| POST | `/workflows/{id}/start` | Start workflow for employee |
| GET | `/workflows/{id}/employees/{employeeId}/checklist` | Get workflow checklist |
| GET | `/workflows/in-progress` | Get in-progress workflows |

## Request/Response Format

### Standard Response Format

All API responses follow this format:

```json
{
    "success": true|false,
    "data": {...},
    "message": "Success message",
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Error Response Format

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Query Parameters

### Pagination

- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

### Filtering

#### Employees
- `search`: Search by name or employee code
- `plant_id`: Filter by plant ID

#### Documents
- `employee_id`: Filter by employee ID
- `document_type_id`: Filter by document type ID
- `status`: Filter by status (expired, expiring_soon, valid)

#### Workflows
- `scope`: Filter by scope (global, plant, project)
- `plant_id`: Filter by plant ID
- `project_id`: Filter by project ID

## Examples

### Create Employee

```http
POST /api/v1/employees
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "employee_code": "EMP001",
    "plant_id": 1
}
```

### Upload Document

```http
POST /api/v1/documents
Authorization: Bearer 1|abc123...
Content-Type: multipart/form-data

employee_id=1
document_type_id=1
issue_date=2024-01-01
file=@document.pdf
```

### Start Workflow

```http
POST /api/v1/workflows/1/start
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
    "employee_id": 1
}
```

### Get Dashboard Stats

```http
GET /api/v1/dashboard/stats
Authorization: Bearer 1|abc123...
```

**Response:**
```json
{
    "success": true,
    "data": {
        "stats": {
            "total_employees": 150,
            "expiring_soon_count": 25,
            "expired_count": 5,
            "workflows_in_progress": 12
        },
        "urgent_documents": [...]
    }
}
```

## Rate Limiting

The API is rate limited to 60 requests per minute per user.

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## Role-Based Access

The API respects user roles:

- **Admin**: Full access to all resources
- **Manager**: Access limited to their assigned plant
- **Viewer**: Read-only access (if implemented)

## File Uploads

When uploading documents:
- Maximum file size: 2MB
- Allowed formats: PDF, JPG, PNG
- Files are stored securely and can be downloaded via the API

## Webhooks (Future Enhancement)

Future versions may include webhook support for real-time notifications of:
- Document expirations
- Workflow completions
- New document uploads

## SDK and Libraries

Consider creating SDKs for popular languages:
- JavaScript/TypeScript
- Python
- PHP
- Java

## Support

For API support, please contact the development team or refer to the main application documentation.