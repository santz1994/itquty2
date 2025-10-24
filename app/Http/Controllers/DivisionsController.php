<?php

namespace App\Http\Controllers;

use App\Division;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Divisions\StoreDivisionRequest;
use App\Http\Requests\Divisions\UpdateDivisionRequest;

class DivisionsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $pageTitle = 'Divisions';
    $divisions = Division::all();
    return view('divisions.index', compact('divisions', 'pageTitle'));
  }

  public function store(StoreDivisionRequest $request)
  {
    $division = Division::create($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $division->name);
    Session::flash('message', 'Successfully created');

    return redirect()->route('divisions.index');
  }

  public function edit(Division $division)
  {
    $pageTitle = 'Edit Division - ' . $division->name;
    return view('divisions.edit', compact('division', 'pageTitle'));
  }

  public function update(UpdateDivisionRequest $request, Division $division)
  {
    $division->update($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $division->name);
    Session::flash('message', 'Successfully updated');

    return redirect()->route('divisions.index');
  }
}
