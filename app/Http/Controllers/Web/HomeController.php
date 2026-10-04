<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
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
        $clcs = CommunityLearningCenter::with(['assignedTeacher.profile'])
            ->where('status', 'active')
            ->orderBy('barangay')
            ->get();

        $strands = LearningStrand::withCount('publishedModules')
            ->orderBy('order_seq')
            ->get();

        $totalClcs = $clcs->count();
        $totalModules = Module::where('is_published', true)->count();
        $barangayCount = $clcs->pluck('barangay')->unique()->count();

        return view('home', compact('clcs', 'strands', 'totalClcs', 'totalModules', 'barangayCount'));
    }
}
