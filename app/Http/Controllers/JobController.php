<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
   
    public function index()
    {
        $jobs = Job::with(['company', 'recruiter', 'applications'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'salary' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active','inactive','closed'])],
        ]);

        $job = Job::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job created successfully.',
            'data' => $job
        ], 201);
    }

  
    public function show($id)
    {
        $job = Job::with(['company', 'recruiter', 'applications'])->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }


    public function update(Request $request, $id)
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found.'
            ], 404);
        }

        $validated = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'salary' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active','inactive','closed'])],
        ]);

        $job->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully.',
            'data' => $job
        ]);
    }

    public function destroy($id)
    {
        $job = Job::find($id);

        if (!$job) {
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
