<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;

class LocationController extends Controller
{
    public function states()
    {
        return response()->json(['success' => true, 'data' => State::all()]);
    }

    public function cities(State $state)
    {
        // Assuming City model has state_id column
        $cities = City::where('state_id', $state->id)->get();

        return response()->json(['success' => true, 'data' => $cities]);
    }
}
