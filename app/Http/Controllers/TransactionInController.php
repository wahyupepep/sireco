<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TypeGood;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionInExport;
use App\Models\TransactionDetail;

class TransactionInController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:barang-masuk-list|barang-masuk-create|barang-masuk-edit|barang-masuk-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:barang-masuk-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:barang-masuk-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:barang-masuk-delete', ['only' => ['delete']]);
        $this->middleware('permission:barang-masuk-export', ['only' => ['excel', 'print']]);
    }
    public function index()
    {

        $products = Product::limit(10)->get();
        $brands = Brand::limit(10)->get();
        $categories = TypeGood::limit(10)->get();
        $users = User::select('id', 'name')
            ->whereIn('role', [1, 2])
            ->get();

        $isSuper = User::find(Auth::user()->id)->hasRole('super admin');

        if (session('filter_kategori') == "") {
            $selectedCategory = '';
        } else {
            $selectedCategory = session('filter_kategori');
        }

        if (session('filter_merk') == "") {
            $selectedBrand = '';
        } else {
            $selectedBrand = session('filter_merk');
        }

        if (session('filter_produk') == "") {
            $selectedProduct = '';
        } else {
            $selectedProduct = session('filter_produk');
        }

        if (session('filter_user') == "") {
            $selectedUser = '';
        } else {
            $selectedUser = session('filter_user');
        }

        if (session('filter_tgl_start') == "") {
            $start_date = date('d-m-Y', strtotime(' - 30 days'));
        } else {
            $start_date = session('filter_tgl_start');
        }

        if (session('filter_tgl_end') == "") {
            $end_date = date('d-m-Y');
        } else {
            $end_date = session('filter_tgl_end');
        }


        return view('content-dashboard.barang_masuk.index', compact('products', 'brands', 'categories', 'users', 'selectedCategory', 'selectedBrand', 'selectedProduct', 'selectedUser', 'start_date', 'end_date', 'isSuper'));
    }

    public function getData(Request $request)
    {

        if ($request->ajax()) {
            $request->session()->put('filter_tgl_start', $request->start_date);
            $request->session()->put('filter_tgl_end', $request->end_date);
            $request->session()->put('filter_user', $request->user);

            $consume_data = Transaction::with('transaction_detail')->where('flag', 0);
            if (User::find(Auth::user()->id)->hasRole('admin barang masuk')) {
                $consume_data->where('user_id', Auth::user()->id);
            }

            if ($request->start_date != "") {
                $consume_data->whereDate('date_input', '>=', date('Y-m-d', strtotime($request->start_date)));
            }
            if ($request->end_date != "") {
                $consume_data->whereDate('date_input', '<=', date('Y-m-d', strtotime($request->end_date)));
            }
            if ($request->user != "") {
                $consume_data->where('user_id', $request->user);
            }
            $data = $consume_data->orderBy('id', 'desc')->get();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('no_nota', function ($row) {
                    return $row->no_nota ?? '-';
                })
                ->addColumn('supplier', function ($row) {
                    return $row->supplier ?? '-';
                })
                ->addColumn('tanggal_input', function ($row) {
                    return date('d M y', strtotime($row->date_input));
                })
                ->addColumn('jumlah_barang_input', function ($row) {
                    return $row->transaction_detail->count();
                })
                ->addColumn('action', function ($row) {
                    $user = User::find(Auth::user()->id);
                    $btn = '';
                    if ($user->hasRole('super admin')) {
                        $btn .= '<a href="' . route('barang-masuk.show', Crypt::encryptString($row->id)) . '" class="detail-transcation btn btn-gradient-info btn-sm" title="Hubungi admin untuk mengedit atau hapus" target="_blank"><i class="mdi mdi-eye"></i></a>';
                        $btn .= '<a href="' . route('barang-masuk.edit', Crypt::encryptString($row->id)) . '" class="edit-transcation ml-1 btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                        $btn .= '<button class="delete-transaction btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . '><i class="mdi mdi-delete"></i></button>';
                    } else {
                        $btn .= '<a href="' . route('barang-masuk.show', Crypt::encryptString($row->id)) . '" class="lock-transcation btn btn-gradient-info btn-sm" title="Hubungi admin untuk mengedit atau hapus"><i class="mdi mdi-eye"></i></a>';
                    }
                    return $btn;
                })

                ->rawColumns(['no_nota', 'supplier', 'tanggal_input', 'jumlah_barang_input', 'action'])

                ->make(true);
        }
    }

    public function add()
    {
        $products = Product::with(['get_brand:id,name', 'get_category:id,name'])->limit(10)->get();
        return view('content-dashboard.barang_masuk.add', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_nota' => 'required|string',
            'pid'     => 'required',
            'qty'     => 'required',
            'date'    => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang-masuk.add')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'no_nota'    => $request->no_nota,
                'supplier'   => $request->supplier ?? '-',
                'flag'       => 0, // flag untuk transaksi masuk
                'date_input' => date('Y-m-d', strtotime($request->date)),
                'user_id'    => Auth::user()->id,
            ]);

            for ($i = 0; $i < count($request->pid); $i++) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $request->pid[$i],
                    'stock'          => $request->qty[$i],
                    'note'           => $request->note[$i] ?? '-'
                ]);

                $product = Product::find($request->pid[$i]);

                $product->update([
                    'stock' => $product->stock + $request->qty[$i]
                ]);
            }


            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_masuk', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('status', 'Stok barang masuk berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('barang-masuk.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $decrypted = Crypt::decryptString($id);

        $details = Transaction::with(['transaction_detail' => function ($query) {
            $query->with(['product' => function ($query) {
                $query->with(['get_category:id,name,size', 'get_brand:id,name']);
            }]);
        }, 'get_user:id,name'])->where('id', $decrypted)->first();

        if (empty($details)) {
            abort('404');
        }

        return view('content-dashboard.barang_masuk.show', compact('details'));
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $transaction_in = Transaction::with(['transaction_detail' => function ($query) {
                $query->with(['product' => function ($query) {
                    $query->with(['get_category:id,name,size', 'get_brand:id,name']);
                }]);
            }, 'get_user:id,name'])->where('id', $decrypted)->first();

            $products = Product::with(['get_brand:id,name', 'get_category:id,name'])->limit(10)->get();

            if (empty($transaction_in)) { // jika id tidak ada diarahkan ke page 404
                abort(404);
            }

            return view('content-dashboard.barang_masuk.edit', compact('transaction_in', 'products'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'no_nota' => 'required|string',
            'pid'     => 'required',
            'qty'     => 'required',
            'date'    => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang-masuk.edit', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        $decrypted = Crypt::decryptString($id);

        try {
            DB::beginTransaction();
            // cek apakah id transaksi yang ada di database
            $transaction_in = Transaction::findOrFail($decrypted);
            $sum_update_data = 0;
            for ($i = 0; $i < count($request->pid); $i++) {
                // cari data transaction detail produk terlebih dahulu
                $get_data_transaction_product_detail = TransactionDetail::where([
                    'transaction_id' => $decrypted,
                    'product_id'     => $request->pid[$i]
                ])->first();


                // cek apakah data ada jika ada diupdate kalau tidak ada di insert
                TransactionDetail::updateOrCreate(
                    ['transaction_id' => $decrypted, 'product_id' => $request->pid[$i]],
                    ['stock'          => $request->qty[$i], 'note'           => $request->note[$i] ?? "-"]
                );

                $product = Product::find($request->pid[$i]);

                // update data stok berdasarkan produk terkait
                if (!empty($get_data_transaction_product_detail)) {
                    if ($request->qty[$i] > $get_data_transaction_product_detail->stock) {
                        $qty = $request->qty[$i] - $get_data_transaction_product_detail->stock;
                        $product->update([
                            'stock' => $product->stock + $qty
                        ]);
                    } else if ($request->qty[$i] < $get_data_transaction_product_detail->stock) {
                        $qty = $get_data_transaction_product_detail->stock - $request->qty[$i];
                        $product->update([
                            'stock' => $product->stock - $qty
                        ]);
                    }
                } else {
                    $product->update([
                        'stock' => $product->stock + $request->qty[$i]
                    ]);
                }
                $sum_update_data += 1;
            }

            if ($sum_update_data != count($request->pid)) { // cek apakah data sudah terupdate semua di tabel transaction detail
                DB::rollBack();
                return redirect()->route('barang-masuk.edit', ['id' => $id])->withErrors('Terjadi kesalahan pada sistem')->withInput();
            }

            $transaction_in->update([ // update transaksi barang masuk jika mengalami kesalahan
                'no_nota'    => $request->no_nota,
                'supplier'   => $request->supplier,
                'date_input' => date('Y-m-d', strtotime($request->date)),
                'updated_by' => Auth::user()->id,
                'updated_admin_at' => date('Y-m-d H:i:s')
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_masuk', 'update'];

            log_activity($data);

            DB::commit();

            return redirect()->route('barang-masuk.index')->with('status', 'Stok barang masuk berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('barang-masuk.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function delete(Request $request)
    {

        try {
            $decrypted = Crypt::decryptString($request->id);

            DB::beginTransaction();

            $transactionInExistOrNot = Transaction::with('transaction_detail')
                ->where('id', $decrypted)
                ->first();

            if (empty($transactionInExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 404);
            }

            foreach ($transactionInExistOrNot->transaction_detail as $item) {
                $product = Product::find($item->product_id);

                $product->update([
                    'stock' => $product->stock - $item->stock
                ]);
            }

            $transactionInExistOrNot->transaction_detail()->delete();

            $transactionInExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_masuk', 'delete'];

            log_activity($data);
            DB::commit();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Data gagal di hapus'
            ], 500);
        }
    }

    public function delete_transaction(Request $request)
    {
        try {
            $transactionInExistOrNot = TransactionDetail::find($request->id);

            if (empty($transactionInExistOrNot)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 200);
            }

            $product = Product::find($transactionInExistOrNot->product_id);

            $product->update([
                'stock' => $product->stock - $transactionInExistOrNot->stock
            ]);

            $transactionInExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'transaction_detail', 'delete'];

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
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function checkData(Request $request)
    {
        $request->session()->put('filter_kategori', $request->category);
        $request->session()->put('filter_merk', $request->brand);
        $request->session()->put('filter_produk', $request->product);
        $request->session()->put('filter_tgl_start', $request->start_date);
        $request->session()->put('filter_tgl_end', $request->end_date);
        $request->session()->put('filter_user', $request->user);

        $consume_data = Transaction::with('transaction_detail')->where('flag', 0);
        if (User::find(Auth::user()->id)->hasRole('admin barang masuk')) {
            $consume_data->where('user_id', Auth::user()->id);
        }

        if ($request->start_date != "") {
            $consume_data->whereDate('date_input', '>=', date('Y-m-d', strtotime($request->start_date)));
        }
        if ($request->end_date != "") {
            $consume_data->whereDate('date_input', '<=', date('Y-m-d', strtotime($request->end_date)));
        }
        if ($request->user != "") {
            $consume_data->where('user_id', $request->user);
        }
        $data = $consume_data->orderBy('id', 'desc')->get();

        if (count($data) <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Data yang dipilih kosong'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Terdapat Data'
        ]);
    }

    public function print()
    {
        $filter_kategori    = session('filter_kategori');
        $filter_merk        = session('filter_merk');
        $filter_tgl_start   = session('filter_tgl_start');
        $filter_tgl_end     = session('filter_tgl_end');
        $filter_produk      = session('filter_produk');
        $filter_user        = session('filter_user');

        $consume_data = Transaction::with(['transaction_detail' => function ($query) {
            $query->with(['product' => function ($query) {
                $query->with(['get_brand:id,name', 'get_category:id,name,size']);
            }]);
        }, 'get_user:id,name'])->where('flag', 0);
        if (User::find(Auth::user()->id)->hasRole('admin barang masuk')) {
            $consume_data->where('user_id', Auth::user()->id);
        }

        if ($filter_tgl_start != "") {
            $consume_data->whereDate('date_input', '>=', date('Y-m-d', strtotime($filter_tgl_start)));
        }
        if ($filter_tgl_end != "") {
            $consume_data->whereDate('date_input', '<=', date('Y-m-d', strtotime($filter_tgl_end)));
        }
        if ($filter_user != "") {
            $consume_data->where('user_id', $filter_user);
        }
        $data = $consume_data->orderBy('id', 'desc')->get();

        return view('content-dashboard.barang_masuk.report.print', compact('data', 'filter_tgl_start', 'filter_tgl_end'));
    }

    public function excel()
    {
        $filter_tgl_start   = session('filter_tgl_start');
        $filter_tgl_end     = session('filter_tgl_end');
        $filter_user        = session('filter_user');

        return Excel::download(new TransactionInExport($filter_user, $filter_tgl_start, $filter_tgl_end), 'Report-History-Pengeluaran-Stok-' . date('d-m-y', strtotime($filter_tgl_start)) . '-' . date('d-m-y', strtotime($filter_tgl_end)) . '.xlsx');
    }
}
