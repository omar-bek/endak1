@extends('layouts.admin')

@section('title', 'إعدادات النظام')
@section('page-title', 'إعدادات النظام')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- إعدادات مزود الخدمة -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-cog"></i> إعدادات مزود الخدمة
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.system-settings.provider') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="provider_max_categories" class="form-label">الحد الأقصى للأقسام</label>
                                <input type="number" name="provider_max_categories" id="provider_max_categories" 
                                       class="form-control" min="1" max="10" 
                                       value="{{ \App\Models\SystemSetting::get('provider_max_categories', 3) }}" required>
                                <small class="text-muted">عدد الأقسام التي يمكن لمزود الخدمة العمل فيها</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="provider_max_cities" class="form-label">الحد الأقصى للمدن</label>
                                <input type="number" name="provider_max_cities" id="provider_max_cities" 
                                       class="form-control" min="1" max="20" 
                                       value="{{ \App\Models\SystemSetting::get('provider_max_cities', 5) }}" required>
                                <small class="text-muted">عدد المدن التي يمكن لمزود الخدمة العمل فيها</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="provider_verification_required" id="provider_verification_required" 
                                           class="form-check-input" value="1"
                                           {{ \App\Models\SystemSetting::get('provider_verification_required', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="provider_verification_required">
                                        يتطلب التحقق من مزود الخدمة
                                    </label>
                                </div>
                                <small class="text-muted">هل يتطلب التحقق من مزود الخدمة قبل تفعيل الحساب</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="provider_auto_approve" id="provider_auto_approve" 
                                           class="form-check-input" value="1"
                                           {{ \App\Models\SystemSetting::get('provider_auto_approve', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="provider_auto_approve">
                                        الموافقة التلقائية على مزودي الخدمة
                                    </label>
                                </div>
                                <small class="text-muted">هل يتم الموافقة التلقائية على مزودي الخدمة</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جميع الإعدادات -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs"></i> جميع إعدادات النظام
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.system-settings.update') }}">
                    @csrf
                    @method('PUT')
                    
                    @foreach($settings as $group => $groupSettings)
                        <div class="mb-4">
                            <h6 class="text-primary">{{ ucfirst($group) }}</h6>
                            <div class="row">
                                @foreach($groupSettings as $setting)
                                    <div class="col-md-6 mb-3">
                                        <label for="setting_{{ $setting->id }}" class="form-label">
                                            {{ $setting->description ?? $setting->key }}
                                        </label>
                                        
                                        @if($setting->type === 'boolean')
                                            <div class="form-check">
                                                <input type="checkbox" name="settings[{{ $setting->key }}][value]" 
                                                       id="setting_{{ $setting->id }}" class="form-check-input" value="1"
                                                       {{ filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="setting_{{ $setting->id }}">
                                                    تفعيل
                                                </label>
                                            </div>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" name="settings[{{ $setting->key }}][value]" 
                                                   id="setting_{{ $setting->id }}" class="form-control" 
                                                   value="{{ $setting->value }}">
                                        @elseif($setting->type === 'json')
                                            <textarea name="settings[{{ $setting->key }}][value]" 
                                                      id="setting_{{ $setting->id }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}][value]" 
                                                   id="setting_{{ $setting->id }}" class="form-control" 
                                                   value="{{ $setting->value }}">
                                        @endif
                                        
                                        <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                        <small class="text-muted">{{ $setting->description }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ جميع الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- معلومات النظام -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> معلومات النظام
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>إصدار Laravel:</strong>
                    <span class="text-muted">{{ app()->version() }}</span>
                </div>
                <div class="mb-3">
                    <strong>بيئة التشغيل:</strong>
                    <span class="badge bg-{{ app()->environment() === 'production' ? 'danger' : 'warning' }}">
                        {{ app()->environment() }}
                    </span>
                </div>
                <div class="mb-3">
                    <strong>اللغة الافتراضية:</strong>
                    <span class="text-muted">{{ config('app.locale') }}</span>
                </div>
                <div class="mb-3">
                    <strong>المنطقة الزمنية:</strong>
                    <span class="text-muted">{{ config('app.timezone') }}</span>
                </div>
            </div>
        </div>

        <!-- إحصائيات مزودي الخدمة -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> إحصائيات مزودي الخدمة
                </h5>
            </div>
            <div class="card-body">
                @php
                    $totalProviders = \App\Models\User::where('user_type', 'provider')->count();
                    $verifiedProviders = \App\Models\ProviderProfile::where('is_verified', true)->count();
                    $activeProviders = \App\Models\ProviderProfile::where('is_active', true)->count();
                    $completeProfiles = \App\Models\ProviderProfile::whereNotNull('bio')
                        ->whereNotNull('phone')
                        ->whereNotNull('address')
                        ->count();
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>إجمالي مزودي الخدمة:</span>
                        <strong>{{ $totalProviders }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>الموثقون:</span>
                        <strong>{{ $verifiedProviders }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>النشطون:</span>
                        <strong>{{ $activeProviders }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>الملفات المكتملة:</span>
                        <strong>{{ $completeProfiles }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
