@extends('layouts.app')

@section('title', 'الشروط والأحكام')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, #eafaf9 0%, #ffffff 100%);">
    <div class="container">
        <div class="terms-card card shadow-lg rounded-4 border-0 mx-auto">
            <div class="card-body p-5">
                <h2 class="text-center mb-4" style="color: #007f7f;">
                    <i class="fas fa-file-contract me-2 text-warning"></i> الشروط والأحكام
                </h2>
                <p class="text-secondary text-center mb-5">
                    نرحب بك في <strong class="text-primary">Endak</strong>، يُرجى قراءة هذه الشروط بعناية قبل استخدام الموقع.
                    استمرارك في التصفح أو استخدام خدماتنا يعني موافقتك الكاملة على هذه البنود والسياسات.
                </p>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-user-shield text-warning me-2"></i> أولاً: شروط استخدام الموقع</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• يُحظر استخدام الموقع لأي غرض غير قانوني أو يضر بالآخرين.</li>
                        <li>• يُمنع جمع أي بيانات من الموقع أو مستخدميه بدون إذن مسبق.</li>
                        <li>• يجب استخدام الموقع بطريقة تحافظ على احترام المستخدمين الآخرين والالتزام بالقوانين المحلية.</li>
                        <li>• لا يجوز استخدام أي أدوات أو برمجيات لاختراق الموقع أو تعطيله.</li>
                    </ul>
                </div>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-id-card text-warning me-2"></i> ثانيًا: إنشاء الحساب واستخدامه</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• يتحمل المستخدم مسؤولية سرية بيانات حسابه وعدم مشاركتها مع الآخرين.</li>
                        <li>• يُسمح بإنشاء حساب واحد فقط لكل مستخدم.</li>
                        <li>• في حالة اكتشاف أي نشاط مريب، يحق لإدارة الموقع إيقاف الحساب مؤقتًا أو دائمًا.</li>
                        <li>• يجب أن تكون المعلومات المقدمة أثناء التسجيل صحيحة ومحدثة.</li>
                    </ul>
                </div>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-handshake text-warning me-2"></i> ثالثًا: الخدمات والتعاملات</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• الموقع يعمل كوسيط بين مقدمي الخدمات والمستخدمين فقط.</li>
                        <li>• لا يتحمل <strong>Endak</strong> أي مسؤولية عن الاتفاقات أو التعاملات المالية بين الأطراف.</li>
                        <li>• يحق لإدارة الموقع إزالة أي خدمة أو إعلان يخالف القوانين أو السياسات العامة.</li>
                        <li>• الأسعار والعروض تخضع للتغيير حسب مقدم الخدمة.</li>
                    </ul>
                </div>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-lock text-warning me-2"></i> رابعًا: الخصوصية وحماية البيانات</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• يلتزم الموقع بحماية بيانات المستخدمين وعدم مشاركتها مع أي طرف ثالث بدون موافقة.</li>
                        <li>• يتم استخدام المعلومات فقط لتحسين تجربة المستخدم والخدمات المقدمة.</li>
                        <li>• قد يستخدم الموقع ملفات تعريف الارتباط (Cookies) لتخصيص المحتوى.</li>
                        <li>• يمكنك مراجعة سياسة الخصوصية لمعرفة المزيد حول كيفية إدارة البيانات.</li>
                    </ul>
                </div>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-balance-scale text-warning me-2"></i> خامسًا: حدود المسؤولية</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• لا يتحمل الموقع أي مسؤولية عن الأضرار الناتجة عن سوء استخدام المستخدم للخدمة.</li>
                        <li>• المحتوى المنشور يعبر عن رأي صاحبه فقط.</li>
                        <li>• لا يُعد الموقع مسؤولًا عن انقطاع الخدمة أو الأعطال المؤقتة الخارجة عن إرادته.</li>
                    </ul>
                </div>

                <div class="mb-5">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-sync text-warning me-2"></i> سادسًا: التعديلات والتحديثات</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• نحتفظ بالحق في تعديل أو تحديث هذه الشروط في أي وقت دون إشعار مسبق.</li>
                        <li>• يُنصح المستخدم بمراجعة الصفحة من حين لآخر لمعرفة التغييرات.</li>
                        <li>• استمرار استخدامك للموقع بعد التعديلات يعني موافقتك عليها.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold text-black mb-3"><i class="fas fa-gavel text-warning me-2"></i> سابعًا: القوانين العامة</h4>
                    <ul class="list-unstyled lh-lg text-muted">
                        <li>• تخضع هذه الشروط لقوانين جمهورية مصر العربية.</li>
                        <li>• في حال حدوث نزاع، تكون المحاكم المحلية هي المختصة بالنظر فيه.</li>
                        <li>• يُعتبر أي بند غير صالح أو غير قابل للتنفيذ قابلاً للفصل دون أن يؤثر على بقية البنود.</li>
                    </ul>
                </div>

                <p class="text-center mt-5 fw-bold" style="color: #007f7f;">
                    باستخدامك لموقع <strong class="text-warning">Endak</strong>، فإنك تقر بقراءة الشروط والأحكام وفهمها والموافقة عليها بالكامل.
                </p>
            </div>
        </div>
    </div>
</section>

<style>
@media (min-width: 992px) {
    .terms-card {
        margin-top: 60px;
    }
}

.terms-card {
    background: #ffffff;
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
}

.terms-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    padding: 2px;
    background: linear-gradient(135deg, #00a6a6, #f2c94c);
    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
}

.terms-card:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(0, 166, 166, 0.2);
}
</style>
@endsection
