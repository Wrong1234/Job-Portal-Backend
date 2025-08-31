<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    // List companies with pagination
    public function index()
    {
        $companies = Company::withCount('jobs')->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $companies
        ]);
    }

    // Store new company
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'address'     => 'nullable|string|max:500',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255|unique:companies',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully.',
            'data'    => $company
        ], 201);
    }

    // Show single company
    public function show($id)
    {
        $company = Company::with('jobs.applications')->find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $company
        ]);
    }

    // Update company
    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'address'     => 'nullable|string|max:500',
            'phone'       => 'nullable|string|max:20',
            'email'       => ['nullable', 'email', 'max:255', Rule::unique('companies')->ignore($company->id)],
        ]);

        $company->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully.',
            'data'    => $company
        ]);
    }

    // Delete company
    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.'
            ], 404);
        }

        if ($company->jobs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete company with associated users or jobs.'
            ], 400);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully.'
        ]);
    }
}
