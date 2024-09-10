<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SupportTicketController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view support ticket'), 403);
        if (\request()->ajax()) {
            $tickets = SupportTicket::query()
                ->with('user', 'property', 'house')
                ->latest();

            return DataTables::of($tickets)
                ->filter(function ($query) {
                    if (\request()->filled('property_filter')) {
                        $query->where('property_id', \request()->get('property_filter'));
                    }
                    if (\request()->filled('status_filter')) {
                        $query->where('status', \request()->get('status_filter'));
                    }
                }, true)
                ->addColumn('property', function ($ticket) {
                    return $ticket->property?->name ?? '';
                })
                ->addColumn('house', function ($ticket) {
                    return $ticket->house?->name ?? '';
                })
                ->addColumn('user', function ($ticket) {
                    return $ticket->user->name ?? '';
                })
                ->addColumn('action', function ($ticket) {
                    return view('admin.tickets.partials.actions', compact('ticket'))->render();
                })
                ->editColumn('subject', function ($ticket) {
                    return Str::words($ticket->subject, 5, '...');
                })
                ->editColumn('created_at', function ($ticket) {
                    return $ticket->created_at->format('d M Y h:i A');
                })
                ->editColumn('status', function ($ticket) {
                    return view('admin.tickets.partials.status', compact('ticket'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $properties = Property::pluck('id', 'name');
        return view('admin.tickets.index', compact('properties'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $ticket = SupportTicket::query()
            ->with(['replies.user', 'replies.attachments', 'attachments'])
            ->findOrFail($id);

        return view('admin.tickets.show', compact('ticket'));
    }


    public function edit($id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
