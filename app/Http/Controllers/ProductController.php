<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\TypeGood;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:produk-list|produk-create|produk-edit|produk-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:produk-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:produk-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:produk-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with(['get_brand:id,name', 'get_category:id,name,size'])->get();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('category', function ($row) {
                    return $row->get_category != null ? $row->get_category->name . ' - ' . $row->get_category->size : '-';
                })
                ->addColumn('brand', function ($row) {
                    return $row->get_brand != null ? $row->get_brand->name : '-';
                })
                ->addColumn('motif', function ($row) {
                    return $row->motif;
                })
                ->addColumn('stock', function ($row) {
                    return $row->stock;
                })
                ->addColumn('action', function ($row) {
                    $user = User::find(Auth::user()->id);
                    if ($user->hasRole('super admin')) {
                        $btn = '<a href="' . route('produk.edit', Crypt::encryptString($row->id)) . '" class="edit-product btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                        $btn .= '<button class="delete-product btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                        return $btn;
                    } else {
                        return '<button class="btn btn-gradient-info btn-sm ml-1" title="Silahkan hubungi super admin untuk edit dan delete"><i class="mdi mdi-lock"></i></button>';
                    }
                })

                ->rawColumns(['name', 'category', 'brand', 'stock', 'motif', 'action'])

                ->make(true);
        }
        return view('content-dashboard.produk.index');
    }

    public function add()
    {
        $brand = Brand::select('id', 'name')->limit(10)->get();
        $category = TypeGood::select('id', 'name', 'size')->limit(10)->get();
        return view('content-dashboard.produk.add', compact('brand', 'category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'motif' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('produk.add')
                ->withErrors($validator)
                ->withInput();
        }

        // cek produk apakah sudah ada di database atau belum?
        $checkProductExistOrNot = Product::where([
            'brand_id'     => $request->brand,
            'type_good_id' => $request->category,
            'motif'        => $request->motif
        ])->first();

        if (!empty($checkProductExistOrNot)) { // jika sudah dikembalikan dengan pesan error di route produk.add
            return redirect()->route('produk.add')
                ->withErrors('Produk telah ada sebelumnya')
                ->withInput();
        }

        try { // jika tidak melakukan insert ke db
            DB::beginTransaction();
            Product::create([
                'name' => $request->name,
                'description' => $request->description ?? '-',
                'brand_id' => $request->brand ?? null,
                'type_good_id' => $request->category ?? null,
                'motif' => $request->motif,
                'stock' => 0
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'master_produk', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('produk.index')->with('status', 'Master barang berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('produk.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $product = Product::findOrFail($decrypted);

            $brand = Brand::select('id', 'name')->limit(10)->get();

            $category = TypeGood::select('id', 'name', 'size')->limit(10)->get();

            return view('content-dashboard.produk.edit', compact('product', 'brand', 'category'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'motif' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('produk.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        $decrypted = Crypt::decryptString($id);

        // cek produk apakah sudah ada di database atau belum?
        $checkProductExistOrNot = Product::where([
            'brand_id'     => $request->brand,
            'type_good_id' => $request->category,
            'motif'        => $request->motif
        ])->where('id', '!=', $decrypted)->first();

        if (!empty($checkProductExistOrNot)) { // jika sudah dikembalikan dengan pesan error di route produk.add
            return redirect()->route('produk.edit', ['id' => $id])
                ->withErrors('Produk telah ada sebelumnya, silahkan menginputkan data kembali')
                ->withInput();
        }

        try {
            $product = Product::findOrFail($decrypted);

            $product->update([
                'name' => $request->name,
                'description' => $request->description ?? '-',
                'brand_id' => $request->brand ?? null,
                'type_good_id' => $request->category ?? null,
                'motif' => $request->motif,

            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'master_produk', 'update'];

            log_activity($data);

            return redirect()->route('produk.index')->with('status', 'Master barang berhasil diperbarui');
        } catch (\Throwable $th) {
            return redirect()->route('produk.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);
            $productExistOrNot = Product::find($decrypted);

            if (empty($productExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data barang tidak ditemukan'
                ], 404);
            }

            $productExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'master_produk', 'delete'];

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
        $product = Product::with(['get_brand:id,name', 'get_category:id,name,size'])
            ->where('name', 'LIKE', "%{$request->search}%")
            ->orderBy('id', 'asc')
            ->limit(10)->get();
        return json_encode($product);
    }

    public function select_product(Request $request)
    {
        $product = Product::with(['get_brand:id,name', 'get_category:id,name,size'])
            ->where('id', $request->product_id)
            ->first();
        return response()->json($product);
    }

    public function get_product()
    {
        $products = Product::with(['get_brand:id,name', 'get_category:id,name'])->limit(10)->get();
        return response()->json([
            'code'   => 200,
            'status' => true,
            'data'   => $products
        ]);
    }
}
