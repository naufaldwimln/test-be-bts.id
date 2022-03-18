<?php

namespace App\Http\Controllers\Logistic;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Courier;

class LogisticController extends Controller
{
     /**
     * Instantiate a new LogisticController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function getAllCourier() {
        return response()->json(['courier' =>  Courier::all()], 200);
    }

    public function singleCourier($origin, $destination) {
        try {
            $data = Courier::where('destination_name', $destination)->where('origin_name', $origin)->first();

            return response()->json(['data' => $data], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'Data not found!'], 404);
        }

    }

}


