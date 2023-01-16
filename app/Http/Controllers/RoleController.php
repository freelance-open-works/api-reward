<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Retrieve All Success',
            'data' => Role::with(['permissions'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*.name' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        $role = Role::create(['name' => $request->name]);
        $role->permissions()->saveMany(collect($request->permissions)->mapInto(RolePermission::class));
        DB::commit();

        return response()->json([
            'message' => 'Add Data Success',
            'data' => $role,
        ], 200);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*.name' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        $role->permissions()->delete();
        $role->update(['name' => $request->name]);
        $role->permissions()->saveMany(collect($request->permissions)->mapInto(RolePermission::class));
        DB::commit();

        return response()->json([
            'message' => 'Update Data Success',
            'data' => $role,
        ], 200);
    }

    public function destroy(Role $role)
    {
        DB::beginTransaction();
        $role->permissions()->delete();
        $role->delete();
        DB::commit();

        return response()->json([
            'message' => 'Delete Data Success',
            'data' => $role,
        ], 200);
    }
}
