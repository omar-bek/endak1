# دليل اختبار الـ API على الـ Local

## المشكلة
الـ API يعمل على السيرفر (endak.net) لكن لا يعمل على الـ local.

## الحلول المطبقة

### 1. تنظيف الـ Cache
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 2. تشغيل الـ Server
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 3. اختبار الـ API

#### GET Request (للحصول على معلومات الـ Endpoint)
```
GET http://127.0.0.1:8000/api/login
```

#### POST Request (لتسجيل الدخول)
```
POST http://127.0.0.1:8000/api/login
Headers:
  Content-Type: application/json
  Accept: application/json
Body (raw JSON):
{
  "email": "user@example.com",
  "password": "password123"
}
```

## الـ Routes المتاحة

### Public Routes (بدون authentication)
- `GET /api/login` - معلومات عن endpoint تسجيل الدخول
- `POST /api/login` - تسجيل الدخول
- `GET /api/register` - معلومات عن endpoint التسجيل
- `POST /api/register` - إنشاء حساب جديد

### V1 Routes
- `GET /api/v1/auth/login` - معلومات عن endpoint تسجيل الدخول
- `POST /api/v1/auth/login` - تسجيل الدخول
- `GET /api/v1/auth/register` - معلومات عن endpoint التسجيل
- `POST /api/v1/auth/register` - إنشاء حساب جديد

## ملاحظات مهمة

1. **تأكد من استخدام POST method** لتسجيل الدخول
2. **أضف Headers** `Content-Type: application/json` و `Accept: application/json`
3. **تأكد من وجود المستخدم** في قاعدة البيانات
4. **تأكد من صحة كلمة المرور** - يجب أن تكون محفوظة بشكل hashed

## استكشاف الأخطاء

### إذا كان الـ response HTML بدلاً من JSON:
- تأكد من أن الـ middleware `ForceJsonResponse` يعمل
- تحقق من الـ headers في Postman

### إذا كان الـ route غير موجود:
- قم بتشغيل `php artisan route:clear`
- تحقق من ملف `routes/api.php`

### إذا كان تسجيل الدخول يفشل:
- تحقق من وجود المستخدم في قاعدة البيانات
- تحقق من أن كلمة المرور محفوظة بشكل hashed
- تحقق من الـ logs في `storage/logs/laravel.log`


