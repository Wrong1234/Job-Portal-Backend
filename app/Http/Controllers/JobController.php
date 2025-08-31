<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Find a recruiter-owned job
     */
    private function findRecruiterJob($id) {
        $user = Auth::user();
        return Job::where('recruiter_id', $user->id)->where('id', $id)->first();
    }

    /**
     * List jobs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->query('per_page', 15);
        if($user->role === "recruiter"){
            $jobs = Job::where('recruiter_id', $user->id)
                        ->withCount('applications')
                        ->paginate($request->query('per_page', 10));
        } else {
            $jobs = Job::with(['company', 'recruiter'])
                        ->withCount('applications')
                        ->paginate($perPage);
        }

        return response()->json([
            'success' => true,
            'data'    => $jobs
        ]);
    }

    /**
     * Store a new job
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements'=> 'nullable|string|max:2000',
            'salary'      => 'nullable|numeric|min:0',
            'location'    => 'nullable|string|max:255',
            'status'      => ['nullable', Rule::in(['active','inactive','closed'])],
        ]);

        $validated['user_id'] = Auth::id();

        $job = Job::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job created successfully.',
            'data'    => $job
        ], 201);
    }

    /**
     * Show a job
     */
    public function show($id)
    {
        $user = Auth::user();

        if($user->role === "recruiter") {
            $job = $this->findRecruiterJob($id);
        } else {
            $job = Job::with(['company', 'recruiter', 'applications'])->find($id);
        }

        if(!$job){
            return response()->json([
                'success' => false,
                'message' => 'Job not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $job
        ]);
    }

    /**
     * Update a job
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if($user->role === "recruiter") {
            $job = $this->findRecruiterJob($id);
        } else {
            $job = Job::find($id);
        }

        if(!$job){
            return response()->json([
                'success' => false,
                'message' => 'Job not found.'
            ], 404);
        }

        $validated = $request->validate([
            'company_id'  => 'sometimes|required|exists:companies,id',
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements'=> 'nullable|string|max:2000',
            'salary'      => 'nullable|numeric|min:0',
            'location'    => 'nullable|string|max:255',
            'status'      => ['nullable', Rule::in(['active','inactive','closed'])],
        ]);

        $job->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully.',
            'data'    => $job
        ]);
    }

    /**
     * Delete a job
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if($user->role === "recruiter") {
            $job = $this->findRecruiterJob($id);
        } else {
            $job = Job::find($id);
        }

        if(!$job){
            return response()->json([
                'success' => false,
                'message' => 'Job not found.'
            ], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully.'
        ]);
    }
}
