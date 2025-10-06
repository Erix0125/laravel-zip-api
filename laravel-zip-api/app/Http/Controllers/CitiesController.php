<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Models\County;

class CitiesController extends Controller
{
    function index()
    {
        $cities = City::with('county')->get()->map(fn($city) => [
            'id' => $city->id,
            'name' => $city->name,
            'zip' => $city->zip_code,
            'county' => $city->county ? $city->county->name : null,
        ]);
        return response()->json([
            'cities' => $cities
        ]);
    }

    function create(CityRequest $request)
    {
        $data = [
            'name' => $request->input('name'),
            'zip_code' => $request->input('zip_code'),
            'county_id' => County::where('name', $request->input('county'))->value('id'),
        ];

        $city = City::create($data);

        return response()->json([
            'message' => 'City created successfully',
            'city' => $city
        ], 201);
    }
}
