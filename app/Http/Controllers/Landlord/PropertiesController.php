<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PropertiesController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {
            $properties = Property::query()
                ->with('address')
                ->where('landlord_id', auth()->user()->id)
                ->select('properties.*')
                ->withCount(['houses' => function ($query) {
                    $query->where('landlord_id', auth()->user()->id);
                }])
                ->latest('id');


            return DataTables::of($properties)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('status', function ($property) {
                    return view('admin.property.partials.status', compact('property'));
                })
                ->addColumn('details', function ($property) {
                    return view('landlord.properties.details', compact('property'));
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->rawColumns(['actions', 'status', 'details'])
                ->make(true);
        }

        return view('landlord.properties.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
