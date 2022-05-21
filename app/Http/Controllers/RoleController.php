<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:role-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('role.edit', Crypt::encryptString($row->id)) . '" class="edit-role btn btn-warning-material btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    $btn .= '<button class="delete-role btn btn-danger-material btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'action'])

                ->make(true);
        }
        return view('content-dashboard.roles.index');
    }

    public function add()
    {
        $permissions = Permission::get();
        return view('content-dashboard.roles.add', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|unique:roles,name',
            'permission' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('role.add')
                ->withErrors($validator)
                ->withInput();
        }


        try {
            DB::beginTransaction();

            $role = Role::create(['name' => $request->input('name')]);

            $role->syncPermissions($request->input('permission'));

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'role', 'store'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('role.index')->with('status', 'Role dan Hak Akses berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('role.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $role = Role::findOrFail($decrypted);

            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $role->id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();

            $permissions = Permission::get();

            return view('content-dashboard.roles.edit', compact('role', 'rolePermissions', 'permissions'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'permission' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('role.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $decrypted = Crypt::decryptString($id);

            $role = Role::findOrFail($decrypted);

            $role->update([
                'name' => $request->name
            ]);

            $role->syncPermissions($request->input('permission'));

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'role', 'update'];

            log_activity($data);

            return redirect()->route('role.index')->with('status', 'Data role berhasil diperbarui');
        } catch (\Throwable $th) {
            return redirect()->route('role.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);
            $roleIsExists = Role::find($decrypted);

            if (empty($roleIsExists)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data role tidak ditemukan'
                ], 404);
            }

            $roleIsExists->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'role', 'delete'];

            log_activity($data);

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Data gagal di hapus'
            ], 500);
        }
    }
}
