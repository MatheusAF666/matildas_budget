<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $settings = [
            'name' => $user->company_name,
            'logo' => $user->company_logo ? Storage::url($user->company_logo) : null,
            'phone' => $user->company_phone,
            'email' => $user->company_email,
            'address' => $user->company_address,
            'website' => $user->company_website,
            'defaultDueDays' => $user->default_due_days ?? 31,
        ];

        return response()->json([
            'status' => true,
            'data' => $settings
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|string|max:255',
            'default_due_days' => 'nullable|integer|min:1|max:365',
        ]);

        $user = $request->user();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->company_logo) {
                Storage::delete($user->company_logo);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $user->company_logo = $logoPath;
        }

        // Update other fields
        $user->company_name = $validated['name'] ?? $user->company_name;
        $user->company_phone = $validated['phone'] ?? $user->company_phone;
        $user->company_email = $validated['email'] ?? $user->company_email;
        $user->company_address = $validated['address'] ?? $user->company_address;
        $user->company_website = $validated['website'] ?? $user->company_website;
        $user->default_due_days = $validated['default_due_days'] ?? $user->default_due_days ?? 31;
        
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Company settings updated successfully',
            'data' => [
                'name' => $user->company_name,
                'logo' => $user->company_logo ? Storage::url($user->company_logo) : null,
                'phone' => $user->company_phone,
                'email' => $user->company_email,
                'address' => $user->company_address,
                'website' => $user->company_website,
                'defaultDueDays' => $user->default_due_days ?? 31,
            ]
        ]);
    }
}
