# 🚀 Foxes Rentals API Documentation

## 📋 Overview

The Foxes Rentals API provides comprehensive endpoints for managing properties, leases, houses, users, and payments. All API responses follow a standardized format for consistency and ease of integration.

## 🔐 Authentication

The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## 📊 Response Format

All API responses follow this standardized format:

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ... },
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": { ... },
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Data retrieved successfully",
    "data": [ ... ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75,
        "from": 1,
        "to": 15,
        "has_more_pages": true
    },
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

## 🏠 Properties API

### Get All Properties
```http
GET /api/properties
```

**Query Parameters:**
- `status` (string): Filter by status (active, inactive, maintenance)
- `landlord_id` (integer): Filter by landlord ID
- `is_vacant` (boolean): Filter by vacancy status
- `type` (string): Filter by property type
- `search` (string): Search in name and description
- `per_page` (integer): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "message": "Properties retrieved successfully",
    "data": [
        {
            "id": "uuid",
            "name": "Beautiful House",
            "description": "A beautiful house description",
            "type": "house",
            "rent": 1500.00,
            "deposit": 3000.00,
            "status": "active",
            "is_vacant": true,
            "landlord_id": "uuid",
            "commission": 10,
            "electricity_id": "EL123456",
            "created_at": "2024-01-15T10:30:00.000Z",
            "updated_at": "2024-01-15T10:30:00.000Z"
        }
    ],
    "pagination": { ... }
}
```

### Get Single Property
```http
GET /api/properties/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Property retrieved successfully",
    "data": {
        "id": "uuid",
        "name": "Beautiful House",
        "description": "A beautiful house description",
        "type": "house",
        "rent": 1500.00,
        "deposit": 3000.00,
        "status": "active",
        "is_vacant": true,
        "landlord_id": "uuid",
        "commission": 10,
        "electricity_id": "EL123456",
        "landlord": {
            "id": "uuid",
            "name": "John Doe"
        },
        "address": {
            "street": "123 Main St",
            "city": "Nairobi",
            "state": "Nairobi",
            "postal_code": "00100",
            "country": "Kenya"
        },
        "houses": [ ... ],
        "leases": [ ... ]
    }
}
```

### Create Property
```http
POST /api/properties
```

**Request Body:**
```json
{
    "name": "Beautiful House",
    "description": "A beautiful house description",
    "type": "house",
    "rent": 1500.00,
    "deposit": 3000.00,
    "landlord_id": "uuid",
    "commission": 10,
    "status": "active",
    "is_vacant": true,
    "electricity_id": "EL123456",
    "address": {
        "street": "123 Main St",
        "city": "Nairobi",
        "state": "Nairobi",
        "postal_code": "00100",
        "country": "Kenya"
    }
}
```

**Response:**
```json
{
    "success": true,
    "message": "Property created successfully",
    "data": { ... },
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

### Update Property
```http
PUT /api/properties/{id}
```

**Request Body:** Same as create property

**Response:**
```json
{
    "success": true,
    "message": "Property updated successfully",
    "data": { ... }
}
```

### Delete Property
```http
DELETE /api/properties/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Property deleted successfully",
    "timestamp": "2024-01-15T10:30:00.000Z"
}
```

### Get Property Statistics
```http
GET /api/properties/statistics
```

**Response:**
```json
{
    "success": true,
    "message": "Property statistics retrieved successfully",
    "data": {
        "total_properties": 25,
        "active_properties": 20,
        "vacant_properties": 15,
        "occupied_properties": 10,
        "properties_by_type": {
            "house": 10,
            "apartment": 8,
            "bungalow": 7
        },
        "properties_by_status": {
            "active": 20,
            "inactive": 3,
            "maintenance": 2
        }
    }
}
```

## 👥 Users API

### Get All Users
```http
GET /api/users
```

**Query Parameters:**
- `role` (string): Filter by role
- `status` (string): Filter by status (active, inactive)
- `search` (string): Search in name, email, phone
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date
- `per_page` (integer): Items per page

**Response:**
```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": "uuid",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+254712345678",
            "is_active": true,
            "roles": ["landlord"],
            "permissions": ["view property", "create property"],
            "created_at": "2024-01-15T10:30:00.000Z"
        }
    ],
    "pagination": { ... }
}
```

### Get Single User
```http
GET /api/users/{id}
```

### Create User
```http
POST /api/users
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+254712345678",
    "password": "securepassword",
    "role": "landlord",
    "is_active": true
}
```

### Update User
```http
PUT /api/users/{id}
```

### Delete User
```http
DELETE /api/users/{id}
```

### Toggle User Status
```http
POST /api/users/{id}/toggle-status
```

### Reset User Password
```http
POST /api/users/{id}/reset-password
```

## 🏠 Houses API

### Get All Houses
```http
GET /api/houses
```

### Get Single House
```http
GET /api/houses/{id}
```

### Create House
```http
POST /api/houses
```

**Request Body:**
```json
{
    "name": "Unit A",
    "description": "A beautiful unit",
    "property_id": "uuid",
    "rent": 1200.00,
    "deposit": 2400.00,
    "landlord_id": "uuid",
    "status": "active",
    "is_vacant": true,
    "bedrooms": 2,
    "bathrooms": 1,
    "size": 75.5,
    "house_type_id": "uuid"
}
```

### Update House
```http
PUT /api/houses/{id}
```

### Delete House
```http
DELETE /api/houses/{id}
```

## 📄 Leases API

### Get All Leases
```http
GET /api/leases
```

### Get Single Lease
```http
GET /api/leases/{id}
```

### Create Lease
```http
POST /api/leases
```

**Request Body:**
```json
{
    "lease_id": "LEASE-001",
    "start_date": "2024-01-01",
    "end_date": "2024-12-31",
    "property_id": "uuid",
    "house_id": "uuid",
    "tenant_id": "uuid",
    "rent": 1500.00,
    "rent_cycle": 1,
    "invoice_generation_day": 1,
    "termination_date_notice": 30,
    "status": "active"
}
```

### Update Lease
```http
PUT /api/leases/{id}
```

### Delete Lease
```http
DELETE /api/leases/{id}
```

## 💰 Payments API

### Get All Payments
```http
GET /api/payments
```

### Get Single Payment
```http
GET /api/payments/{id}
```

### Create Payment
```http
POST /api/payments
```

**Request Body:**
```json
{
    "invoice_id": "uuid",
    "amount": 1500.00,
    "payment_method": "mpesa",
    "reference_number": "REF123456",
    "paid_at": "2024-01-15",
    "status": "completed",
    "notes": "Payment notes"
}
```

### Update Payment
```http
PUT /api/payments/{id}
```

### Verify Payment
```http
POST /api/payments/{id}/verify
```

## 📊 Reports API

### Get Financial Reports
```http
GET /api/reports/financial
```

**Query Parameters:**
- `start_date` (date): Report start date
- `end_date` (date): Report end date
- `type` (string): Report type (income, expense, profit)

### Get Property Reports
```http
GET /api/reports/properties
```

### Get Occupancy Reports
```http
GET /api/reports/occupancy
```

## 🔧 Health Check

### System Health
```http
GET /api/public/health
```

**Response:**
```json
{
    "status": "healthy",
    "timestamp": "2024-01-15T10:30:00.000Z",
    "version": "1.0.0"
}
```

### System Information
```http
GET /api/public/info
```

**Response:**
```json
{
    "name": "Foxes Rentals",
    "version": "1.0.0",
    "environment": "production",
    "debug": false
}
```

## 📝 Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 405 | Method Not Allowed |
| 409 | Conflict |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## 🔒 Permissions

### Property Permissions
- `view property` - View properties
- `create property` - Create properties
- `edit property` - Edit properties
- `delete property` - Delete properties

### User Permissions
- `view users` - View users
- `create users` - Create users
- `edit users` - Edit users
- `delete users` - Delete users

### Lease Permissions
- `view lease` - View leases
- `create lease` - Create leases
- `edit lease` - Edit leases
- `delete lease` - Delete leases

### Payment Permissions
- `view payment` - View payments
- `create payment` - Create payments
- `edit payment` - Edit payments
- `verify payment` - Verify payments

## 🚀 Rate Limiting

API endpoints are rate limited:
- **Default**: 60 requests per minute per user
- **Authentication**: 5 requests per minute per IP
- **Payment endpoints**: 10 requests per minute per user

## 📱 SDK Examples

### JavaScript/Node.js
```javascript
const axios = require('axios');

const api = axios.create({
    baseURL: 'https://your-domain.com/api',
    headers: {
        'Authorization': 'Bearer your-token',
        'Content-Type': 'application/json'
    }
});

// Get properties
const properties = await api.get('/properties');

// Create property
const newProperty = await api.post('/properties', {
    name: 'New Property',
    type: 'house',
    rent: 1500,
    landlord_id: 'uuid'
});
```

### PHP
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://your-domain.com/api/',
    'headers' => [
        'Authorization' => 'Bearer your-token',
        'Content-Type' => 'application/json'
    ]
]);

// Get properties
$response = $client->get('properties');
$properties = json_decode($response->getBody(), true);

// Create property
$response = $client->post('properties', [
    'json' => [
        'name' => 'New Property',
        'type' => 'house',
        'rent' => 1500,
        'landlord_id' => 'uuid'
    ]
]);
```

## 📞 Support

For API support and questions:
- **Email**: api-support@foxesrentals.com
- **Documentation**: https://docs.foxesrentals.com
- **Status Page**: https://status.foxesrentals.com

---

**Last Updated**: January 15, 2024  
**API Version**: v1.0.0  
**Base URL**: `https://your-domain.com/api`
