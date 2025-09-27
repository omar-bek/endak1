@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    @if($user->image && file_exists(public_path('storage/' . $user->image)))
                        <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}"
                             class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center bg-primary text-white"
                             style="width: 120px; height: 120px; font-size: 48px; font-weight: bold;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">
                        @if($user->isProvider())
                            <i class="fas fa-tools me-1"></i>مزود خدمة
                        @elseif($user->isCustomer())
                            <i class="fas fa-user me-1"></i>مستخدم
                        @elseif($user->isAdmin())
                            <i class="fas fa-crown me-1"></i>مدير النظام
                        @endif
                    </p>

                    @if($user->phone)
                    <p class="text-muted">
                        <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                    </p>
                    @endif

                    <p class="text-muted">
                        <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                    </p>

                    @if($user->bio)
                    <p class="text-muted">{{ $user->bio }}</p>
                    @endif

                    <p class="text-muted">
                        <small>انضم في {{ $user->created_at->format('Y/m/d') }}</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>تعديل الملف الشخصي
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف *</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <label for="bio" class="form-label">نبذة شخصية</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">الصورة الشخصية</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">الأبعاد المفضلة: 300x300 بكسل. الأنواع المدعومة: JPG, PNG, GIF</small>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($user->image)
                                <div class="mt-2">
                                    <small class="text-muted">الصورة الحالية:</small>
                                    <div class="mt-1">
                                        <img src="{{ asset('storage/' . $user->image) }}" alt="الصورة الحالية"
                                             class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Provider Dashboard (if provider) -->
            @if($user->isProvider())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>لوحة مزود الخدمة
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->hasCompleteProviderProfile())
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="bg-primary text-white rounded p-3">
                                    <h3>{{ $user->services->count() }}</h3>
                                    <p class="mb-0">الخدمات</p>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="bg-success text-white rounded p-3">
                                    <h3>{{ $user->services->where('is_active', true)->count() }}</h3>
                                    <p class="mb-0">الخدمات النشطة</p>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="bg-warning text-white rounded p-3">
                                    {{--  <h3>{{ $user->orders->count() }}</h3>  --}}
                                    <p class="mb-0">الطلبات</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('provider.services.index') }}" class="btn btn-primary">
                                <i class="fas fa-cogs me-2"></i>إدارة الخدمات
                            </a>
                            <a href="{{ route('provider.profile') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user me-2"></i>الملف الشخصي المتقدم
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>يجب إكمال الملف الشخصي أولاً</strong>
                            <p class="mb-0 mt-2">لإدارة الخدمات والعروض، يجب إكمال ملفك الشخصي كمزود خدمة.</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('provider.complete-profile') }}" class="btn btn-warning">
                                <i class="fas fa-user-plus me-2"></i>إكمال الملف الشخصي
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Customer Dashboard (if customer) -->
            @if($user->isCustomer())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>لوحة العميل
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center mb-3">
                            {{--  <div class="bg-info text-white rounded p-3">
                                <h3>{{ $user->orders->count() }}</h3>
                                <p class="mb-0">الطلبات</p>
                            </div>  --}}
                        </div>
                        <div class="col-md-6 text-center mb-3">
                            {{--  <div class="bg-success text-white rounded p-3">
                                <h3>{{ $user->orders->where('status', 'completed')->count() }}</h3>
                                <p class="mb-0">الطلبات المكتملة</p>
                            </div>  --}}
                        </div>
                    </div>

                    <div class="text-center">
                        {{--  <a href="{{ route('customer.orders.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>طلباتي
                        </a>  --}}
                        <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>تصفح الخدمات
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
