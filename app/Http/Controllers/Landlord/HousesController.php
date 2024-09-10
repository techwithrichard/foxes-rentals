<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\House;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HousesController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {
            $houses = House::with('property:id,name', 'landlord:id,name')
                ->where('landlord_id', auth()->id())
                ->select('houses.*')
                ->latest();


            return DataTables::of($houses)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }

                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($house) {
//                    return view('admin.house.partials.actions', compact('house'));
                    return '';
                })
                ->editColumn('status', function ($house) {
                    return view('admin.house.partials.status', compact('house'));
                })
                //edit rent column
                ->editColumn('rent', function ($house) {
                    return number_format($house->rent, 2);
                })
                ->addColumn('rent_status', function ($house) {
                    return view('admin.house.partials.rent', compact('house'));
                })
                ->addColumn('property', function ($house) {
                    return $house->property->name;
                })
                ->rawColumns(['actions', 'is_vacant', 'rent_status'])
                ->make(true);

        }
        return view('landlord.houses.index');

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
