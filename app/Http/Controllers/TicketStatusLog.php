<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketStatusLog extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua ticket beserta relasi user (yang ditugaskan), creator, dan statusLogs
        $tickets = \App\Models\Ticket::with(['user', 'creator', 'statusLogs.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('content.apps.History.historyTicket', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
