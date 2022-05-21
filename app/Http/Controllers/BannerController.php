<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Banner::orderBy('sort', 'asc')->get();
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
                ->addColumn('title', function ($row) {
                    return $row->title ?? '-';
                })
                ->addColumn('link', function ($row) {
                    if (!empty($row->link)) {
                        $link = '<a href=' . $row->link . ' target="_blank"> ' . $row->link . '</a>';
                    } else {
                        $link = '-';
                    }
                    return $link;
                })
                ->addColumn('sort', function ($row) {
                    return $row->sort ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $id = $this->safe_encode(Crypt::encryptString($row->id));
                    $btn = '<a href="' . route('banner.edit', ['id' => $id]) . '" class="edit btn btn-warning-material btn-sm mdi mdi-pencil"></a><button class="delete btn btn-danger-material btn-sm mdi mdi-delete ml-2"  data-id=' . $id . '></button>';

                    return $btn;
                })
                ->rawColumns(['image', 'title', 'sort', 'link', 'action'])
                ->make(true);
        }

        return view('content-dashboard.banners.index');
    }

    public function add()
    {
        $sort_number = collect(Banner::SORT_NUMBER);
        $banners = Banner::select('sort')->limit(3)->pluck('sort');

        $data_sort = $sort_number->diff($banners); // get data diff from sort number on model banner

        return view('content-dashboard.banners.add', compact('data_sort'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'title'  => 'required|string',
                'sort' => 'required|numeric',
                'category_file' => 'required|string'
            ]);

            if ($validator->fails()) {
                if ($request->has('category_file')) {
                    if (file_exists(public_path('uploads/banners/' . $request->category_file))) {
                        unlink(public_path('uploads/banners/' . $request->category_file));
                    }
                }
                return redirect()->route('banner.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($request->has('link')) {
                if (!filter_var($request->link, FILTER_VALIDATE_URL)) {
                    if ($request->has('category_file')) {
                        if (file_exists(public_path('uploads/banners/' . $request->category_file))) {
                            unlink(public_path('uploads/banners/' . $request->category_file));
                        }
                    }
                    return redirect()->route('banner.add')
                        ->withErrors('Link tidak sesuai format')
                        ->withInput();
                }
            }
            $banner = Banner::create([
                'title' => $request->title,
                'src_image' => 'uploads/banners/' . $request->category_file,
                'link' => $request->link ?? null,
                'sort' => $request->sort
            ]);

            if ($banner) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'banner', 'store'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('banner.index')->with('status', 'Banner berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('banner.add')->withErrors($th->getMessage())->withInput();
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
            $path = public_path('uploads/banners');


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
            $banner = Banner::findOrFail($dec_id);
            $data_sort = Banner::SORT_NUMBER;
            $fileName = '';
            $fileSize = '';
            $filePath = '';
            if (file_exists(public_path($banner->src_image))) {
                $file_path = asset($banner->src_image);

                $size = filesize(public_path($banner->src_image));
                $file = explode('/', $banner->src_image);

                $fileName = $file[2];
                $fileSize = $size;
                $filePath = $file_path;
            }

            return view('content-dashboard.banners.edit', compact('banner', 'fileName', 'fileSize', 'filePath', 'id', 'data_sort'));
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
                    'title'  => 'required|string',
                    'sort' => 'required|numeric',
                    'category_file' => 'required|string'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'title'  => 'required|string',
                    'sort' => 'required|numeric',
                ]);
            }

            if ($validator->fails()) {
                if ($request->has('category_file')) {
                    if (file_exists(public_path('uploads/banners/' . $request->category_file))) {
                        unlink(public_path('uploads/banners/' . $request->category_file));
                    }
                }
                return redirect()->route('banner.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($request->has('link')) {
                if (!filter_var($request->link, FILTER_VALIDATE_URL)) {
                    if ($request->has('category_file')) {
                        if (file_exists(public_path('uploads/banners/' . $request->category_file))) {
                            unlink(public_path('uploads/banners/' . $request->category_file));
                        }
                    }
                    return redirect()->route('banner.add')
                        ->withErrors('Link tidak sesuai format')
                        ->withInput();
                }
            }

            $dec_id = $this->safe_decode(Crypt::decryptString($id));

            $banner = Banner::find($dec_id);

            if (empty($banner)) {
                return redirect()->route('banner.edit', ['id' => $id])->withErrors('Data banner tidak ditemukan')->withInput();
            }

            if ($request->has('category_file')) {
                if (file_exists(public_path($banner->src_image))) {
                    unlink(public_path($banner->src_image)); // delete file before
                }
                $source = 'uploads/banners/' . $request->category_file;
            } else {
                $source = $banner->src_image;
            }

            if ($request->sort != $banner->sort) {
                // check input sort already exist on another data or not??
                $sortExist = Banner::where('sort', $request->sort)->first();

                if (!empty($sortExist)) {
                    $sortExist->update(['sort' => $banner->sort]); // update sort from data sort on update
                }
            }


            $banner->update([
                'title' => $request->title,
                'src_image' => $source,
                'sort' => $request->sort
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'banner', 'update'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('banner.index')->with('status', 'Banner berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->has('category_file')) {
                if (file_exists(public_path('uploads/banners/' . $request->category_file))) {
                    unlink(public_path('uploads/banners/' . $request->category_file));
                }
            }
            return redirect()->route('banner.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/banners/' . $request->name);

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
            $banner = Banner::find($dec_id);

            if (empty($banner)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data banner not found'
                ]);
            }

            // check what data have a file?
            if ($banner->src_image != '' || $banner->src_image != null || !empty($banner->src_image)) {
                // check if data file exists or not, if exists deleted file
                if (file_exists(public_path($banner->src_image))) {
                    unlink(public_path($banner->src_image));
                }
            }

            $banner->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'banner', 'deletes'];

            log_activity($data); // simpan log activity

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Berhasil Menghapus Data Banner'
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
