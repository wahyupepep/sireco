<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Partner::all();
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
                ->addColumn('action', function ($row) {
                    $id = $this->safe_encode(Crypt::encryptString($row->id));
                    $btn = '<button class="delete btn btn-danger-material btn-sm mdi mdi-delete ml-2"  data-id=' . $id . '></button>';

                    return $btn;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('content-dashboard.partners.index');
    }

    public function add()
    {

        return view('content-dashboard.partners.add');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'category_file.*' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->route('partner.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $partner = collect($request->category_file)->map(function ($item, $key) {
                Partner::create([
                    'src_image' => 'uploads/partners/' . $item
                ]);
                return true;
            });

            // checking if data array partner is bool false
            if (in_array(false, $partner->toArray())) {
                return redirect()->route('partner.add')->withErrors('Terdapat salah satu data yang tidak input ke database')->withInput();
            }

            $agent = new Agent();

            $data = [$request->ip(), $agent->device(), $agent->browser(), 'partner', 'store'];

            log_activity($data); // simpan log activity


            DB::commit();

            return redirect()->route('partner.index')->with('status', 'Mitra berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('partner.add')->withErrors($th->getMessage())->withInput();
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
            $path = public_path('uploads/partners');


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

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/partners/' . $request->name);

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
