<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAdminController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Retrieve All Success',
            'data' => Admin::with(['role'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = Admin::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'is_super' => 0
        ]);

        return response()->json([
            'message' => 'Add Data Success',
            'data' => $user,
        ], 200);
    }

    public function update(Request $request, Admin $user)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username,'.$user->id,
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($request->input('password') != '') {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            $user->fill(['password' => bcrypt($request->password)]);
        }

        $user->update([
            'username' => $request->username,
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'message' => 'Update Data Success',
            'data' => $user,
        ], 200);
    }

    public function destroy(Admin $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Delete Data Success'
        ], 200);
    }
}
