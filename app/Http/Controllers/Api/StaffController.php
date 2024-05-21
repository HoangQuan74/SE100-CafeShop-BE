<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MultipleDestroyRequest;
use App\Http\Requests\StoreStaffRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 0)->paginate();

        return response()->json($staffs);
    }

    public function store(StoreStaffRequest $request)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'email_verified_at' => now(),
            'password' => bcrypt($request->input('password')),
            'remember_token' => Str::random(10),
            'role' => 0,
        ];
        $staff = User::create($data);

        return response()->json($staff)->setStatusCode(201);
    } 

    public function update(StoreStaffRequest $request, User $staff)
    {
        $staff->update($request->all());

        return response()->json($staff);
    } 

    public function show(User $staff)
    {
        return response()->json($staff);
    } 

    public function destroy(User $staff)
    {
        $staff->delete();

        return response('', 204);
    } 

    public function destroyMultiple(MultipleDestroyRequest $request)
    {
        $ids = $request->ids;

        if (in_array(Auth::user()->id, $ids)) {
            return response('Cannot delete yourself', 202);
        }

        User::destroy($request->ids);

        return response('Deleted successfully', 204);
    }

     /**
     * Reset password for a specific staff.
     *
     * @param UpdatePasswordRequest $request
     * @param User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(UpdatePasswordRequest $request, User $staff)
    {
        $staff->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }

     /**
     * Toggle active status of a staff.
     *
     * @param User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActiveStatus(User $staff)
    {
        $staff->active = !$staff->active;
        $staff->save();

        return response()->json(['active' => $staff->active]);
    }

    /**
     * Assign a role to a staff.
     *
     * @param UpdateRoleRequest $request
     * @param User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(UpdateRoleRequest $request, User $staff)
    {
        $staff->role = $request->role;
        $staff->save();

        return response()->json(['role' => $staff->role]);
    }
}
