<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    /**
     * Tạo một nhân viên mới
     *
     * @param array $data
     * @return User
     */
    public static function createStaff(array $data)
    {
        $staff = new User();
        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->password = Hash::make($data['password']);
        $staff->role_id = $data['role_id'];
        $staff->manager_id = $data['manager_id'] ?? null;
        $staff->save();

        return $staff;
    }
}