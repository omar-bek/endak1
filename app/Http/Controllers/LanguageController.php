<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // التحقق من أن اللغة مدعومة
        $supportedLocales = ['ar', 'en'];
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'ar';
        }
        
        // حفظ اللغة في الجلسة
        session()->put('locale', $locale);
        
        // إعادة التوجيه للصفحة السابقة
        return redirect()->back();
    }
}
