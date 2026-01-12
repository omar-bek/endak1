# API Documentation - Endak Platform

## Base URL

-   **Development**: `http://127.0.0.1:8000/api`
-   **Production**: `https://endak.net/api`

## Authentication

معظم الـ endpoints تتطلب authentication باستخدام API token في الـ header:

```
Authorization: Bearer {token}
```

---

## 📋 Table of Contents

1. [Public Endpoints](#public-endpoints)
2. [Authentication APIs](#authentication-apis)
3. [Categories APIs](#categories-apis)
4. [Category Fields APIs](#category-fields-apis)
5. [Services APIs](#services-apis)
6. [Service Offers APIs](#service-offers-apis)
7. [Notifications APIs](#notifications-apis)
8. [Messages APIs](#messages-apis)

---

## Public Endpoints

### 1. API Status

```
GET /api/v1/status
```

**Response:**

```json
{
    "success": true,
    "message": "API is up",
    "timestamp": "2024-01-01T00:00:00+00:00"
}
```

---

## Authentication APIs

### 1. Register

```
POST /api/register
POST /api/v1/auth/register
```

**Request Body:**

```json
{
    "name": "User Name",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "0123456789",
    "user_type": "customer"
}
```

**Response:**

```json
{
  "success": true,
  "message": "تم إنشاء الحساب بنجاح",
  "data": {
    "token": "api_token_here",
    "user": { ... }
  }
}
```

### 2. Login

```
POST /api/login
POST /api/v1/auth/login
```

**Request Body:**

```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "token": "api_token_here",
    "user": { ... }
  }
}
```

### 3. Logout (Requires Auth)

```
POST /api/v1/auth/logout
```

**Headers:**

```
Authorization: Bearer {token}
```

### 4. Get Profile (Requires Auth)

```
GET /api/v1/auth/profile
```

### 5. Update Profile (Requires Auth)

```
PUT /api/v1/auth/profile
```

**Request Body:**

```json
{
    "name": "Updated Name",
    "phone": "0987654321",
    "bio": "User bio",
    "user_type": "provider"
}
```

---

## Categories APIs

### 1. Get All Categories

```
GET /api/v1/categories
```

**Query Parameters:**

-   None

**Response:**

```json
{
  "success": true,
  "message": "success",
  "data": [
    {
      "id": 1,
      "name": "نقل الأثاث",
      "name_en": "Furniture Moving",
      "slug": "furniture-moving",
      "children": [...],
      "services_count": 10
    }
  ]
}
```

### 2. Get Category Details

```
GET /api/v1/categories/{slug}/details
```

**Query Parameters:**

-   `sub_category_id` (optional)
-   `search` (optional)
-   `city_id` (optional)
-   `per_page` (optional, default: 12)

**Response:**

```json
{
  "success": true,
  "data": {
    "category": { ... },
    "services": { ... }
  }
}
```

### 3. Get Subcategories

```
GET /api/v1/categories/{id}/subcategories
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name_ar": "نقل أثاث منزلي",
            "name_en": "Home Furniture Moving",
            "category_id": 1
        }
    ]
}
```

### 4. Get Cities Available in Category

```
GET /api/v1/categories/{id}/cities
```

**Query Parameters:**

-   `search` (optional) - البحث في أسماء المدن

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name_ar": "الرياض",
            "name_en": "Riyadh",
            "slug": "riyadh"
        },
        {
            "id": 2,
            "name_ar": "جدة",
            "name_en": "Jeddah",
            "slug": "jeddah"
        }
    ]
}
```

**Description:**

-   يعيد قائمة المدن المتاحة (النشطة) المرتبطة بفئة معينة
-   المدن مرتبة حسب `sort_order` ثم حسب الاسم
-   يمكن البحث في أسماء المدن باستخدام معامل `search`

### 5. Get Request Data (NEW)

```
GET /api/v1/categories/{id}/request-data
```

**Query Parameters:**

-   `sub_category_id` (optional)

**Response:**

```json
{
  "success": true,
  "data": {
    "category": { ... },
    "has_sub_categories": true,
    "sub_categories": [ ... ],
    "selected_sub_category": { ... },
    "cities": [ ... ],
    "fields": [ ... ],
    "grouped_fields": { ... }
  }
}
```

---

## Category Fields APIs

### 1. Get Category Fields

```
GET /api/v1/categories/{category}/fields
```

**Query Parameters:**

-   `sub_category_id` (optional)

**Response:**

```json
{
  "success": true,
  "data": {
    "category": { ... },
    "fields": [
      {
        "id": 1,
        "name": "from_city",
        "name_ar": "من المدينة",
        "name_en": "From City",
        "type": "select",
        "options": ["الرياض", "جدة"],
        "is_required": true,
        "is_repeatable": false
      }
    ]
  }
}
```

### 2. Get Grouped Fields

```
GET /api/v1/categories/{category}/fields/grouped
```

**Query Parameters:**

-   `sub_category_id` (optional)

**Response:**

```json
{
  "success": true,
  "data": {
    "category": { ... },
    "grouped_fields": {
      "location": [ ... ],
      "default": [ ... ]
    }
  }
}
```

### 3. Get Single Field

```
GET /api/v1/categories/{category}/fields/{field}
```

---

## Services APIs

### 1. Get All Services (Public)

```
GET /api/v1/services
```

**Query Parameters:**

-   `category_id` (optional)
-   `sub_category_id` (optional)
-   `city_id` (optional)
-   `user_id` (optional)
-   `search` (optional)
-   `per_page` (optional, default: 12)

**Response:**

```json
{
  "success": true,
  "data": {
    "data": [ ... ],
    "current_page": 1,
    "per_page": 12,
    "total": 100
  }
}
```

### 2. Get Service Details (Public)

```
GET /api/v1/services/{service}
```

### 3. Get My Services (Requires Auth)

```
GET /api/v1/services/me
```

**Query Parameters:**

-   `per_page` (optional, default: 12)

### 4. Create Service (Requires Auth)

```
POST /api/v1/services
```

**Request Body:**

```json
{
    "title": "Service Title",
    "description": "Service Description",
    "price": 100.0,
    "category_id": 1,
    "sub_category_id": 1,
    "city_id": 1,
    "custom_fields": {
        "from_city": "الرياض",
        "to_city": "جدة"
    }
}
```

### 5. Update Service (Requires Auth)

```
PUT /api/v1/services/{service}
```

**Request Body:** (all fields optional)

```json
{
    "title": "Updated Title",
    "description": "Updated Description",
    "is_active": true
}
```

### 6. Delete Service (Requires Auth)

```
DELETE /api/v1/services/{service}
```

---

## Service Offers APIs

### 1. Get Offers (Requires Auth)

```
GET /api/v1/offers
```

**Query Parameters:**

-   `per_page` (optional, default: 15)

**Note:**

-   إذا كان المستخدم مزود خدمة: يعرض عروضه
-   إذا كان المستخدم عادي: يعرض العروض على خدماته

### 2. Create Offer (Requires Auth - Provider Only)

```
POST /api/v1/services/{service}/offers
```

**Request Body:**

```json
{
    "price": 500.0,
    "notes": "عرض خاص",
    "expires_at": "2024-12-31 23:59:59"
}
```

### 3. Accept Offer (Requires Auth)

```
POST /api/v1/offers/{offer}/accept
```

**Note:** فقط صاحب الخدمة يمكنه قبول العرض

### 4. Reject Offer (Requires Auth)

```
POST /api/v1/offers/{offer}/reject
```

**Note:** فقط صاحب الخدمة يمكنه رفض العرض

### 5. Deliver Offer (Requires Auth - Provider Only)

```
POST /api/v1/offers/{offer}/deliver
```

**Note:** فقط مزود الخدمة يمكنه تحديد العرض كمُسلم

### 6. Review Offer (Requires Auth)

```
POST /api/v1/offers/{offer}/review
```

**Request Body:**

```json
{
    "rating": 5,
    "review": "خدمة ممتازة"
}
```

**Note:** فقط صاحب الخدمة يمكنه تقييم العرض بعد التسليم

---

## Notifications APIs

### 1. Get Notifications (Requires Auth)

```
GET /api/v1/notifications
```

**Query Parameters:**

-   `per_page` (optional, default: 20)

**Response:**

```json
{
  "success": true,
  "data": {
    "notifications": [ ... ],
    "unread_count": 5
  }
}
```

### 2. Mark Notification as Read (Requires Auth)

```
POST /api/v1/notifications/{notification}/read
```

### 3. Mark All as Read (Requires Auth)

```
POST /api/v1/notifications/mark-all-read
```

### 4. Delete Notification (Requires Auth)

```
DELETE /api/v1/notifications/{notification}
```

---

## Messages APIs

### 1. Get Conversations (Requires Auth)

```
GET /api/v1/messages
```

**Response:** قائمة بآخر رسالة من كل محادثة

### 2. Get Conversation with User (Requires Auth)

```
GET /api/v1/messages/{user}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "partner": {
      "id": 1,
      "name": "User Name",
      "avatar": "..."
    },
    "messages": [ ... ]
  }
}
```

### 3. Send Message (Requires Auth)

```
POST /api/v1/messages
```

**Request Body:**

```json
{
    "receiver_id": 2,
    "content": "Hello!",
    "service_id": 1,
    "service_offer_id": 1
}
```

### 4. Delete Message (Requires Auth)

```
DELETE /api/v1/messages/{message}
```

**Note:** فقط المرسل يمكنه حذف الرسالة

---

## Error Responses

جميع الـ APIs ترجع نفس تنسيق الـ error:

```json
{
    "success": false,
    "message": "رسالة الخطأ",
    "errors": {
        "field_name": ["خطأ في الحقل"]
    }
}
```

### Status Codes:

-   `200` - Success
-   `201` - Created
-   `400` - Bad Request
-   `401` - Unauthorized
-   `403` - Forbidden
-   `404` - Not Found
-   `422` - Validation Error
-   `500` - Server Error

---

## Testing Checklist

### ✅ Public Endpoints

-   [ ] GET /api/v1/status
-   [ ] GET /api/v1/categories
-   [ ] GET /api/v1/categories/{slug}/details
-   [ ] GET /api/v1/categories/{id}/subcategories
-   [ ] GET /api/v1/categories/{id}/cities
-   [ ] GET /api/v1/categories/{id}/request-data
-   [ ] GET /api/v1/categories/{category}/fields
-   [ ] GET /api/v1/categories/{category}/fields/grouped
-   [ ] GET /api/v1/categories/{category}/fields/{field}
-   [ ] GET /api/v1/services
-   [ ] GET /api/v1/services/{service}

### ✅ Authentication

-   [ ] POST /api/register
-   [ ] POST /api/login
-   [ ] POST /api/v1/auth/logout (with token)
-   [ ] GET /api/v1/auth/profile (with token)
-   [ ] PUT /api/v1/auth/profile (with token)

### ✅ Services (Auth Required)

-   [ ] GET /api/v1/services/me (with token)
-   [ ] POST /api/v1/services (with token)
-   [ ] PUT /api/v1/services/{service} (with token)
-   [ ] DELETE /api/v1/services/{service} (with token)

### ✅ Service Offers (Auth Required)

-   [ ] GET /api/v1/offers (with token)
-   [ ] POST /api/v1/services/{service}/offers (with token - provider)
-   [ ] POST /api/v1/offers/{offer}/accept (with token)
-   [ ] POST /api/v1/offers/{offer}/reject (with token)
-   [ ] POST /api/v1/offers/{offer}/deliver (with token - provider)
-   [ ] POST /api/v1/offers/{offer}/review (with token)

### ✅ Notifications (Auth Required)

-   [ ] GET /api/v1/notifications (with token)
-   [ ] POST /api/v1/notifications/{notification}/read (with token)
-   [ ] POST /api/v1/notifications/mark-all-read (with token)
-   [ ] DELETE /api/v1/notifications/{notification} (with token)

### ✅ Messages (Auth Required)

-   [ ] GET /api/v1/messages (with token)
-   [ ] GET /api/v1/messages/{user} (with token)
-   [ ] POST /api/v1/messages (with token)
-   [ ] DELETE /api/v1/messages/{message} (with token)

---

## Notes

1. جميع الـ endpoints تستخدم `BaseApiController` الذي يوفر:

    - معالجة الأخطاء الموحدة
    - تنسيق JSON موحد
    - Logging تلقائي

2. الـ authentication يتم عبر middleware `api.token`

3. جميع الـ IDs في الـ routes يجب أن تكون أرقام (using `whereNumber()`)

4. الـ pagination متاح في معظم الـ endpoints عبر `per_page` parameter

5. الـ search متاح في بعض الـ endpoints عبر `search` parameter
