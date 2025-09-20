<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HouseStatusEnum;
use App\Enums\PropertyStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeaseRequest;
use App\Models\Lease;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LeaseController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view lease'), 403);
        if (\request()->ajax()) {
            $leases = Lease::query()
                ->with('tenant:id,name', 'property:id,name', 'house:id,name')
                ->select('leases.*')
                ->withSum('bills', 'amount')
                ->latest('id');

            return DataTables::of($leases)
                ->filter(function ($query) {

                    if (request()->filled('property_filter')) {
                        $query->where('property_id', request()->get('property_filter'));
                    }

                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.lease.partials.actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant->name;
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property->name;
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house->name;
                })
                ->addColumn('rent_and_bills', function ($lease) {
                    return view('admin.lease.partials.rent_and_bills', compact('lease'));
                })
                ->editColumn('rent', function ($lease) {
                    return @setting('currency_symbol') . ' ' . number_format($lease->rent, 2);
                })
                ->editColumn('rent_cycle', function ($lease) {
//                    return $lease->rent_cycle . ' ' . ($lease->rent_cycle > 1 ? __('months') : __('month'));
                    return $lease->rent_cycle . ' ' . __($lease->rent_cycle > 1 ? 'months' : 'month');
                })
                ->editColumn('start_date', function ($lease) {
                    return $lease->start_date->format('M d, Y');
                })
//                ->addColumn('lease_dates', function ($lease) {
//                    return view('admin.lease.partials.lease_dates', compact('lease'));
//                })
                ->rawColumns(['actions', 'rent_and_bills', 'lease_dates'])
                ->toJson();
        }

        $properties = Property::orderBy('name')->pluck('name', 'id');
        return view('admin.lease.index', compact('properties'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create lease'), 403);
        return view('admin.lease.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaseRequest $request)
    {
        // Validation is handled by LeaseRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Calculate next billing date
            $nextBillingDate = \Carbon\Carbon::parse($validated['start_date'])
                ->addMonths($validated['rent_cycle'])
                ->day($validated['invoice_generation_day']);

            // Create the lease
            $lease = Lease::create([
                'lease_id' => $validated['lease_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'property_id' => $validated['property_id'],
                'house_id' => $validated['house_id'],
                'tenant_id' => $validated['tenant_id'],
                'rent' => $validated['rent'],
                'rent_cycle' => $validated['rent_cycle'],
                'invoice_generation_day' => $validated['invoice_generation_day'],
                'next_billing_date' => $nextBillingDate,
                'termination_date_notice' => $validated['termination_date_notice'] ?? 30,
                'status' => $validated['status'],
            ]);

            // Update property vacancy status if house is assigned
            if ($validated['house_id']) {
                $house = \App\Models\House::find($validated['house_id']);
                if ($house) {
                    $house->update(['is_vacant' => false]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.lease.show', $lease)
                ->with('success', __('Lease created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to create lease: ') . $e->getMessage());
        }
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view lease'), 403);
        $lease = Lease::withTrashed()
            ->with('tenant:id,name', 'property:id,name', 'house:id,name', 'bills')
            ->findOrFail($id);
        return view('admin.lease.show', compact('lease'));
    }


    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit lease'), 403);
        //restrict trash and draft lease from editing
        $lease = Lease::findOrFail($id);
        return view('admin.lease.edit', compact('id'));
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete lease'), 403);
        $lease = Lease::with('tenant', 'property', 'house')->findOrFail($id);
        DB::transaction(function () use ($lease) {
            if ($lease->house_id != null) {
                $lease->house()->update([
                    'is_vacant' => true,
                    'status' => HouseStatusEnum::VACANT
                ]);
            } else {
                $lease->property()->update([
                        'is_vacant' => true,
                        'status' => PropertyStatusEnum::VACANT
                    ]
                );
            }

            $lease->delete();

            $lease->tenant->notify(new \App\Notifications\TenantLeaseTerminatedNotification());

        });


        return redirect()->route('admin.leases.index')
            ->with('success', __('Lease terminated successfully.Terminated leases can be found in the lease history section.'));
    }
}
