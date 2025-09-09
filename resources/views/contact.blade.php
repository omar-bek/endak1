@extends('layouts.app')

@section('title', 'اتصل بنا')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h1 class="fw-bold">اتصل بنا</h1>
            <p class="text-muted">نحن هنا لمساعدتك في أي استفسار</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">أرسل لنا رسالة</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">الاسم الكامل *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">البريد الإلكتروني *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">الموضوع *</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">الرسالة *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>إرسال الرسالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">معلومات التواصل</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">البريد الإلكتروني</h6>
                                <small class="text-muted">info@endak.com</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">الهاتف</h6>
                                <small class="text-muted">+966 50 123 4567</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">العنوان</h6>
                                <small class="text-muted">الرياض، المملكة العربية السعودية</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">ساعات العمل</h6>
                                <small class="text-muted">الأحد - الخميس: 8 ص - 6 م</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">تابعنا</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">الأسئلة الشائعة</h2>
            <p class="text-muted">إجابات على أكثر الأسئلة شيوعاً</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                كيف يمكنني التسجيل في المنصة؟
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يمكنك التسجيل بسهولة من خلال النقر على زر "التسجيل" في أعلى الصفحة وملء النموذج بالمعلومات المطلوبة.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                كيف يمكنني إضافة خدمة جديدة؟
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                بعد تسجيل الدخول، يمكنك الذهاب إلى لوحة التحكم وإضافة خدمة جديدة من خلال النقر على "إضافة خدمة".
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                ما هي طرق الدفع المتاحة؟
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نحن نقبل جميع طرق الدفع الرئيسية مثل البطاقات الائتمانية والمدى والتحويل البنكي.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
