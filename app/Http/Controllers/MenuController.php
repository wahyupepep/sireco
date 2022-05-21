<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Menu::with('category');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (preg_match('/^http/i', $row->src_image)) {
                        $source = $row->src_image;
                    } else {
                        $source = asset($row->src_image);
                    }
                    $src = '<img src="' . $source . '" class="img-fluid img-responsive shadow d-block mx-auto" style="width: 100px; height: 100px; object-fit:cover">';
                    return $src;
                })
                ->addColumn('category', function ($row) {
                    return $row->category->name;
                })
                ->addColumn('price', function ($row) {
                    return "Rp " . number_format($row->price, 2, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $id = $this->safe_encode(Crypt::encryptString($row->id));
                    $btn = '<a href="' . route('menu.edit', ['id' => $id]) . '" class="edit btn btn-warning-material btn-sm mdi mdi-pencil"></a><button class="delete btn btn-danger-material btn-sm mdi mdi-delete ml-2"  data-id=' . $id . ' data-name=' . $row->name . '></button>';

                    return $btn;
                })
                ->rawColumns(['image', 'category', 'price', 'action'])
                ->make(true);
        }

        return view('content-dashboard.menus.index');
    }

    public function add()
    {
        $category = Category::first(['id', 'name']);
        return view('content-dashboard.menus.add', compact('category'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'menu'  => 'required|string',
                'category' => 'required|numeric',
                'price' => 'required|string',
                'description' => 'required|string',
                'category_file' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->route('menu.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $menu = Menu::create([
                'name' => $request->menu,
                'category_id' => $request->category,
                'price' => str_replace('.', '', $request->price),
                'description' => $request->description,
                'src_image' => 'uploads/menus/' . $request->category_file
            ]);

            if ($menu) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'menu', 'store'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('menu.index')->with('status', 'Menu berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('menu.add')->withErrors($th->getMessage())->withInput();
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
            $path = public_path('uploads/menus');


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
            $menu = Menu::findOrFail($dec_id);

            $category = Category::find($menu->category_id);
            $fileName = '';
            $fileSize = '';
            $filePath = '';
            if (file_exists(public_path($menu->src_image))) {
                $file_path = asset($menu->src_image);

                $size = filesize(public_path($menu->src_image));
                $file = explode('/', $menu->src_image);

                $fileName = $file[2];
                $fileSize = $size;
                $filePath = $file_path;
            }

            return view('content-dashboard.menus.edit', compact('category', 'menu', 'fileName', 'fileSize', 'filePath', 'id'));
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
                    'menu'  => 'required|string',
                    'category' => 'required|numeric',
                    'price' => 'required|string',
                    'description' => 'required|string',
                    'category_file' => 'required|string'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'menu'  => 'required|string',
                    'category' => 'required|numeric',
                    'price' => 'required|string',
                    'description' => 'required|string',
                ]);
            }

            if ($validator->fails()) {
                return redirect()->route('menu.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $dec_id = $this->safe_decode(Crypt::decryptString($id));

            $menu = Menu::find($dec_id);

            if (empty($menu)) {
                return redirect()->route('menu.edit', ['id' => $id])->withErrors('Data menu tidak ditemukan')->withInput();
            }

            if ($request->has('category_file')) {
                if (file_exists(public_path($menu->src_image))) {
                    unlink(public_path($menu->src_image)); // delete file before
                }
                $source = 'uploads/menus/' . $request->category_file;
            } else {
                $source = $menu->src_image;
            }


            $menu->update([
                'name' => $request->menu,
                'category_id' => $request->category,
                'price' => str_replace('.', '', $request->price),
                'description' => $request->description,
                'src_image' => $source
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'menu', 'update'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('menu.index')->with('status', 'Menu berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('menu.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/menus/' . $request->name);

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
            $menu = Menu::find($dec_id);

            if (empty($menu)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data menu not found'
                ]);
            }

            // check what data have a file?
            if ($menu->src_image != '' || $menu->src_image != null || !empty($menu->src_image)) {
                // check if data file exists or not, if exists deleted file
                if (file_exists(public_path($menu->src_image))) {
                    unlink(public_path($menu->src_image));
                }
            }

            $menu->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'menu', 'deletes'];

            log_activity($data); // simpan log activity

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Berhasil Menghapus Data Menu'
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

    public function searchCategory(Request $request)
    {
        $categories = Category::select('id', 'name')->where('name', 'LIKE', "%{$request->search}%")
            ->orderBy('id', 'asc')
            ->limit(10)->get();

        return json_encode($categories);
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
