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

    /**
     * Cập nhật thông tin nhân viên
     *
     * @param int $staffId
     * @param array $data
     * @return bool
     */
    public static function updateStaff(int $staffId, array $data)
    {
        $staff = User::find($staffId);
        if (!$staff) {
            return false;
        }

        $staff->name = $data['name'];
        $staff->email = $data['email'];
        if (isset($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }
        $staff->role_id = $data['role_id'];
        $staff->manager_id = $data['manager_id'] ?? null;
        $staff->save();

        return true;
    }
}