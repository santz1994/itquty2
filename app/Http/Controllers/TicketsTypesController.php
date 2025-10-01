<?php

namespace App\Http\Controllers;

use App\TicketsType;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\TicketsTypes\StoreTicketsTypeRequest;
use App\Http\Requests\TicketsTypes\UpdateTicketsTypeRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class TicketsTypesController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $user = auth()->user();
  if (!$user || !($user->role === 'super-admin' || $user->role === 'admin')) {
      abort(403);
    }
    $pageTitle = 'Ticket Types';
    $ticketsTypes = TicketsType::all();
    return view('admin.ticket-types.index', compact('pageTitle', 'ticketsTypes'));
  }

  public function store(StoreTicketsTypeRequest $request)
  {
    $user = auth()->user();
    if (!$user || !in_array($user->role, ['super-admin', 'admin'])) {
      abort(403);
    }
  TicketsType::create($request->all());
  $ticketsType = TicketsType::get()->last();

  Session::flash('status', 'success');
  Session::flash('title', 'Ticket Type: ' . $ticketsType->type);
  Session::flash('message', 'Successfully created');

  return redirect()->route('admin.ticket-types.index')
    ->with('message', 'Successfully created')
    ->with('status', 'success')
    ->with('title', 'Ticket Type: ' . $ticketsType->type);
  }

  public function edit(TicketsType $ticketsType)
  {
    $user = auth()->user();
    if (!$user || !in_array($user->role, ['super-admin', 'admin'])) {
      abort(403);
    }
    $pageTitle = 'Edit Ticket Type - ' . $ticketsType->type;
    $ticketsTypes = TicketsType::all();
    return view('admin.ticket-types.edit', compact('pageTitle', 'ticketsTypes', 'ticketsType'));
  }

  public function update(UpdateTicketsTypeRequest $request, TicketsType $ticketsType)
  {
    $user = auth()->user();
    if (!$user || !in_array($user->role, ['super-admin', 'admin'])) {
      abort(403);
    }
  $ticketsType->update($request->all());

  Session::flash('status', 'success');
  Session::flash('title', 'Ticket Type: ' . $ticketsType->type);
  Session::flash('message', 'Successfully updated');

  return redirect()->route('admin.ticket-types.index')
    ->with('message', 'Successfully updated')
    ->with('status', 'success')
    ->with('title', 'Ticket Type: ' . $ticketsType->type);
  }
}
