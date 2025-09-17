<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view activity log'), 403);

        if ($request->ajax()) {
            $logs = \Spatie\Activitylog\Models\Activity::latest();

            return DataTables::of($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->filled('date_filter')) {
                        $query->whereDate('created_at', $request->get('date_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('causer', function ($row) {
                    return $row->causer->name ?? 'System';
                })
                ->addColumn('subject', function ($row) {
                    return $row->subject_type;
                })
                ->addColumn('description', function ($row) {
                    return $row->description;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->toDayDateTimeString();
                })
                ->editColumn('properties', function ($row) {
                    $properties = json_decode($row->properties, true);
                    return view('admin.activity_logs.attributes', compact('properties'))->render();
                })
                ->rawColumns(['causer', 'subject', 'description', 'created_at', 'properties'])
                ->make(true);

        }
        return view('admin.activity_logs.index');
    }

    public function show(Request $request, $user)
    {
        abort_unless(auth()->user()->can('view activity log'), 403);

        if ($request->ajax()) {
            $logs = \Spatie\Activitylog\Models\Activity::where('causer_id', $user)->latest();

            return DataTables::of($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->filled('date_filter')) {
                        $query->whereDate('created_at', $request->get('date_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('causer', function ($row) {
                    return $row->causer->name ?? 'System';
                })
                ->addColumn('subject', function ($row) {
                    return $row->subject_type;
                })
                ->addColumn('description', function ($row) {
                    return $row->description;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->toDayDateTimeString();
                })
                ->editColumn('properties', function ($row) {
                    $properties = json_decode($row->properties, true);
                    return view('admin.activity_logs.attributes', compact('properties'))->render();
                })
                ->rawColumns(['causer', 'subject', 'description', 'created_at', 'properties'])
                ->make(true);

        }
        return view('admin.activity_logs.show', compact('user'));
    }

    public function __invoke(Request $request)
    {
        return $this->index($request);
    }
}
