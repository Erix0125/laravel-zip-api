<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;
use PHPUnit\Framework\Constraint\Count;

class CountiesController extends Controller
{
    function index()
    {
        return response()->json([
            'cities' => County::all()
        ]);
    }

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
