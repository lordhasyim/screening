<?php

// app/Helpers/QuizHelpers.php

if (!function_exists('getPhq9InterpretationText')) {
    function getPhq9InterpretationText($category) 
    {
        return match(strtolower($category)) {
            'sangat rendah' => 'Tidak terlihat adanya indikasi keluhan depresif signifikan berdasarkan skor tes.',
            'rendah' => 'Kondisi relatif baik, namun perhatikan gejala ringan. Jika muncul perubahan, pertimbangkan follow-up.',
            'sedang' => 'Terdapat indikasi gejala sedang. Disarankan melakukan pemeriksaan lebih lanjut atau berkonsultasi dengan layanan profesional.',
            'tinggi' => 'Terdapat indikasi gejala berat. Segera pertimbangkan konsultasi ke layanan kesehatan mental.',
            'sangat tinggi' => 'Terdapat indikasi gejala yang sangat berat. Disarankan segera menghubungi layanan profesional.',
            default => 'Hasil memerlukan interpretasi lebih lanjut.'
        };
    }
}

if (!function_exists('getDass21InterpretationText')) {
    function getDass21InterpretationText($category) 
    {
        return match(strtolower($category)) {
            'sangat rendah' => 'Hasil menunjukkan tingkat gejala dalam kondisi sangat rendah.',
            'rendah' => 'Hasil menunjukkan tingkat gejala rendah.',
            'sedang' => 'Hasil menunjukkan tingkat gejala sedang. Disarankan evaluasi lebih lanjut.',
            'tinggi' => 'Hasil menunjukkan tingkat gejala tinggi. Disarankan segera konsultasi.',
            'sangat tinggi' => 'Hasil menunjukkan tingkat gejala sangat tinggi. Disarankan segera mendapatkan bantuan profesional.',
            default => 'Hasil memerlukan interpretasi lebih lanjut.'
        };
    }
}

if (!function_exists('getOverallRecommendation')) {
    function getOverallRecommendation($riskLevel) 
    {
        return match($riskLevel) {
            'Low' => 'Kesehatan mental Anda dalam kondisi baik. Tetap pertahankan gaya hidup sehat dan jaga keseimbangan kehidupan.',
            'Moderate' => 'Kondisi kesehatan mental perlu perhatian. Disarankan untuk melakukan langkah-langkah preventif dan monitoring diri.',
            'High' => 'Kondisi kesehatan mental memerlukan perhatian serius. Sangat disarankan untuk segera berkonsultasi dengan profesional.',
            'Critical' => 'Kondisi kesehatan mental memerlukan intervensi segera. Hubungi layanan konseling atau hotline darurat untuk bantuan.',
            default => 'Hasil memerlukan evaluasi lebih lanjut oleh profesional kesehatan mental.'
        };
    }
}

if (!function_exists('getRiskBadgeColor')) {
    function getRiskBadgeColor($riskLevel) 
    {
        return match($riskLevel) {
            'Low' => 'success',
            'Moderate' => 'info', 
            'High' => 'warning',
            'Critical' => 'danger',
            default => 'secondary'
        };
    }
}