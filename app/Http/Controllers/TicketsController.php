<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Ticket;
use App\TicketsEntry;
use App\TicketsPriority;
use App\TicketsStatus;
use App\TicketsType;
use App\Location;
use App\User;
use App\TicketsCannedField;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;

class TicketsController extends Controller
{
  /**
   * Check if user is logged in
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Show all tickets
   */
  public function index()
  {
    $pageTitle = 'Tickets';
    // Fix N+1 Query: Eager load all relations that will be used in the view
    $tickets = Ticket::with([
        'user',
        'assignedTo', 
        'ticket_status',
        'ticket_priority',
        'ticket_type',
        'location',
        'asset'
    ])->get();
    return view('tickets.index', compact('tickets', 'pageTitle'));
  }

  /**
   * Show the Ticket
   */
  public function show(Ticket $ticket)
  {
    $pageTitle = 'Viewing Ticket #' . $ticket->id;
    $now = new Carbon();
    $ticketEntries = TicketsEntry::where('ticket_id', $ticket->id)->orderBy('created_at', 'asc')->get();
    
    // View composer will provide dropdown data
    return view('tickets.show', compact('ticket', 'ticketEntries', 'pageTitle', 'now'));
  }

  /**
   * Show form for creating a new Ticket
   *
   * @return view 'Create Ticket Form'
   */
  public function create()
  {
    $pageTitle = 'Create New Ticket';
    $ticketsCannedFields = TicketsCannedField::all();
    
    // Create empty ticket object for form
    $ticket = new \stdClass();
    $ticket->user_id = '';
    
    // View composer will provide dropdown data
    return view('tickets.create', compact('ticketsCannedFields', 'pageTitle', 'ticket'));
  }

  /**
   * Store the new Ticket
   * @param  CreateTicketRequest $request
   * @return [type]                       [description]
   */
  public function store(StoreTicketRequest $request)
  {
    Ticket::create($request->all());
    $ticket = Ticket::get()->last();

    $user = User::findOrFail($ticket->user_id);

    Mail::send('emails.new-ticket', ['user' => $user, 'ticket' => $ticket], function ($m) use ($user, $ticket) {
      $m->to($user->email, $user->name)->subject('New Ticket: #' . $ticket->id . ' - ' . $ticket->subject);
    });

    Session::flash('status', 'success');
    Session::flash('title', 'Ticket #' . $ticket->id);
    Session::flash('message', 'Successfully logged');

    return redirect()->route('tickets.show', $ticket->id);
  }

  public function edit(Ticket $ticket)
  {
    $pageTitle = 'Edit Ticket - ' . $ticket->id;
    return view('tickets.edit', compact('ticket', 'pageTitle'));
  }

  public function update(UpdateTicketRequest $request, Ticket $ticket)
  {
    $ticket->update($request->all());

    Session::flash('status', 'success');
    Session::flash('title', 'Ticket #' . $ticket->id);
    Session::flash('message', 'Successfully updated');

    return redirect()->route('tickets.show', $ticket->id);
  }
}
