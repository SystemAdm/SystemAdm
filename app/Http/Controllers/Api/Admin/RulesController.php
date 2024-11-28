<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use Illuminate\Http\Request;

class RulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = Rule::withTrashed()->with('location')->get()->keyBy('id');
        $groupedRules = $rules->groupBy(function ($rule) {
            return $rule->location ? $rule->location->id : 'no_location';
        })->mapWithKeys(function ($rules, $locationId) {
            $locationName = $rules->first()->location ? $rules->first()->location->name : null;
            return [$locationId => [
                'location_id' => $locationId,
                'location_name' => $locationName,
                'rules' => $rules,
            ]];
        });
        return response()->json($groupedRules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);
        $rule = new Rule(['name' => $validated['name'], 'description' => $validated['description']]);
        $rule->save();
        return response()->json($rule);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rule $rule)
    {
        // Validate only the fields that are provided
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
            'pending' => 'nullable|boolean',
        ]);

        // Conditionally update fields
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $rule->$key = $value;
            }
        }

        $rule->save();

        // Return the updated rule
        return response()->json($rule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
