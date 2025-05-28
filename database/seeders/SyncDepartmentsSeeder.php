<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DepartmentSetting;
use Illuminate\Support\Facades\DB;

class SyncDepartmentsSeeder extends Seeder
{
    public function run()
    {
        // Get all unique departments from users
        $departments = User::whereNotNull('department')
                          ->select('department')
                          ->distinct()
                          ->pluck('department');
        
        foreach ($departments as $departmentName) {
            // Check if department already exists in department_settings
            $exists = DepartmentSetting::where('department', $departmentName)->exists();
            
            if (!$exists) {
                // Create new department setting
                DepartmentSetting::create([
                    'department' => $departmentName,
                    'budget' => 0, // Default budget
                    'description' => 'Automatically created based on user assignments'
                ]);
                
                $this->command->info("Created department: {$departmentName}");
            }
        }
    }
}