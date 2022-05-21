<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Slider::orderBy('sort', 'asc')->get();
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
                ->addColumn('sort', function ($row) {
                    return $row->sort ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $id = $this->safe_encode(Crypt::encryptString($row->id));
                    $btn = '<a href="' . route('slider.edit', ['id' => $id]) . '" class="edit btn btn-warning-material btn-sm mdi mdi-pencil"></a><button class="delete btn btn-danger-material btn-sm mdi mdi-delete ml-2"  data-id=' . $id . '></button>';

                    return $btn;
                })
                ->rawColumns(['image', 'title', 'sort', 'action'])
                ->make(true);
        }

        return view('content-dashboard.sliders.index');
    }

    public function add()
    {
        $sort_number = collect(Slider::SORT_NUMBER);
        $sliders = Slider::select('sort')->limit(3)->pluck('sort');

        $data_sort = $sort_number->diff($sliders); // get data diff from sort number on model banner

        return view('content-dashboard.sliders.add', compact('data_sort'));
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
                    if (file_exists(public_path('uploads/sliders/' . $request->category_file))) {
                        unlink(public_path('uploads/sliders/' . $request->category_file));
                    }
                }
                return redirect()->route('banner.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $slider = Slider::create([
                'title' => $request->title,
                'src_image' => 'uploads/sliders/' . $request->category_file,
                'sort' => $request->sort
            ]);

            if ($slider) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'slider', 'store'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('slider.index')->with('status', 'Slider berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->has('category_file')) {
                if (file_exists(public_path('uploads/sliders/' . $request->category_file))) {
                    unlink(public_path('uploads/sliders/' . $request->category_file));
                }
            }
            return redirect()->route('slider.add')->withErrors($th->getMessage())->withInput();
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
            $path = public_path('uploads/sliders');


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

            $slider = Slider::findOrFail($dec_id);
            $data_sort = Slider::SORT_NUMBER;
            $fileName = '';
            $fileSize = '';
            $filePath = '';
            if (file_exists(public_path($slider->src_image))) {
                $file_path = asset($slider->src_image);

                $size = filesize(public_path($slider->src_image));
                $file = explode('/', $slider->src_image);

                $fileName = $file[2];
                $fileSize = $size;
                $filePath = $file_path;
            }

            return view('content-dashboard.sliders.edit', compact('slider', 'fileName', 'fileSize', 'filePath', 'id', 'data_sort'));
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
                    if (file_exists(public_path('uploads/sliders/' . $request->category_file))) {
                        unlink(public_path('uploads/sliders/' . $request->category_file));
                    }
                }
                return redirect()->route('banner.add')
                    ->withErrors($validator)
                    ->withInput();
            }



            $dec_id = $this->safe_decode(Crypt::decryptString($id));

            $slider = Slider::find($dec_id);

            if (empty($slider)) {
                return redirect()->route('slider.edit', ['id' => $id])->withErrors('Data slider tidak ditemukan')->withInput();
            }

            if ($request->has('category_file')) {
                if (file_exists(public_path($slider->src_image))) {
                    unlink(public_path($slider->src_image)); // delete file before
                }
                $source = 'uploads/sliders/' . $request->category_file;
            } else {
                $source = $slider->src_image;
            }

            if ($request->sort != $slider->sort) {
                // check input sort already exist on another data or not??
                $sortExist = Slider::where('sort', $request->sort)->first();

                if (!empty($sortExist)) {
                    $sortExist->update(['sort' => $slider->sort]); // update sort from data sort on update
                }
            }


            $slider->update([
                'title' => $request->title,
                'src_image' => $source,
                'sort' => $request->sort
            ]);

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'slider', 'update'];

            log_activity($data); // simpan log activity

            DB::commit();

            return redirect()->route('banner.index')->with('status', 'Slider berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            if ($request->has('category_file')) {
                if (file_exists(public_path('uploads/sliders/' . $request->category_file))) {
                    unlink(public_path('uploads/sliders/' . $request->category_file));
                }
            }
            return redirect()->route('banner.edit', ['id' => $id])->withErrors($th->getMessage())->withInput();
        }
    }

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/sliders/' . $request->name);

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
            $slider = Slider::find($dec_id);

            if (empty($slider)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data slider not found'
                ]);
            }

            // // check what data have a file?
            // if ($slider->src_image != '' || $slider->src_image != null || !empty($slider->src_image)) {
            //     // check if data file exists or not, if exists deleted file
            //     if (file_exists(public_path($slider->src_image))) {
            //         unlink(public_path($slider->src_image));
            //     }
            // }

            $slider->delete();

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'slider', 'deletes'];

            log_activity($data); // simpan log activity

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Berhasil Menghapus Data Slider'
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
