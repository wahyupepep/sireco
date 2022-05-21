<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::select('id', 'name');
            return Datatables::of($data)
                ->addIndexColumn()
                ->setRowId('id')
                ->addColumn('action', function ($row) {
                    $id = $this->safe_encode(Crypt::encryptString($row->id));
                    $btn = '<a href="' . route('category.edit', ['id' => $id]) . '" class="edit btn btn-warning-material btn-sm mdi mdi-pencil"></a><button class="delete btn btn-danger-material btn-sm mdi mdi-delete ml-2"  data-id=' . $id . ' data-name=' . $row->name . '></button>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content-dashboard.categories.index');
    }

    public function add()
    {
        return view('content-dashboard.categories.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name'  => 'required|string',
            'category_file' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis_barang.add')
                ->withErrors($validator)
                ->withInput();
        }


        try {
            DB::beginTransaction();

            $category = Category::create([
                'name' => $request->category_name,
                'src_image' => 'uploads/categories/' . $request->category_file
            ]);

            if ($category) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'category', 'store'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('category.index')->with('status', 'Kategori berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('category.add')->withErrors($th->getMessage())->withInput();
        }
    }

    public function storeImage(Request $request)
    {

        try {
            $validation = Validator::make($request->all(), [
                "file" => 'required|mimes:jpg,jpeg,png,svg|file|max:1048',
            ], [
                'file.required' => 'File must be filled',
                'file.mimes' => 'File must be jpg/jpeg/png/svg',
                'file.size'  => 'File must be lower than 2 MB'
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'code'    => 500,
                    'success' => false,
                    'message' => $validation->errors()->first()
                ], 500);
            }
            $path = public_path('uploads/categories');


            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file = $request->file('file');

            $name = uniqid() . '_' . trim($file->getClientOriginalName());

            $file->move($path, $name);

            return response()->json([
                'name'          => $name,
                'original_name' => $file->getClientOriginalName(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $dec_id = $this->safe_decode(Crypt::decryptString($id));

            $category = Category::findOrFail($dec_id);
            $fileName = '';
            $fileSize = '';
            $filePath = '';
            if (file_exists(public_path($category->src_image))) {
                $file_path = asset($category->src_image);

                $size = filesize(public_path($category->src_image));
                $file = explode('/', $category->src_image);

                $fileName = $file[2];
                $fileSize = $size;
                $filePath = $file_path;
            }

            return view('content-dashboard.categories.edit', compact('category', 'fileName', 'fileSize', 'filePath', 'id'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            if ($request->has('category_file')) {
                $validator = Validator::make($request->all(), [
                    'category_name' => 'required|string',
                    'category_file' => 'required|string'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'category_name' => 'required|string',
                ]);
            }

            if ($validator->fails()) {
                return redirect()->route('category.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $dec_id = $this->safe_decode(Crypt::decryptString($id));

            $category = Category::find($dec_id);

            if (empty($category)) {
                return redirect()->route('category.edit', ['id' => $id])->withErrors('Data kategori tidak ditemukan')->withInput();
            }

            if ($request->has('category_file')) {
                if (file_exists(public_path($category->src_image))) {
                    unlink(public_path($category->src_image)); // delete file before
                }
                $source = 'uploads/categories/' . $request->category_file;
            } else {
                $source = $category->src_image;
            }


            $category->update([
                'name' => $request->category_name,
                'src_image' => $source
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'category', 'update'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('category.index')->with('status', 'Kategori berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('category.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/categories/' . $request->name);

            if (!file_exists($path)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'File not found'
                ]);
            }

            unlink($path); // delete file from server

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Successfully deleted file'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $dec_id = $this->safe_decode(Crypt::decryptString($request->id));
            $category = Category::find($dec_id);

            if (empty($category)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data kategori not found'
                ]);
            }

            // check what data have a file?
            if ($category->src_image != '' || $category->src_image != null || !empty($category->src_image)) {
                // check if data file exists or not, if exists deleted file
                if (file_exists(public_path($category->src_image))) {
                    unlink(public_path($category->src_image));
                }
            }

            $category->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'category', 'deletes'];

            log_activity($data); // simpan log activity

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Berhasil Menghapus Data Kategori'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    function safe_encode($string)
    {
        $data = str_replace(array('/'), array('_'), $string);
        return $data;
    }

    function safe_decode($string, $mode = null)
    {
        $data = str_replace(array('_'), array('/'), $string);
        return $data;
    }
}
