<?php

// namespace App\Http\Controllers;

// use App\Models\Job;
// use App\Models\Application;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;

// class AdminAnalyticsController extends Controller
// {
//     public function summary()
//     {

//         $activeJobs   = Job::where('status','active')->count();
//         $closedJobs   = Job::whereIn('status',['closed','filled','expired'])->count();
//         $newJobsToday = Job::whereDate('posted_at', now()->toDateString())->count();

//         $totalJobs    = Job::count();
//         $totalApps    = Application::count();
//         $avgAppsPerJob = $totalJobs ? round($totalApps / $totalJobs, 2) : 0;
//         $acceptApp     = Application::where('status', 'accepted')->count();
//         $rejectApp     = Application::where('status', 'rejected')->count();

//         $newAppsToday = Application::whereDate('applied_at', now()->toDateString())->count();


//         return response()->json([
//             'jobs' => [
//                 'activeJob'      => $activeJobs,
//                 'closedJob'      => $closedJobs,
//                 'newTodayAddJob' => $newJobsToday,
//             ],
//             'applications' => [
//                 'totalApplication'        => $totalApps,
//                 'todayApplication'        => $newAppsToday,
//                 'avg_per_job_application' => $avgAppsPerJob,
//             ],
//             'application_overview' => [
//                 'acceptedApplication' => $acceptApp,
//                 'rejectedApplication' => $rejectApp,
//             ],
//         ]);
//     }
// }

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAnalyticsController extends Controller
{
    /**
     * Get analytics summary based on user role
     */
    public function summary()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminSummary();
        }

        if ($user->role === 'recruiter') {
            return $this->recruiterSummary($user->id);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access',
        ], 403);
    }

    /**
     * Admin analytics - full data
     */
    private function adminSummary()
    {
        $activeJobs    = Job::where('status','active')->count();
        $closedJobs    = Job::whereIn('status',['closed','filled','expired'])->count();
        $newJobsToday  = Job::whereDate('posted_at', now()->toDateString())->count();

        $totalJobs     = Job::count();
        $totalApps     = Application::count();
        $avgAppsPerJob = $totalJobs ? round($totalApps / $totalJobs, 2) : 0;

        $acceptApp     = Application::where('status', 'accepted')->count();
        $rejectApp     = Application::where('status', 'rejected')->count();
        $newAppsToday  = Application::whereDate('applied_at', now()->toDateString())->count();

        return response()->json([
            'success' => true,
            'role' => 'admin',
            'jobs' => [
                'activeJobs'      => $activeJobs,
                'closedJobs'      => $closedJobs,
                'newJobsToday'    => $newJobsToday,
                'totalJobs'       => $totalJobs,
            ],
            'applications' => [
                'totalApplications'        => $totalApps,
                'todayApplications'        => $newAppsToday,
                'avgApplicationsPerJob'    => $avgAppsPerJob,
                'acceptedApplications'     => $acceptApp,
                'rejectedApplications'     => $rejectApp,
            ],
        ]);
    }

    /**
     * Recruiter analytics - only for their own jobs
     */
    private function recruiterSummary($recruiterId)
    {
        $activeJobs    = Job::where('recruiter_id', $recruiterId)
                            ->where('status','active')
                            ->count();

        $closedJobs    = Job::where('recruiter_id', $recruiterId)
                            ->whereIn('status',['closed','filled','expired'])
                            ->count();

        $newJobsToday  = Job::where('recruiter_id', $recruiterId)
                            ->whereDate('posted_at', now()->toDateString())
                            ->count();

        $totalJobs     = Job::where('recruiter_id', $recruiterId)->count();
        $totalApps     = Application::whereHas('job', function($q) use($recruiterId){
            $q->where('recruiter_id', $recruiterId);
        })->count();

        $avgAppsPerJob = $totalJobs ? round($totalApps / $totalJobs, 2) : 0;

        $acceptApp     = Application::whereHas('job', function($q) use($recruiterId){
            $q->where('recruiter_id', $recruiterId);
        })->where('status', 'accepted')->count();

        $rejectApp     = Application::whereHas('job', function($q) use($recruiterId){
            $q->where('recruiter_id', $recruiterId);
        })->where('status', 'rejected')->count();

        $newAppsToday  = Application::whereHas('job', function($q) use($recruiterId){
            $q->where('recruiter_id', $recruiterId);
        })->whereDate('applied_at', now()->toDateString())->count();

        return response()->json([
            'success' => true,
            'role' => 'recruiter',
            'jobs' => [
                'activeJobs'      => $activeJobs,
                'closedJobs'      => $closedJobs,
                'newJobsToday'    => $newJobsToday,
                'totalJobs'       => $totalJobs,
            ],
            'applications' => [
                'totalApplications'        => $totalApps,
                'todayApplications'        => $newAppsToday,
                'avgApplicationsPerJob'    => $avgAppsPerJob,
                'acceptedApplications'     => $acceptApp,
                'rejectedApplications'     => $rejectApp,
            ],
        ]);
    }
}
