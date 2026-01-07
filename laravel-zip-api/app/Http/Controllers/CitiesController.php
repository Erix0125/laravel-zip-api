<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Models\County;

setlocale(LC_COLLATE, 'hu_HU.UTF-8', 'hu_HU', 'hun_HUN', 'hu');

class CitiesController extends Controller
{
    /**
     * @api {get} /counties/:county_id/cities List all cities in a county
     * @apiName GetCities
     * @apiGroup City
     * @apiParam {Number} county_id County's unique ID.
     * @apiSuccess {Object[]} cities List of cities.
     * @apiSuccess {Number} cities.id City ID.
     * @apiSuccess {String} cities.name City name.
     * @apiSuccess {String} cities.zip City zip code.
     * @apiSuccess {String} cities.county County name.
     * @apiError (404) CountyNotFound County not found.
     */
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

    /**
     * @api {post} /counties/:county_id/cities Create a new city in a county
     * @apiName CreateCity
     * @apiGroup City
     * @apiHeader {String} Authorization Bearer token.
     * @apiParam {Number} county_id County's unique ID.
     * @apiBody {String} name City name.
     * @apiBody {String} zip_code City zip code.
     * @apiSuccess (201) {String} message Success message.
     * @apiSuccess (201) {Object} city Created city object.
     * @apiError (404) CountyNotFound County not found.
     * @apiError (401) Unauthorized Only authenticated users can create.
     */
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

    /**
     * @api {patch} /counties/:county_id/cities/:city_id Modify a city in a county
     * @apiName ModifyCity
     * @apiGroup City
     * @apiHeader {String} Authorization Bearer token.
     * @apiParam {Number} county_id County's unique ID.
     * @apiParam {Number} city_id City's unique ID.
     * @apiBody {String} [name] City name.
     * @apiBody {String} [zip_code] City zip code.
     * @apiSuccess {String} message Success message.
     * @apiSuccess {Object} city Updated city object.
     * @apiError (404) CountyNotFound County not found.
     * @apiError (404) CityNotFound City not found in the specified county.
     * @apiError (401) Unauthorized Only authenticated users can modify.
     */
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

    /**
     * @api {delete} /counties/:county_id/cities/:city_id Delete a city in a county
     * @apiName DeleteCity
     * @apiGroup City
     * @apiHeader {String} Authorization Bearer token.
     * @apiParam {Number} county_id County's unique ID.
     * @apiParam {Number} city_id City's unique ID.
     * @apiSuccess {String} message Success message.
     * @apiError (404) CountyNotFound County not found.
     * @apiError (404) CityNotFound City not found in the specified county.
     * @apiError (401) Unauthorized Only authenticated users can delete.
     */
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

    /**
     * @api {get} /counties/:county_id/abc List first letters of city names in a county
     * @apiName GetCityFirstLetters
     * @apiGroup City
     * @apiParam {Number} county_id County's unique ID.
     * @apiSuccess {String[]} letters List of first letters (uppercase).
     * @apiError (404) CountyNotFound County not found.
     */
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
            ->toArray();

        // Hungarian collation sort
        $hungarianOrder = [
            'A' => 0,
            'Á' => 0.5,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'É' => 4.5,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'Í' => 8.5,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'Ó' => 14.5,
            'Ö' => 15,
            'Ő' => 15.5,
            'P' => 16,
            'Q' => 17,
            'R' => 18,
            'S' => 19,
            'T' => 20,
            'U' => 21,
            'Ú' => 21.5,
            'Ü' => 22,
            'Ű' => 22.5,
            'V' => 23,
            'W' => 24,
            'X' => 25,
            'Y' => 26,
            'Z' => 27
        ];

        usort($letters, function ($a, $b) use ($hungarianOrder) {
            $orderA = $hungarianOrder[$a] ?? 999;
            $orderB = $hungarianOrder[$b] ?? 999;
            return $orderA <=> $orderB;
        });

        return response()->json([
            'letters' => array_values($letters)
        ]);

        // $county = County::find($county_id);
        // if (!$county) {
        //     return response()->json([
        //         'message' => 'County not found'
        //     ], 404);
        // }
        // $letters = City::where('county_id', $county_id)
        //     ->pluck('name')
        //     ->map(function ($name) {
        //         return mb_strtoupper(mb_substr($name, 0, 1));
        //     })
        //     ->unique()
        //     ->sort()
        //     ->values();

        // return response()->json([
        //     'letters' => $letters
        // ]);
    }

    /**
     * @api {get} /counties/:county_id/abc/:letter List cities in a county starting with a letter
     * @apiName GetCitiesByFirstLetter
     * @apiGroup City
     * @apiParam {Number} county_id County's unique ID.
     * @apiParam {String} letter First letter of city name (uppercase).
     * @apiSuccess {Object[]} cities List of cities.
     * @apiError (404) CountyNotFound County not found.
     */
    function abcFiltered($county_id, $letter)
    {
        $letter = mb_strtolower($letter, "UTF-8");
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

        $count = count($cities);
        for ($i = 0; $i < $count; $i++) {
            if (mb_strtolower(mb_substr($cities[$i]['name'], 0, 1, "UTF-8"), "UTF-8") != $letter) {
                $cities->forget($i);
            }
        }

        return response()->json([
            'cities' => $cities,
        ]);
    }
}
