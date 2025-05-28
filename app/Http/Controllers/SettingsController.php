<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return view('teacher.settings', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'cin' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $request->firstName . ' ' . $request->lastName;
        $user->email = $request->email;
        $user->cin = $request->cin;
        $user->phone = $request->phone;
        $user->department = $request->department;

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return redirect()->route('teacher.settings')
            ->with('profile_success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->currentPassword, $user->password)) {
            return back()->withErrors([
                'currentPassword' => 'Le mot de passe actuel est incorrect.',
            ])->withInput();
        }

        // Update password
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return redirect()->route('teacher.settings')
            ->with('security_success', 'Mot de passe mis à jour avec succès.');
    }
    public function settings()
    {
        $user = Auth::user();
        
        // Get department settings if exist
        $departmentSettings = DepartmentSetting::where('department', $user->department)->first();
        
        return view('chef.settings', compact('user', 'departmentSettings'));
    }
    public function updateDepartmentSettings(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is a department head
        if (!$user->isChefDepartement() || !$user->department) {
            return redirect()->route('chef.settings')
                ->with('error', 'Vous devez d\'abord configurer votre département dans votre profil.');
        }
        
        $request->validate([
            'departmentBudget' => 'required|numeric|min:0',
            'departmentDescription' => 'nullable|string',
            'enableDirectorValidation' => 'sometimes|boolean',
            'enableBudgetCheck' => 'sometimes|boolean',
        ]);
        
        // Find or create department settings
        $departmentSettings = DepartmentSetting::firstOrNew(['department' => $user->department]);
        
        $departmentSettings->budget = $request->departmentBudget;
        $departmentSettings->description = $request->departmentDescription;
        $departmentSettings->director_validation = $request->has('enableDirectorValidation');
        $departmentSettings->budget_check = $request->has('enableBudgetCheck');
        
        $departmentSettings->save();
        
        return redirect()->route('chef.settings')
            ->with('department_success', 'Paramètres du département mis à jour avec succès.');
    }
}