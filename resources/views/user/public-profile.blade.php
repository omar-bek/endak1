@extends('layouts.app')

@section('title', 'الملف الشخصي - ' . $user->name)

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user"></i> الملف الشخصي
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            @if($user->image && file_exists(public_path('storage/' . $user->image)))
                                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}"
                                     class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center bg-primary text-white"
                                     style="width: 120px; height: 120px; font-size: 48px; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h3 class="mb-2">{{ $user->name }}</h3>
                            <p class="text-muted">
                                @if($user->isProvider())
                                    <i class="fas fa-tools me-1"></i>مزود خدمة
                                @elseif($user->isCustomer())
                                    <i class="fas fa-user me-1"></i>مستخدم
                                @elseif($user->isAdmin())
                                    <i class="fas fa-crown me-1"></i>مدير النظام
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        @if($user->phone)
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary mb-1">
                                    <i class="fas fa-phone"></i> رقم الهاتف
                                </h6>
                                <p class="text-muted">{{ $user->phone }}</p>
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-1">
                                <i class="fas fa-envelope"></i> البريد الإلكتروني
                            </h6>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                    </div>

                    @if($user->bio)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-info-circle"></i> نبذة
                            </h6>
                            <p class="text-muted">{{ $user->bio }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            انضم في {{ $user->created_at->format('Y-m-d') }}
                        </small>
                    </div>

                    @if(Auth::check() && Auth::id() != $user->id)
                        <div class="mb-3">
                            <a href="{{ route('messages.show', $user->id) }}" 
                               class="btn btn-primary rounded-pill">
                                <i class="fas fa-envelope me-1"></i> إرسال رسالة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- الإحصائيات -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> الإحصائيات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @if($user->isCustomer())
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary">{{ $user->orders()->count() }}</h4>
                                    <small class="text-muted">الطلبات</small>
                                </div>
                            </div>
                        @elseif($user->isProvider())
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary">{{ $user->services()->count() }}</h4>
                                    <small class="text-muted">الخدمات</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-success">{{ $user->offers()->count() }}</h4>
                                    <small class="text-muted">العروض</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
