<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Shopping;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\DB;

class ShoppingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function getAllUser() {
        return response()->json(['users' =>  User::all()], 200);
    }

    public function getAllShopping() {
        return response()->json(['data' =>  Shopping::all()], 200);
    }

    public function show($id) {
        $data = Shopping::find($id);
        if ($data) {
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['data' => []], 200);
        }
    }

    public function store(Request $request) {
    
        //validate incoming request 
        $validator = Validator::make($request->shopping,[
            'name' => 'required',
            'createddate' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }

        DB::beginTransaction();
        try {
            $shop = new Shopping;
            $shop->name = $request->shopping['name'];
            $shop->createdate = $request->shopping['createddate'];
            
            $shop->save();
            DB::commit();

            $data = array(
                'data' => $shop,
            );

            //return successful response
            return response()->json($data, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            //return error message
            return response()->json(['message' => 'Gagal menambah!' ], 409);
        }

    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->shopping,[
            'name' => 'required',
            'createddate' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }

        DB::beginTransaction();
        try {
            $shop = Shopping::find($id);
            if (!$shop) {
                return response()->json(['message' => 'Gagal, data tidak ditemukan!' ], 409);
            }
            $shop->name = $request->shopping['name'];
            $shop->createdate = $request->shopping['createddate'];
            
            $shop->save();
            DB::commit();

            $data = array(
                'data' => $shop,
            );

            //return successful response
            return response()->json($data, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            //return error message
            return response()->json(['message' => 'Gagal Mengubah!' ], 409);
        }
    }

    public function destroy($id) {
        $data = Shopping::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['message' => 'Berhasil menghapus'], 200);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 200);
        }
    }

    // public function getAllCourier() {
    //     return response()->json(['courier' =>  Courier::all()], 200);
    // }

    // public function singleCourier($origin, $destination) {
    //     try {
    //         $data = Courier::where('destination_name', $destination)->where('origin_name', $origin)->first();

    //         return response()->json(['data' => $data], 200);

    //     } catch (\Exception $e) {

    //         return response()->json(['message' => 'Data not found!'], 404);
    //     }

    // }

}


