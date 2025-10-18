@extends('layouts.app')

@section('title', 'من نحن')

@section('content')
<style>
  .about-section {
        background: linear-gradient(135deg, #e9f7f6 0%, #fefefe 100%);
        margin-top: 80px;
    }

    @media (max-width: 992px) {
        .about-section {
            margin-top: 0;
        }
    }

    .about-card {
        border: 2px solid #d4af37; 
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        animation: fadeInUp 1.2s ease;
    }

    .about-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 25px rgba(0, 128, 128, 0.15);
    }

    .about-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .about-header h2 {
        color: #007c80; 
        font-weight: 700;
    }

    .about-header i {
        color: #d4af37;
    }

    .about-section h5 {
        color: #007c80;
        font-weight: 600;
        position: relative;
        display: inline-block;
    }

    .about-section h5::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 50%;
        height: 3px;
        background: #d4af37;
        border-radius: 5px;
        transition: width 0.4s ease;
    }

    .about-section h5:hover::after {
        width: 100%;
    }

    .about-section p {
        color: #555;
        line-height: 1.9;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

</style>

<section class="py-5 about-section">
    <div class="container">
        <div class="card about-card shadow-lg border-2 rounded-4 p-5">
            <div class="about-header">
                <h2><i class="fas fa-info-circle me-2"></i>من نحن</h2>
            </div>

            <p class="mb-4">
                <strong>Endak</strong> هي منصة متكاملة تجمع بين العملاء ومقدمي الخدمات في مكان واحد، لتسهيل الوصول إلى كل ما تحتاجه في حياتك اليومية بخطوات بسيطة وآمنة.  
                نحن لا نقدم خدمات رقمية فقط، بل نوصلك مباشرة بمقدمي الخدمات في أرض الواقع — سواء كنت تحتاج إلى <strong>تنظيف، أو صيانة سيارات، أو ديكور، أو أعمال سباكة، أو كهرباء، أو حتى خدمات الحدائق</strong> — نحن نوفر لك الأشخاص المناسبين، في الوقت المناسب.
            </p>

            <h5>رؤيتنا</h5>
            <p>
                أن نكون المنصة الأولى في العالم العربي التي تغيّر مفهوم الخدمات المنزلية والميدانية، من خلال الجمع بين التكنولوجيا والراحة والثقة.  
                هدفنا أن يشعر المستخدم أن كل خدمة تأتيه إلى بابه دون عناء، وأن كل مقدم خدمة يجد بيئة احترافية تسهّل عليه التواصل مع عملائه.
            </p>

            <h5 class="mt-4">رسالتنا</h5>
            <p>
                نعمل على بناء منظومة خدمات حقيقية تعتمد على الثقة والاحترافية.  
                <strong>Endak</strong> ليست مجرد موقع إلكتروني، بل مجتمع تفاعلي يربط بين مقدم الخدمة والعميل بشكل مباشر.  
                العميل يختار الخدمة، يتلقى العروض من مقدمي الخدمات، ويقرر بمن يتعامل — بكل شفافية وسهولة.
            </p>

            <h5 class="mt-4">خدماتنا</h5>
            <p>
                نقدم مجموعة واسعة من الخدمات الميدانية التي تُنفذ على أرض الواقع وليس فقط عبر الإنترنت، ومن أبرزها:
            </p>
            <ul class="text-muted">
                <li>🔹 خدمات التنظيف المنزلية والمكتبية.</li>
                <li>🔹 أعمال الصيانة (كهرباء، سباكة، أجهزة منزلية، سيارات).</li>
                <li>🔹 تصميم وتنفيذ الديكورات الداخلية والخارجية.</li>
                <li>🔹 خدمات النقل والتركيب والصيانة الدورية.</li>
                <li>🔹 أعمال الحدائق والعناية بالمساحات الخضراء.</li>
                <li>🔹 توفير قطع الغيار المطلوبة والتوصيل حتى باب المنزل.</li>
            </ul>

            <h5 class="mt-4">قيمنا</h5>
            <p>
                نؤمن أن النجاح لا يتحقق إلا من خلال <strong>الثقة، الشفافية، الجودة، والالتزام</strong>.  
                لذلك نحرص دائمًا على متابعة مقدمي الخدمات وتقييم أدائهم لضمان أعلى مستوى من الرضا لعملائنا.
            </p>

            <h5 class="mt-4">إخلاء المسؤولية</h5>
            <p>
                <strong>Endak</strong> تسعى لتوفير بيئة آمنة للتعامل بين العملاء ومقدمي الخدمات،  
                ومع ذلك، فإن المنصة غير مسؤولة عن أي تعاملات مالية أو اتفاقات تتم خارج نظامها الرسمي.  
                نوصي دائمًا بالتعامل من خلال المنصة لضمان الحماية والدعم الكامل.
            </p>

            <div class="text-center mt-5">
                <h5 class="text-success mb-3">مع <strong>Endak</strong>، الخدمة توصلك لحد بابك 🚪✨</h5>
                <p class="text-muted">راحة بالك تبدأ بخدمة سهلة وآمنة بين يديك.</p>
            </div>
        </div>
    </div>
</section>
@endsection
