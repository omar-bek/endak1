@extends('layouts.app')

@section('title', 'التسجيل')

@section('content')
<div class="auth-container register-mode" id="authContainer">
    <div class="auth-card register-card">
        <div class="side-panel right-panel">
            <div class="content text-center">
                <i class="fas fa-user-plus fa-3x text-warning mb-3"></i>
                <h2>مرحبًا بك في Endak!</h2>
                <p>هل لديك حساب بالفعل؟</p>
                <button class="btn btn-outline-light mt-3 switch-btn" id="switchToLogin">تسجيل الدخول</button>
            </div>
        </div>

        <div class="form-section fadeInLeft">
            <div class="logo mb-4 text-center">
                <a href="{{ route('home') }}" class="text-decoration-none text-dark fs-3 fw-bold">
                    <img src="{{ asset('home.png') }}" alt="Endak Logo" class="me-2" style="height: 50px; width: auto;">Endak
                </a>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3 position-relative">
                    <i class="fas fa-user input-icon text-secondary"></i>
                    <input type="text" class="form-control" name="name" placeholder="الاسم الكامل" required>
                </div>

                <div class="mb-3 position-relative">
                    <i class="fas fa-mobile-alt input-icon text-secondary"></i>
                    <input type="tel" class="form-control" name="phone" placeholder="رقم الهاتف" required>
                </div>

                <div class="mb-3 position-relative">
                    <i class="fas fa-lock input-icon text-secondary"></i>
                    <input type="password" class="form-control" name="password" placeholder="كلمة المرور" required>
                </div>

                <div class="mb-3 position-relative">
                    <i class="fas fa-key input-icon text-secondary"></i>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
                </div>

                <div class="mb-3 position-relative">
                    <i class="fas fa-users input-icon text-secondary"></i>
                    <select class="form-control" name="user_type" required>
                        <option value="" disabled selected>اختر نوع الحساب</option>
                        <option value="customer">مستخدم عادي</option>
                        <option value="provider">مزود خدمة</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-login w-100">إنشاء الحساب</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("switchToLogin").addEventListener("click", function() {
    window.location.href = "{{ route('login') }}";
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
body {
    font-family: 'Cairo', sans-serif;
    background: #f5f6fa;
    overflow-x: hidden;
}
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-card {
    display: flex;
    flex-direction: row-reverse;
    width: 900px;
    max-width: 95%;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    animation: slideInRight 0.8s ease;
}
.side-panel {
    width: 45%;
    background: linear-gradient(135deg, #2f5c69, #3c7d8b);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
}
.side-panel .btn {
    border-radius: 30px;
    padding: 0.6rem 1.5rem;
    border: 2px solid #fff;
    color: #fff;
    transition: all 0.3s ease;
}
.side-panel .btn:hover {
    background: #f3a446;
    border-color: #f3a446;
}
.form-section {
    width: 55%;
    padding: 3rem;
}
.position-relative {
    position: relative;
}
.input-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #999;
}
.form-control {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 0.75rem 2.8rem 0.75rem 1rem;
    font-size: 1rem;
}
.form-control::placeholder {
    color: #aaa;
}
.form-control:focus {
    border-color: #2f5c69;
    box-shadow: 0 0 5px rgba(47, 92, 105, 0.3);
    outline: none;
}
.btn-login {
    background: linear-gradient(90deg, #2f5c69, #3c7d8b);
    border: none;
    border-radius: 30px;
    padding: 0.75rem;
    color: #fff;
    font-weight: 600;
    transition: transform 0.3s, background 0.3s;
}
.btn-login:hover {
    transform: translateY(-3px);
    background: #f3a446;
}
@keyframes slideInRight {
    from {opacity: 0; transform: translateX(100px);}
    to {opacity: 1; transform: translateX(0);}
}
@media (max-width: 768px) {
    .auth-card {
        flex-direction: column;
        width: 95%;
        animation: slideDown 0.8s ease;
    }
    .side-panel {
        width: 100%;
        border-radius: 20px 20px 0 0;
    }
    .form-section {
        width: 100%;
    }
    @keyframes slideDown {
        from {opacity: 0; transform: translateY(-80px);}
        to {opacity: 1; transform: translateY(0);}
    }
}
</style>
@endsection
