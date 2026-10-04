<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\LearningStrand;
use App\Models\Module;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public Constructivist Homepage.
     */
    public function index(): View
    {
        return $this->renderPublicPage('home');
    }

    /**
     * Display one of the public information pages from the main navigation.
     */
    public function showPage(string $pageKey): View
    {
        abort_unless(in_array($pageKey, ['about', 'programs', 'requirements', 'centers', 'contact'], true), 404);

        return $this->renderPublicPage($pageKey);
    }

    private function renderPublicPage(string $pageKey): View
    {
        $pageTitles = [
            'home' => 'Home',
            'about' => 'About ALS',
            'programs' => 'A & E Program',
            'requirements' => 'Requirements',
            'centers' => 'Learning Centers',
            'contact' => 'Contact Us',
        ];

        $clcs = collect();
        $strands = collect();
        $totalClcs = 0;
        $totalModules = 0;
        $barangayCount = 0;

        if ($pageKey === 'home') {
            $activeClcs = CommunityLearningCenter::where('status', 'active')->get(['barangay']);
            $totalClcs = $activeClcs->count();
            $barangayCount = $activeClcs->pluck('barangay')->unique()->count();
            $totalModules = Module::where('is_published', true)->count();
        }

        if ($pageKey === 'programs') {
            $strands = LearningStrand::withCount('publishedModules')
                ->orderBy('order_seq')
                ->get();
        }

        if ($pageKey === 'centers') {
            $clcs = CommunityLearningCenter::with(['assignedTeacher.profile'])
                ->where('status', 'active')
                ->orderBy('barangay')
                ->get();
        }

        return view('home', compact(
            'pageKey',
            'pageTitles',
            'clcs',
            'strands',
            'totalClcs',
            'totalModules',
            'barangayCount'
        ));
    }
}
