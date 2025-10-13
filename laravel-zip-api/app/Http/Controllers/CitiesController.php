<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Models\County;

class CitiesController extends Controller
{
    function index($county_id)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }
        $cities = City::where('county_id', $county_id)->get()->map(function ($city) use ($county) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'zip' => $city->zip_code,
                'county' => $county->name,
            ];
        });
        return response()->json([
            'cities' => $cities
        ]);
    }

    function create(CityRequest $request, $county_id)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }

        $data = [
            'name' => $request->input('name'),
            'zip_code' => $request->input('zip_code'),
            'county_id' => $county_id,
        ];

        $city = City::create($data);

        return response()->json([
            'message' => 'City created successfully',
            'city' => $city
        ], 201);
    }

    function modify(CityRequest $request, $county_id, $city_id)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }

        $city = City::where('id', $city_id)->where('county_id', $county_id)->first();
        if (!$city) {
            return response()->json([
                'message' => 'City not found in the specified county'
            ], 404);
        }

        $city->update($request->all());

        return response()->json([
            'message' => 'City updated successfully',
            'city' => $city
        ]);
    }

    function delete($county_id, $city_id)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }

        $city = City::where('id', $city_id)->where('county_id', $county_id)->first();
        if (!$city) {
            return response()->json([
                'message' => 'City not found in the specified county'
            ], 404);
        }

        $city->delete();

        return response()->json([
            'message' => 'City deleted successfully'
        ]);
    }

    function abc($county_id)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }
        $letters = City::where('county_id', $county_id)
            ->pluck('name')
            ->map(function ($name) {
                return mb_strtoupper(mb_substr($name, 0, 1));
            })
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'letters' => $letters
        ]);
    }

    function abcFiltered($county_id, $letter)
    {
        $county = County::find($county_id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }
        $cities = City::where('county_id', $county_id)
            ->where('name', 'LIKE', $letter . '%')
            ->get()
            ->map(function ($city) use ($county) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'zip' => $city->zip_code,
                    'county' => $county->name,
                ];
            });

        return response()->json([
            'cities' => $cities
        ]);
    }
}
