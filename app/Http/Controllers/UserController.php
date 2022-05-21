<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {


        if ($request->ajax()) {
            $data = User::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('role', function ($row) {
                    return $row->role == 1 ? '<span class="badge btn-danger-material">Super Admin</span>' : '<span class="badge btn-blue-material">Admin</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('user.edit', Crypt::encryptString($row->id)) . '" class="edit-user btn btn-warning-material btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    Auth::user()->id == $row->id ?  $btn .= '<button class="reset-user btn btn-blue-material btn-sm ml-1 d-none" data-id=' .  Crypt::encryptString($row->id) . ' data-name=' . $row->name . ' disabled><i class="mdi mdi-lock"></i></button>'  :  $btn .= '<button class="reset-user btn btn-blue-material btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name=' . $row->name . '><i class="mdi mdi-lock"></i></button>';
                    Auth::user()->id == $row->id ?  $btn .= '<button class="delete-user btn btn-danger-material btn-sm ml-1 disabled" data-id=' .  Crypt::encryptString($row->id) . ' data-name=' . $row->name . '><i class="mdi mdi-delete"></i></button>' : $btn .= '<button class="delete-user btn btn-danger-material btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name=' . $row->name . '><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'email', 'role', 'action'])

                ->make(true);
        }

        return view('content-dashboard.users.index');
    }

    public function add()
    {
        $roles = Role::all();
        return view('content-dashboard.users.add', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|email',
            'role'  => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.add')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => bcrypt('password123456'),
                'role' => $request->role,
                'status' => 1,
            ]);

            $user->assignRole($request->role);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'user', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('user.index')->with('status', 'Data user berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('user.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {

        try {
            $decrypted = Crypt::decryptString($id);

            $user = User::findOrFail($decrypted);

            $roles = Role::all();

            return view('content-dashboard.users.edit', compact('user', 'roles'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|email',
            'role'  => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $decrypted = Crypt::decryptString($id);

            $user = User::findOrFail($decrypted);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'user', 'update'];

            log_activity($data);
            return redirect()->route('user.index')->with('status', 'Data user berhasil diperbarui');
        } catch (\Throwable $th) {
            return redirect()->route('user.index', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);
            $userExistOrNot = User::find($decrypted);

            if (empty($userExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data user tidak ditemukan'
                ], 404);
            }

            $userExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'user', 'delete'];

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

    public function resetPassword(Request $request)
    {
        try {
            $user = User::find(Crypt::decryptString($request->id));

            if (empty($user)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data user tidak ditemukan'
                ], 404);
            }

            $user->update([
                'password' => bcrypt('password2022')
            ]);

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Berhasil mereset password user ' . $user->name
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
