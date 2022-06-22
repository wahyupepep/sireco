<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Room::all();

            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
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
                    $btn = '<a href="' . route('master.room.edit', Crypt::encryptString($row->id)) . '" class="edit-brand btn btn-gradient-warning btn-sm"><i class="mdi mdi-lead-pencil"></i></a>';
                    $btn .= '<button class="delete-room btn btn-gradient-danger btn-sm ml-1" data-id=' .  Crypt::encryptString($row->id) . ' data-name="' . $row->name . '"><i class="mdi mdi-delete"></i></button>';
                    return $btn;
                })

                ->rawColumns(['name', 'action'])

                ->make(true);
        }
        return view('content-dashboard.masters.rooms.index');
    }

    public function add()
    {
        return view('content-dashboard.masters.rooms.add');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.room.add')
                    ->withErrors($validator)
                    ->withInput();
            }
            $data_room = Room::where('name', 'like', "%{$request->name}%")->exists();


            if ($data_room) {
                return redirect()->route('master.room.add')->withErrors('Room already exists')->withInput();
            }

            $store_room = Room::create([
                'name' => $request->name,
            ]);

            if ($store_room) {
                DB::commit();
                return redirect()->route('master.room.index')->with('status', 'Successfully add room');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.room.add')->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $room = Room::find(Crypt::decryptString($id), ['id', 'name']);

            if (empty($room)) {
                return redirect()->route('master.room.edit', ['id' => $id])->withErrors('Data room Not Found')->withInput();
            }

            return view('content-dashboard.masters.rooms.edit', compact('room', 'id'));
        } catch (\Throwable $th) {
            return redirect()->route('master.room.edit', ['id' => $id])->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('master.room.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput();
            }

            $room = Room::find(Crypt::decryptString($id));

            if (empty($room)) {
                return redirect()->route('master.room.edit', ['id' => $id])->withErrors('Data Room Not Found')->withInput();
            }

            $room->update([
                'name' => $request->name,
            ]);

            DB::commit();

            return redirect()->route('master.room.index')->with('status', 'Successfully update room');
        } catch (\Throwable $th) {
            return redirect()->route('master.room.edit', ['id' => $id])->withErrors($th->getMessage() . ' on the line ' . $th->getLine())->withInput();
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $room = Room::find(Crypt::decryptString($request->id));

            if (empty($room)) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }

            $room->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Successfully delete data room'
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
