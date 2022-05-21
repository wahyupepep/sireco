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
use App\Exports\TransactionOutExport;
use App\Models\TransactionDetail;

class TransactionOutController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:barang-keluar-list|barang-keluar-create|barang-keluar-edit|barang-keluar-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:barang-keluar-create', ['only' => ['add', 'store']]);
        $this->middleware('permission:barang-keluar-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:barang-keluar-delete', ['only' => ['delete']]);
        $this->middleware('permission:barang-keluar-export', ['only' => ['excel', 'print']]);
    }
    public function index()
    {
        $products = Product::limit(10)->get();
        $brands = Brand::limit(10)->get();
        $categories = TypeGood::limit(10)->get();
        $users = User::select('id', 'name')
            ->whereIn('role', [1, 3])
            ->get();

        $isAdmin = User::find(Auth::user()->id)->hasRole('super admin');

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



        return view('content-dashboard.barang_keluar.index', compact('products', 'brands', 'categories', 'users', 'selectedCategory', 'selectedBrand', 'selectedProduct', 'selectedUser', 'start_date', 'end_date', 'isAdmin'));
    }

    public function getData(Request $request)
    {

        if ($request->ajax()) {

            $request->session()->put('filter_kategori', $request->category);
            $request->session()->put('filter_merk', $request->brand);
            $request->session()->put('filter_produk', $request->product);
            $request->session()->put('filter_tgl_start', $request->start_date);
            $request->session()->put('filter_tgl_end', $request->end_date);
            $request->session()->put('filter_user', $request->user);

            $consume_data = Transaction::with('transaction_detail')->where('flag', 1);
            if (User::find(Auth::user()->id)->hasRole('admin barang keluar')) {
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
                ->addColumn('customer', function ($row) {
                    return $row->customer_name;
                })
                ->addColumn('date_input', function ($row) {
                    return date('d M y', strtotime($row->date_input));
                })
                ->addColumn('sum_item', function ($row) {
                    return $row->transaction_detail()->count();
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (User::find(Auth::user()->id)->hasRole('super admin')) {
                        $btn .= '<a href="' . route('barang-keluar.show', Crypt::encryptString($row->id)) . '" class="detail-transcation btn btn-gradient-info btn-sm" title="Hubungi admin untuk mengedit atau hapus" target="_blank"><i class="mdi mdi-eye"></i></a>';
                        $btn .= '<a href="' . route('barang-keluar.edit', Crypt::encryptString($row->id)) . '" class="edit-transcation btn btn-gradient-warning btn-sm ml-1"><i class="mdi mdi-lead-pencil"></i></a>';
                        $btn .= '<button class="delete-transaction btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . '><i class="mdi mdi-delete"></i></button>';
                    } else {
                        $btn .= '<a href="' . route('barang-keluar.show', Crypt::encryptString($row->id)) . '" class="detail-transcation btn btn-gradient-info btn-sm" title="Hubungi admin untuk mengedit atau hapus" target="_blank"><i class="mdi mdi-eye"></i></a>';
                    }

                    return $btn;
                })

                ->rawColumns(['customer', 'date_input', 'sum_item', 'action'])

                ->make(true);
        }
    }

    public function add()
    {
        $products = Product::with(['get_brand:id,name', 'get_category:id,name'])->limit(10)->get();
        return view('content-dashboard.barang_keluar.add', compact('products'));
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

        return view('content-dashboard.barang_keluar.show', compact('details'));
    }

    public function store(Request $request)
    {
        // return response()->json($request->all());
        $validator = Validator::make($request->all(), [
            'customer' => 'required|string',
            'date'     => 'required|string',
            'pid'      => 'required',
            'qty'      => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang-keluar.add')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // cek apakah ada qty yang diinputkan 0 
            if ($this->check_qty_not_nill($request->qty)) {
                return redirect()->route('barang-keluar.add')->withErrors('Jumlah barang yang diinputkan tidak boleh 0')->withInput();
            }

            // cek stok produk jika ada yang false lempar kembalikan ke page add lagi sebagai pesan salah
            if (in_array(false, $this->cek_stock_is_exist($request->pid, $request->qty))) {
                return redirect()->route('barang-keluar.add')->withErrors('Maaf, Stok yang tersedia kurang dari stok barang yang tersedia')->withInput();
            }

            $sum = 0;

            $transaction =  Transaction::create([
                'customer_name' => $request->customer,
                'flag'       => 1, // flag untuk barang keluar
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
                    'stock' => $product->stock - $request->qty[$i]
                ]);

                $sum += 1;
            }

            if ($sum != count($request->pid)) {
                DB::rollback();
                return redirect()->route('barang-keluar.add')->withErrors('Terjadi kesalahan pada sistem')->withInput();
            }

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_keluar', 'insert'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('barang-keluar.index')->with('status', 'Stok barang keluar berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('barang-keluar.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $decrypted = Crypt::decryptString($id);


            $transaction_out = Transaction::with(['transaction_detail' => function ($query) {
                $query->with(['product' => function ($query) {
                    $query->with(['get_category:id,name,size', 'get_brand:id,name']);
                }]);
            }, 'get_user:id,name'])->where('id', $decrypted)->first();

            $products = Product::with(['get_brand:id,name', 'get_category:id,name'])->limit(10)->get();

            if (empty($transaction_out)) { // jika id tidak ada diarahkan ke page 404
                abort(404);
            }

            return view('content-dashboard.barang_keluar.edit', compact('transaction_out', 'products'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'customer' => 'required|string',
            'date'     => 'required|string',
            'pid'      => 'required',
            'qty'      => 'required'
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
            $transaction_out = Transaction::findOrFail($decrypted);

            $sum_update_data = 0;

            for ($i = 0; $i < count($request->pid); $i++) {
                // cari data transaction detail produk terlebih dahulu
                $get_data_transaction_product_detail = TransactionDetail::where([
                    'transaction_id' => $decrypted,
                    'product_id'     => $request->pid[$i]
                ])->first();


                $product = Product::find($request->pid[$i]);

                // cek apakah data ada jika ada diupdate kalau tidak ada di insert
                TransactionDetail::updateOrCreate(
                    ['transaction_id' => $decrypted, 'product_id' => $request->pid[$i]],
                    ['stock'          => $request->qty[$i], 'note'   => $request->note[$i] ?? "-"]
                );


                // update data stok berdasarkan produk terkait
                if (!empty($get_data_transaction_product_detail)) {
                    if ($request->qty[$i] > $get_data_transaction_product_detail->stock) {
                        $qty = $request->qty[$i] - $get_data_transaction_product_detail->stock;
                        if ($qty > $product->stock) {
                            DB::rollBack();
                            return redirect()->route('barang-keluar.edit', ['id' => $id])->withErrors('Maaf, Stok yang tersedia kurang dari stok barang yang tersedia')->withInput();
                        } else {
                            $product->update([
                                'stock' => $product->stock - $qty
                            ]);
                        }
                    } else if ($request->qty[$i] < $get_data_transaction_product_detail->stock) {
                        $qty = $get_data_transaction_product_detail->stock - $request->qty[$i];
                        $product->update([
                            'stock' => $product->stock + $qty
                        ]);
                    }
                } else {
                    if ($product->stock > $request->qty[$i]) {
                        $product->update([
                            'stock' => $product->stock - $request->qty[$i]
                        ]);
                    } else {
                        DB::rollback();
                        return redirect()->route('barang-keluar.edit', ['id' => $id])->withErrors('Maaf, Stok yang tersedia kurang dari stok barang yang tersedia')->withInput();
                    }
                }
                $sum_update_data += 1;
            }

            if ($sum_update_data != count($request->pid)) { // cek apakah data sudah terupdate semua di tabel transaction detail
                DB::rollBack();
                return redirect()->route('barang-keluar.edit', ['id' => $id])->withErrors('Terjadi kesalahan pada sistem')->withInput();
            }

            $transaction_out->update([ // update transaksi barang masuk jika mengalami kesalahan
                'customer_name' => $request->customer,
                'date_input' => date('Y-m-d', strtotime($request->date)),
                'updated_by' => Auth::user()->id,
                'updated_admin_at' => date('Y-m-d H:i:s')
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_keluar', 'update'];

            log_activity($data);

            DB::commit();

            return redirect()->route('barang-keluar.index')->with('status', 'Stok barang keluar berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('barang-keluar.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
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
                    'stock' => $product->stock + $item->stock
                ]);
            }

            $transactionInExistOrNot->transaction_detail()->delete();

            $transactionInExistOrNot->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'barang_keluar', 'delete'];

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
                'stock' => $product->stock + $transactionInExistOrNot->stock
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
        $request->session()->put('filter_tgl_start', $request->start_date);
        $request->session()->put('filter_tgl_end', $request->end_date);
        $request->session()->put('filter_user', $request->user);

        $consume_data = Transaction::with(['transaction_detail' => function ($query) {
            $query->with(['product' => function ($query) {
                $query->with(['get_brand:id,name', 'get_category:id,name,size']);
            }]);
        }, 'get_user:id,name'])->where('flag', 1);

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
        $filter_tgl_start   = session('filter_tgl_start');
        $filter_tgl_end     = session('filter_tgl_end');
        $filter_user        = session('filter_user');

        $consume_data = Transaction::with(['transaction_detail' => function ($query) {
            $query->with(['product' => function ($query) {
                $query->with(['get_brand:id,name', 'get_category:id,name,size']);
            }]);
        }, 'get_user:id,name'])->where('flag', 1);

        if (User::find(Auth::user()->id)->hasRole('admin barang keluar')) {
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

        return view('content-dashboard.barang_keluar.report.print', compact('data', 'filter_tgl_start', 'filter_tgl_end'));
    }

    public function excel()
    {
        $filter_tgl_start   = session('filter_tgl_start');
        $filter_tgl_end     = session('filter_tgl_end');
        $filter_user        = session('filter_user');

        return Excel::download(new TransactionOutExport($filter_user, $filter_tgl_start, $filter_tgl_end), 'Report-History-Pengeluaran-Stok-' . date('d-m-y', strtotime($filter_tgl_start)) . '-' . date('d-m-y', strtotime($filter_tgl_end)) . '.xlsx');
    }

    private function cek_stock_is_exist($productid, $qty)
    {
        $tmp_check = [];
        for ($i = 0; $i < count($productid); $i++) {
            $product = Product::find($productid[$i]);
            if ($product->stock >= $qty[$i]) {
                array_push($tmp_check, true);
            } else {
                array_push($tmp_check, false);
            }
        }
        return $tmp_check;
    }

    private function check_qty_not_nill($qty)
    {
        return in_array(0, $qty);
    }
}
