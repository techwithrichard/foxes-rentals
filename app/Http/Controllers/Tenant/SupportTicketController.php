<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{

    public function index()
    {
        $tickets = SupportTicket::query()
            ->where('user_id', auth()->id())
            ->simplePaginate(20);
        return view('tenant.support_tickets.index', compact('tickets'));
    }


    public function create()
    {
        return view('tenant.support_tickets.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $ticket = SupportTicket::query()
            ->with(['replies.user','replies.attachments', 'attachments'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        return view('tenant.support_tickets.show', compact('ticket'));
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
