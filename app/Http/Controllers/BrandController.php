<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:merk-list|merk-create|merk-edit|merk-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:merk-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:merk-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:merk-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Brand::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $user = User::find(Auth::user()->id);
                    if ($user->hasRole('super admin')) {
                        $btn = '<a href="' . route('merk.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                        $btn .= '<button class="delete-brand btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                        return $btn;
                    } else {
                        return '<button class="btn btn-gradient-info btn-sm ml-1" title="Silahkan hubungi super admin untuk edit dan delete"><i class="mdi mdi-lock"></i></button>';
                    }
                })

                ->rawColumns(['name', 'action'])

                ->make(true);
        }
        return view('content-dashboard.merk.index');
    }

    public function add()
    {
        return view('content-dashboard.merk.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('merk.add')
                ->withErrors($validator)
                ->withInput();
        }


        try {
            DB::beginTransaction();
            Brand::create([
                'name' => $request->name,
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'merk', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('merk.index')->with('status', 'Merk berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('merk.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $brand     = Brand::findOrFail($decrypted);

            return view('content-dashboard.merk.edit', compact('brand'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('merk.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $decrypted = Crypt::decryptString($id);

            $brand = Brand::findOrFail($decrypted);

            $brand->update([
                'name' => $request->name,
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'merk', 'update'];

            log_activity($data);

            return redirect()->route('merk.index')->with('status', 'Data Merk berhasil diperbarui');
        } catch (\Throwable $th) {
            return redirect()->route('merk.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);
            $brandExistOrNot = Brand::find($decrypted);

            if (empty($brandExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Datamerk tidak ditemukan'
                ], 404);
            }

            $brandExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'merk', 'delete'];

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

    public function search(Request $request)
    {
        $brand = Brand::where('name', 'LIKE', "%{$request->search}%")
            ->orderBy('name', 'asc')
            ->limit(10)->get();
        return json_encode($brand);
    }
}
