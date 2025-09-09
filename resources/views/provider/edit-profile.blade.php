@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي - مزود الخدمة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-edit"></i> تعديل الملف الشخصي
                    </h3>
                    <p class="mb-0 mt-2">قم بتحديث معلوماتك الشخصية والأقسام والمدن</p>
                </div>

                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('provider.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- المعلومات الأساسية -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="text-primary mb-4">
                                    <i class="fas fa-user"></i> المعلومات الأساسية
                                </h4>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bio" class="form-label">نبذة عنك <span class="text-danger">*</span></label>
                                <textarea name="bio" id="bio" class="form-control @error('bio') is-invalid @enderror"
                                          rows="4" placeholder="اكتب نبذة مختصرة عن خبراتك ومهاراتك...">{{ old('bio', $profile ? $profile->bio : '') }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $profile ? $profile->phone : '') }}" placeholder="مثال: 0501234567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $profile ? $profile->address : '') }}" placeholder="عنوانك الكامل">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- الأقسام -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="text-primary mb-4">
                                    <i class="fas fa-folder"></i> الأقسام التي تعمل فيها
                                    <small class="text-muted">(حد أقصى {{ $maxCategories }} أقسام)</small>
                                </h4>

                                @error('categories')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="row">
                                    @foreach($categories as $category)
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                @php
                                                    $selectedCategories = $profile ? $profile->activeCategories()->pluck('category_id')->toArray() : [];
                                                @endphp
                                                <input class="form-check-input" type="checkbox"
                                                       name="categories[]" value="{{ $category->id }}"
                                                       id="category_{{ $category->id }}"
                                                       {{ in_array($category->id, old('categories', $selectedCategories)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category_{{ $category->id }}">
                                                    <i class="{{ $category->icon }} text-primary"></i>
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- المدن -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="text-primary mb-4">
                                    <i class="fas fa-map-marker-alt"></i> المدن التي تعمل فيها
                                    <small class="text-muted">(حد أقصى {{ $maxCities }} مدن)</small>
                                </h4>

                                @error('cities')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="row">
                                    @foreach($cities as $city)
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                @php
                                                    $selectedCities = $profile ? $profile->activeCities()->pluck('city_id')->toArray() : [];
                                                @endphp
                                                <input class="form-check-input" type="checkbox"
                                                       name="cities[]" value="{{ $city->id }}"
                                                       id="city_{{ $city->id }}"
                                                       {{ in_array($city->id, old('cities', $selectedCities)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="city_{{ $city->id }}">
                                                    <i class="fas fa-map-marker-alt text-info"></i>
                                                    {{ $city->name_ar }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-save"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('provider.profile') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> العودة للملف الشخصي
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
