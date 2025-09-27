@extends('layouts.app')

@section('title', 'إكمال الملف الشخصي - مزود الخدمة')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> إكمال الملف الشخصي - مزود الخدمة
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif



                    <form method="POST" action="{{ route('provider.profile.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- المعلومات الأساسية -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle"></i> المعلومات الأساسية
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bio" class="form-label">نبذة عنك <span class="text-danger">*</span></label>
                                    <textarea name="bio" id="bio" class="form-control @error('bio') is-invalid @enderror"
                                              rows="4" placeholder="اكتب نبذة مختصرة عن خبراتك ومهاراتك...">{{ old('bio', $profile ? $profile->bio : '') }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->phone }}" readonly>
                                    <small class="form-text text-muted">رقم الهاتف المسجل في حسابك</small>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                                           value="{{ old('address', $profile ? $profile->address : '') }}" placeholder="عنوانك الكامل">
                                    @error('address')
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

                                    @if(Auth::user()->image)
                                        <div class="mt-2">
                                            <small class="text-muted">الصورة الحالية:</small>
                                            <div class="mt-1">
                                                <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="الصورة الحالية"
                                                     class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- اختيار الأقسام -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-folder"></i> الأقسام التي تعمل فيها
                                    <small class="text-muted">(حد أقصى {{ $maxCategories }} أقسام)</small>
                                </h5>

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
                                                <input class="form-check-input category-checkbox" type="checkbox"
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

                        <!-- اختيار المدن -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt"></i> المدن التي تعمل فيها
                                    <small class="text-muted">(حد أقصى {{ $maxCities }} مدن)</small>
                                </h5>

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
                                                <input class="form-check-input city-checkbox" type="checkbox"
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



                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> حفظ الملف الشخصي
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
