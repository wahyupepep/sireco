<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        // check on table about already exists data or not
        $about = About::first();

        if ($about) { // redirect to edit page
            $enc_id = $this->safe_encode(crypt::encryptString($about->id));
            $data_images = [$about->favicon, $about->logo, $about->owner_photo, $about->image_url];
            $data_images = [
                [
                    'type' => 'favicon',
                    'fileName' => $about->favicon
                ],
                [
                    'type' => 'logo',
                    'fileName' => $about->logo
                ],
                [
                    'type' => 'owner',
                    'fileName' => $about->owner_photo
                ],
                [
                    'type' => 'image_url',
                    'fileName' => $about->image_url
                ],
            ];

            $collection_data_images = collect($data_images);


            return view('content-dashboard.settings.general.edit', compact('enc_id', 'about', 'collection_data_images'));
        }

        return view('content-dashboard.settings.general.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'meta_title' => 'required|string',
                'meta_description' => 'required|string|max:175',
                'copyright' => 'required|string',
                'owner_name' => 'required|string',
                'owner_statement' => 'required|string',
                'email' => 'required|string|email',
                'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'phone_order' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'phone_kemitraan' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'address' => 'required|string',
                'txt_order' => 'required|string',
                'txt_kemitraan' => 'required|string',
                'maps' => 'required|string',
                'facebook' => 'required|string',
                'instagram' => 'required|string',
                'whatsapp' => 'required|string',
                'category_file_seo.*' => 'required|string'
            ]);

            if ($validator->fails()) {
                $this->deleteImageByUrl($request->category_file_seo);
                return redirect()->route('setting.general.index')
                    ->withErrors($validator)
                    ->withInput();
            }

            // check what input category file is four
            if (count($request->category_file_seo) < 4) {
                $this->deleteImageByUrl($request->category_file_seo);
                return redirect()->route('setting.general.index')
                    ->withErrors('File upload harus 4 gambar')
                    ->withInput();
            }

            // check format link is match or not??
            $arrUrl = [
                [
                    'name' => 'maps',
                    'url' => $request->maps,
                ],
                [
                    'name' => 'facebook',
                    'url' => $request->facebook,
                ],
                [
                    'name' => 'instagram',
                    'url' => $request->instagram,
                ],
                [
                    'name' => 'whatsapp',
                    'url' => $request->whatsapp,
                ],
            ];

            $url = collect($arrUrl);

            if ($this->checkingUrlMatchFormat($url)) {
                $this->deleteImageByUrl($request->category_file_seo);
                $obj = $this->checkingUrlMatchFormat($url);
                return redirect()->route('setting.general.index')
                    ->withErrors('Format input link ' . $obj['name'] . ' tidak sesuai')
                    ->withInput();
            }

            $about = About::create([
                'name'  => $request->title,
                'title' => $request->title,
                'favicon' => 'uploads/abouts/' . $request->category_file_seo[0],
                'logo' => 'uploads/abouts/' . $request->category_file_seo[1],
                'email' => $request->email,
                'address' => $request->address,
                'phone_company' => $request->phone,
                'phone_order' => $request->phone_order,
                'phone_mitra' => $request->phone_kemitraan,
                'txt_phone_order' => $request->txt_order,
                'txt_phone_mitra' => $request->txt_kemitraan,
                'url' => $request->getHttpHost(),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'url_facebook' => $request->facebook,
                'url_instagram' => $request->instagram,
                'url_whatsapp' => $request->whatsapp,
                'url_maps' => $request->maps,
                'copyright' => $request->copyright,
                'owner' => $request->owner_name,
                'owner_photo' => 'uploads/abouts/' . $request->category_file_seo[2],
                'owner_statement' => $request->owner_statement,
                'image_url' => 'uploads/abouts/' . $request->category_file_seo[3]
            ]);

            if ($about) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'setting-general', 'store'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('setting.general.index')->with('status', 'Pengaturan umum berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollback();
            $this->deleteImageByUrl($request->category_file_seo);
            return redirect()->route('setting.general.index')->withErrors($th->getMessage() . 'and line code ' . $th->getLine())->withInput();
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
            $path = public_path('uploads/abouts');


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

    public function edit(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'meta_title' => 'required|string',
                'meta_description' => 'required|string|max:175',
                'copyright' => 'required|string',
                'owner_name' => 'required|string',
                'owner_statement' => 'required|string',
                'email' => 'required|string|email',
                'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'phone_order' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'phone_kemitraan' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'address' => 'required|string',
                'txt_order' => 'required|string',
                'txt_kemitraan' => 'required|string',
                'maps' => 'required|string',
                'facebook' => 'required|string',
                'instagram' => 'required|string',
                'whatsapp' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('setting.general.index')
                    ->withErrors($validator)
                    ->withInput();
            }

            // check what input category file is four
            if (count($request->category_file_seo) < 4) {
                return redirect()->route('setting.general.index')
                    ->withErrors('File upload harus 4 gambar')
                    ->withInput();
            }

            // check format link is match or not??
            $arrUrl = [
                [
                    'name' => 'maps',
                    'url' => $request->maps,
                ],
                [
                    'name' => 'facebook',
                    'url' => $request->facebook,
                ],
                [
                    'name' => 'instagram',
                    'url' => $request->instagram,
                ],
                [
                    'name' => 'whatsapp',
                    'url' => $request->whatsapp,
                ],
            ];

            $url = collect($arrUrl);

            if ($this->checkingUrlMatchFormat($url)) {
                $obj = $this->checkingUrlMatchFormat($url);
                return redirect()->route('setting.general.index')
                    ->withErrors('Format input link ' . $obj['name'] . ' tidak sesuai')
                    ->withInput();
            }

            $dec_id = $this->safe_decode(Crypt::decryptString($request->about_id));

            $about = About::find($dec_id);

            if (empty($about)) {
                return redirect()->route('setting.general.index')
                    ->withErrors('Data pengaturan tidak ditemukan')
                    ->withInput();
            }

            $about = $about->update([
                'name'  => $request->title,
                'title' => $request->title,
                'email' => $request->email,
                'address' => $request->address,
                'phone_company' => $request->phone,
                'phone_order' => $request->phone_order,
                'phone_mitra' => $request->phone_kemitraan,
                'txt_phone_order' => $request->txt_order,
                'txt_phone_mitra' => $request->txt_kemitraan,
                'url' => $request->getHttpHost(),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'url_facebook' => $request->facebook,
                'url_instagram' => $request->instagram,
                'url_whatsapp' => $request->whatsapp,
                'url_maps' => $request->maps,
                'copyright' => $request->copyright,
                'owner' => $request->owner_name,
                'owner_statement' => $request->owner_statement,
            ]);

            if ($about) {
                $agent = new Agent();

                $data = [$request->ip(), $agent->device(), $agent->browser(), 'setting-general', 'update'];

                log_activity($data); // simpan log activity
            }

            DB::commit();

            return redirect()->route('setting.general.index')->with('status', 'Pengaturan umum berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('setting.general.index')->withErrors($th->getMessage())->withInput();
        }
    }

    public function deleteImage(Request $request)
    {
        try {

            $path = public_path('uploads/abouts/' . $request->name);

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

    public function newUpload(Request $request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                "file_new" => 'required|mimes:jpg,jpeg,png,svg|file|max:1048',
            ], [
                'file_new.required' => 'File must be filled',
                'file_new.mimes' => 'File must be jpg/jpeg/png/svg',
                'file_new.size'  => 'File must be lower than 2 MB'
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'code'    => 500,
                    'success' => false,
                    'message' => $validation->errors()->first()
                ], 500);
            }

            // check data by id
            $dec_id = $this->safe_decode(Crypt::decryptString($request->id));

            $about = About::find($dec_id);

            if (empty($about)) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Data about not found'
                ]);
            }

            // upload new file  based on type and update data by type

            $path = public_path('uploads/abouts');


            $file = $request->file('file_new');

            $name = uniqid() . '_' . trim($file->getClientOriginalName());

            $file->move($path, $name);

            $file_path = 'uploads/abouts/' . $name;

            if ($request->type == 'favicon') {
                $about->update(['favicon' => $file_path]);
            } else if ($request->type == 'logo') {
                $about->update(['logo' => $file_path]);
            } else if ($request->type == 'owner') {
                $about->update(['owner_photo' => $file_path]);
            } else if ($request->type == 'image_url') {
                $about->update(['image_url' => $file_path]);
            }

            // delete old file upload
            if (file_exists(public_path($request->file_old))) {
                unlink(public_path($request->file_old));
            }

            DB::commit();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'OK',
                'data' => [
                    'filePath' => 'uploads/abouts/' . $name,
                    'assetFilePath' => asset('uploads/abouts/' . $name)
                ]
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

    public function previewImage(Request $request)
    {
        try {
            $dec_id = $this->safe_decode(crypt::decryptString($request->id));

            $about = About::find($dec_id);

            if (empty($about)) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Data about not found'
                ]);
            }

            $data_images = [
                [
                    'type' => 'favicon',
                    'fileName' => $about->favicon
                ],
                [
                    'type' => 'logo',
                    'fileName' => $about->logo
                ],
                [
                    'type' => 'owner',
                    'fileName' => $about->owner_photo
                ],
                [
                    'type' => 'image_url',
                    'fileName' => $about->image_url
                ],
            ];

            $collection_data_images = collect($data_images);

            $src = [];

            if ($collection_data_images->count() > 0) {
                foreach ($collection_data_images as $data_image) {
                    if (file_exists(public_path($data_image['fileName']))) {
                        $file_path = asset($data_image['fileName']);

                        $size = filesize(public_path($data_image['fileName']));
                        // $file = explode('/', $data_image['fileName']);

                        $src[] = [
                            // 'fileName' => $file[2],
                            // 'fileSize' => $size,
                            // 'filePath' => $file_path
                            'filePath' => $file_path,
                            'file' => $data_image['fileName'],
                            'type' => $data_image['type']
                        ];
                    }
                }
            }

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'OK',
                'data' => [
                    'src' => $src,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    private function checkingUrlMatchFormat($arrUrl)
    {

        $getCheckUrl = $arrUrl->first(function ($item, $key) {
            return !filter_var($item['url'], FILTER_VALIDATE_URL);
        });

        return empty($getCheckUrl) ? false : $getCheckUrl;
    }

    private function deleteImageByUrl($arrUrlImage)
    {

        if (count($arrUrlImage) == 1) {
            if (file_exists(public_path('uploads/abouts/' . $arrUrlImage[0]))) unlink(public_path('uploads/abouts/' . $arrUrlImage[0]));
        }

        for ($i = 0; $i < count($arrUrlImage); $i++) {
            if (file_exists(public_path('uploads/abouts/' . $arrUrlImage[$i]))) unlink(public_path('uploads/abouts/' . $arrUrlImage[$i]));
        }

        return true;
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
