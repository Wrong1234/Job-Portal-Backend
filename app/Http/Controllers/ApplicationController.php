<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
      public function index()
    {
        $user = Auth::user();
        if ($user->role === 'recruiter') {
            $jobs = Job::with('applications.user')
                    ->where('recruiter_id', $user->id)
                    ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $jobs
            ]);
        }

        if ($user->role === 'user') {
            $applications = Application::where('user_id', $user->id)
                                       ->paginate(10);

            return response()->json([
                'success' => true,
                'data'    => $applications
            ]);
        }

        // Admin role
        $applications = Application::paginate(10);
        return response()->json([
            'success' => true,
            'data'    => $applications
        ]);
    }


    /**
     * Recruiter job and applications
     */
    public function show($jobId)
    {
        $user = Auth::user();

        $job =Job::with('applications.user')
                    ->where('id', $jobId)
                    ->where('recruiter_id', $user->id)
                    ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or not authorized'
            ], 404);
        }

        $applications = $job->applications()->with('user')->paginate(10);

        return response()->json([
            'success'      => true,
            'job'          => $job,
            'applications' => $applications
        ]);
    }

    /**
     * Recruiter job status update
     */
    public function update(Request $request, $jobId, $applicationId)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $user = Auth::user();

        $job = Job::where('id', $jobId)
                ->where('recruiter_id', $user->id)
                ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found or not authorized'
            ], 404);
        }

        $application = $job->applications()->where('id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found for this job'
            ], 404);
        }

        // Application এর status update করা
        $application->status = $request->status;
        $application->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Application status updated successfully',
            'application' => $application
        ]);
    }

}
