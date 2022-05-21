<?php

namespace App\Http\Controllers;

use App\Models\TypeGood;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class TypegoodController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:jenis-barang-list|jenis-barang-create|jenis-barang-edit|jenis-barang-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:jenis-barang-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:jenis-barang-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:jenis-barang-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = TypeGood::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('size', function ($row) {
                    return $row->size ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $user = User::find(Auth::user()->id);
                    if ($user->hasRole('super admin')) {
                        $btn = '<a href="' . route('jenis_barang.edit', Crypt::encryptString($row->id)) . '" class="edit-typegood btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                        $btn .= '<button class="delete-typegood btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                        return $btn;
                    } else {
                        return '<button class="btn btn-gradient-info btn-sm ml-1" title="Silahkan hubungi super admin untuk edit dan delete"><i class="mdi mdi-lock"></i></button>';
                    }
                })

                ->rawColumns(['name', 'size', 'action'])

                ->make(true);
        }
        return view('content-dashboard.jenis_barang.index');
    }

    public function add()
    {
        return view('content-dashboard.jenis_barang.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'size' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis_barang.add')
                ->withErrors($validator)
                ->withInput();
        }


        try {
            DB::beginTransaction();
            TypeGood::create([
                'name' => $request->name,
                'size' => $request->size
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'jenis_barang', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('jenis_barang.index')->with('status', 'Jenis Barang berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('jenis_barang.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $typegood = TypeGood::findOrFail($decrypted);

            return view('content-dashboard.jenis_barang.edit', compact('typegood'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'size' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis_barang.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $decrypted = Crypt::decryptString($id);

            $typegood = TypeGood::findOrFail($decrypted);

            $typegood->update([
                'name' => $request->name,
                'size' => $request->size
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'jenis_barang', 'update'];

            log_activity($data);

            return redirect()->route('jenis_barang.index')->with('status', 'Data jenis barang berhasil diperbarui');
        } catch (\Throwable $th) {
            return redirect()->route('jenis_barang.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);
            $typegoodExistOrNot = TypeGood::find($decrypted);

            if (empty($typegoodExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data jenis barang tidak ditemukan'
                ], 404);
            }

            $typegoodExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'jenis_barang', 'delete'];

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
        $category = TypeGood::orWhere('name', 'LIKE', "%{$request->search}%")
            ->orWhere('size', 'LIKE', "%{$request->search}%")
            ->orderBy('id', 'asc')
            ->limit(10)->get();
        return json_encode($category);
    }
}
