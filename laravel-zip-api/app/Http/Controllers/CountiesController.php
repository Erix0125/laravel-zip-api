<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;
use PHPUnit\Framework\Constraint\Count;

class CountiesController extends Controller
{
    /**
     * @api {get} /counties List all counties
     * @apiName GetCounties
     * @apiGroup County
     * @apiSuccess {Object[]} counties List of counties.
     * @apiSuccess {Number} counties.id County ID.
     * @apiSuccess {String} counties.name County name.
     */
    function index()
    {
        return response()->json([
            'counties' => County::all()
        ]);
    }

    /**
     * @api {post} /counties Create a new county
     * @apiName CreateCounty
     * @apiGroup County
     * @apiHeader {String} Authorization Bearer token.
     * @apiBody {String} name County name.
     * @apiSuccess (201) {String} message Success message.
     * @apiSuccess (201) {Object} county Created county object.
     * @apiError (401) Unauthorized Only authenticated users can create.
     */
    function create(Request $request)
    {
        $county = County::create([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'message' => 'County created successfully',
            'county' => $county
        ], 201);
    }

    /**
     * @api {patch} /counties/:id Modify a county
     * @apiName ModifyCounty
     * @apiGroup County
     * @apiHeader {String} Authorization Bearer token.
     * @apiParam {Number} id County's unique ID.
     * @apiBody {String} [name] County name.
     * @apiSuccess {String} message Success message.
     * @apiSuccess {Object} county Updated county object.
     * @apiError (404) CountyNotFound County not found.
     * @apiError (401) Unauthorized Only authenticated users can modify.
     */
    function modify(Request $request, $id)
    {
        $county = County::find($id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }

        $county->name = $request->input('name', $county->name);
        $county->save();

        return response()->json([
            'message' => 'County updated successfully',
            'county' => $county
        ]);
    }

    /**
     * @api {delete} /counties/:id Delete a county
     * @apiName DeleteCounty
     * @apiGroup County
     * @apiHeader {String} Authorization Bearer token.
     * @apiParam {Number} id County's unique ID.
     * @apiSuccess {String} message Success message.
     * @apiError (404) CountyNotFound County not found.
     * @apiError (401) Unauthorized Only authenticated users can delete.
     */
    function delete($id)
    {
        $county = County::find($id);
        if (!$county) {
            return response()->json([
                'message' => 'County not found'
            ], 404);
        }

        $county->delete();

        return response()->json([
            'message' => 'County deleted successfully'
        ]);
    }
}
