<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Page Controller
 * 
 * Handles static pages like Terms, Privacy, How It Works, Fees
 */
class PageController extends Controller
{
    /**
     * Show Terms and Conditions page
     */
    public function terms(): View
    {
        return view('pages.terms', [
            'title' => __('pages.terms.title'),
            'metaDescription' => __('pages.terms.meta_description'),
            'lastUpdated' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Show Privacy Policy page
     */
    public function privacy(): View
    {
        return view('pages.privacy', [
            'title' => __('pages.privacy.title'),
            'metaDescription' => __('pages.privacy.meta_description'),
            'lastUpdated' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Show How It Works page
     */
    public function howItWorks(): View
    {
        return view('pages.how-it-works', [
            'title' => __('pages.how_it_works.title'),
            'metaDescription' => __('pages.how_it_works.meta_description'),
        ]);
    }

    /**
     * Show Fees and Commissions page
     */
    public function fees(): View
    {
        return view('pages.fees', [
            'title' => __('pages.fees.title'),
            'metaDescription' => __('pages.fees.meta_description'),
            'lastUpdated' => now()->format('Y-m-d'),
        ]);
    }
}
