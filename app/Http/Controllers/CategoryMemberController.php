<?php

namespace App\Http\Controllers;

use App\Models\CategoryMember;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryMemberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CategoryMember::all();
            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('num_price', function ($row) {
                    return "Rp " . number_format($row->price, 2, ',', '.');
                })
                ->addColumn('discount', function ($row) {
                    return $row->discount == 0 ? '-' : $row->discount . '%';
                })
                ->addColumn('action', function ($row) {
                    // $user = User::find(Auth::user()->id);
                    // if ($user->hasRole('super admin')) {
                    //     $btn = '<a href="' . route('merk.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    //     $btn .= '<button class="delete-brand btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    //     return $btn;
                    // } else {
                    //     return '<button class="btn btn-gradient-info btn-sm ml-1" title="Silahkan hubungi super admin untuk edit dan delete"><i class="mdi mdi-lock"></i></button>';
                    // }
                    $btn = '<a href="' . route('master.category_member.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    $btn .= '<button class="delete-category-member btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'num_price', 'discount', 'action'])

                ->make(true);
        }
        return view('content-dashboard.masters.category_members.index');
    }

    public function add()
    {
        $type_members = CategoryMember::TYPE_MEMBER;
        return view('content-dashboard.masters.category_members.add', compact('type_members'));
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.category_member.add')
                    ->withErrors($validator)
                    ->withInput();
            }

            $data_type_member = CategoryMember::where('name', 'like', "%{$request->name}%")->exists();


            if ($data_type_member) {
                return redirect()->route('master.category_member.add')->withErrors('Member type already exists')->withInput();
            }

            $change_price = str_replace('.', '', $request->price);

            $store_category_member = CategoryMember::create([
                'name' => $request->name,
                'price' => $change_price,
                'discount' => $request->discount ?? 0
            ]);

            if ($store_category_member) {
                DB::commit();
                return redirect()->route('master.category_member.index')->with('status', 'Successfully add category member');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.category_member.add')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $category_member = CategoryMember::find(Crypt::decryptString($id));

            $type_members = CategoryMember::TYPE_MEMBER;

            if (empty($category_member)) {
                return redirect()->route('master.category_member.edit', ['id' => $id])->withErrors('Data Category Member Found')->withInput();
            }

            return view('content-dashboard.masters.category_members.edit', compact('type_members', 'category_member', 'id'));
        } catch (\Throwable $th) {
            return redirect()->route('master.category_member.edit', ['id' => $id])->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.category_member.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput();
            }

            $category_member = CategoryMember::find(Crypt::decryptString($id));

            if (empty($category_member)) {
                return redirect()->route('master.category_member.edit', ['id' => $id])->withErrors('Data Category Member Not Found')->withInput();
            }

            $change_price = str_replace('.', '', $request->price);

            $category_member->update([
                'name' => $request->name,
                'price' => $change_price,
                'discount' => $request->discount ?? 0
            ]);

            DB::commit();

            return redirect()->route('master.category_member.index')->with('status', 'Successfully update category member');
        } catch (\Throwable $th) {
            return redirect()->route('master.category_member.edit', ['id' => $id])->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $category_member = CategoryMember::find(Crypt::decryptString($request->id));

            if (empty($category_member)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }

            $category_member->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Successfully delete data category member'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $th->getMessage() . ' on the line ' . $th->getLine()
            ], 500);
        }
    }
}
