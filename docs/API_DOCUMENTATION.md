# FarmMarket API Documentation

## Overview
Complete RESTful API for mobile app integration with the same database and business logic as the web platform.

## Base URL
```
http://127.0.0.1:8000/api/v1
```

## Authentication
All endpoints except public ones require Bearer token authentication.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## API Endpoints

### Authentication Endpoints

#### Register User
```
POST /api/v1/auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+255123456789",
    "role": "farmer|buyer",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+255123456789",
            "role": "farmer",
            "is_verified": false,
            "created_at": "2024-04-14T10:30:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "token_type": "Bearer"
    }
}
```

#### Login User
```
POST /api/v1/auth/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
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
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+255123456789",
            "role": "farmer",
            "is_verified": true,
            "created_at": "2024-04-14T10:30:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "token_type": "Bearer"
    }
}
```

#### Get Profile
```
GET /api/v1/auth/profile
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+255123456789",
            "address": "123 Main St, City",
            "role": "farmer",
            "is_verified": true,
            "verification_status": "verified",
            "created_at": "2024-04-14T10:30:00.000000Z"
        },
        "verification": {...},
        "crops_count": 15,
        "orders_count": 8
    }
}
```

#### Update Profile
```
PUT /api/v1/auth/profile
```

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "phone": "+255123456780",
    "address": "456 Updated St, City"
}
```

#### Refresh Token
```
POST /api/v1/auth/refresh
```

#### Logout
```
POST /api/v1/auth/logout
```

### Crops Endpoints

#### Get All Crops (Public)
```
GET /api/v1/crops
```

**Query Parameters:**
- `category` (optional): Filter by category ID
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `search` (optional): Search in name/description
- `sort_by` (optional): Sort field (created_at, price_per_unit, name)
- `sort_order` (optional): asc or desc
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "crops": [...],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 45,
            "last_page": 3
        }
    }
}
```

#### Get Crop Details
```
GET /api/v1/crops/{id}
```

#### Get Categories
```
GET /api/v1/categories
```

#### Get My Crops (Farmers Only)
```
GET /api/v1/my-crops
```

**Query Parameters:**
- `available` (optional): Filter by availability
- `category` (optional): Filter by category ID

#### Create Crop (Farmers Only)
```
POST /api/v1/crops
```

**Request Body:**
```json
{
    "name": "Fresh Tomatoes",
    "category_id": 1,
    "description": "Fresh, organic tomatoes from local farm",
    "price_per_unit": 5.50,
    "quantity_available": 100,
    "unit": "kg",
    "location": "Dar es Salaam",
    "is_available": true,
    "images": [File objects]
}
```

#### Update Crop (Farmers Only)
```
PUT /api/v1/crops/{id}
```

#### Delete Crop (Farmers Only)
```
DELETE /api/v1/crops/{id}
```

### Orders Endpoints

#### Get My Orders
```
GET /api/v1/orders
```

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, shipped, delivered, cancelled)
- `sort_by` (optional): Sort field
- `sort_order` (optional): asc or desc
- `per_page` (optional): Items per page

#### Create Order
```
POST /api/v1/orders
```

**Request Body:**
```json
{
    "items": [
        {
            "crop_id": 1,
            "quantity": 5,
            "price_per_unit": 5.50
        }
    ],
    "delivery_address": "123 Delivery St, City",
    "payment_method": "bank_transfer",
    "notes": "Please deliver in the morning"
}
```

#### Get Order Details
```
GET /api/v1/orders/{id}
```

#### Update Order Status
```
PUT /api/v1/orders/{id}/status
```

**Request Body:**
```json
{
    "status": "confirmed",
    "payment_receipt": "Bank transfer confirmed",
    "tracking_number": "TRK123456",
    "notes": "Order confirmed by farmer"
}
```

### Verification Endpoints

#### Get Verification Status
```
GET /api/v1/verification
```

#### Submit ID Verification
```
POST /api/v1/verification/id
```

**Request Body (multipart/form-data):**
```
id_type: national_id|passport|driving_license
id_number: "123456789"
id_front_image: [File]
id_back_image: [File]
selfie_image: [File]
```

#### Send Phone Verification
```
POST /api/v1/verification/phone/send
```

**Request Body:**
```json
{
    "phone_number": "+255123456789"
}
```

#### Verify Phone Code
```
POST /api/v1/verification/phone/verify
```

**Request Body:**
```json
{
    "phone_verification_code": "123456"
}
```

#### Submit Address Verification
```
POST /api/v1/verification/address
```

**Request Body (multipart/form-data):**
```
verification_document: utility_bill|lease_agreement|bank_statement
address_proof_image: [File]
```

### Chat/Messaging Endpoints

#### Get Chats
```
GET /api/v1/chats
```

#### Create Chat
```
POST /api/v1/chats
```

**Request Body:**
```json
{
    "buyer_id": 2,
    "farmer_id": 1,
    "crop_id": 5,
    "order_id": 10,
    "subject": "Question about tomatoes",
    "message": "Are your tomatoes organic?"
}
```

#### Get Chat Details
```
GET /api/v1/chats/{id}
```

#### Send Message
```
POST /api/v1/chats/{id}/messages
```

**Request Body:**
```json
{
    "content": "Yes, they are certified organic"
}
```

### Notifications Endpoint

#### Get Notifications
```
GET /api/v1/notifications
```

**Response:**
```json
{
    "success": true,
    "data": {
        "notifications": [...],
        "unread_count": 5
    }
}
```

### Statistics Endpoint

#### Get Statistics
```
GET /api/v1/stats
```

**Response (Farmers):**
```json
{
    "success": true,
    "data": {
        "total_crops": 15,
        "active_crops": 12,
        "total_orders": 8,
        "pending_orders": 2,
        "completed_orders": 5,
        "total_revenue": 1250.50
    }
}
```

**Response (Buyers):**
```json
{
    "success": true,
    "data": {
        "total_orders": 8,
        "pending_orders": 2,
        "completed_orders": 5,
        "total_spent": 1250.50
    }
}
```

## Error Responses

All endpoints return consistent error format:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {...}
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `422` - Validation Error
- `500` - Server Error

## Rate Limiting
API implements basic rate limiting to prevent abuse.

## File Uploads
Use multipart/form-data for file uploads with proper MIME types and size limits.

## Testing
Use Postman or similar API testing tools with the provided endpoints.

## Mobile App Integration
The mobile app can use these endpoints to provide full platform functionality including:
- User registration and authentication
- Crop browsing and management
- Order creation and tracking
- Account verification
- Real-time messaging
- Notifications and statistics

All endpoints use the same database models and business logic as the web platform, ensuring data consistency.
