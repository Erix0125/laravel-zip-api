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
            ->sort(SORT_LOCALE_STRING)
            ->values();

        return response()->json([
            'letters' => $letters
        ]);
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
