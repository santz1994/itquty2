<?php

namespace App\Http\Controllers;

use App\Pcspec;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Pcspecs\StorePcspecRequest;

class PcspecsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $pageTitle = 'PC Specifications';
    $pcspecs = Pcspec::all();
    return view('pcspecs.index', compact('pcspecs', 'pageTitle'));
  }

  public function store(StorePcspecRequest $request)
  {
    $pcspec = Pcspec::create($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $pcspec->cpu . ', ' . $pcspec->ram . ', ' . $pcspec->hdd);
    Session::flash('message', 'Successfully created');

    return redirect()->route('pcspecs.index');
  }

  public function edit(Pcspec $pcspec)
  {
    $pageTitle = 'Edit PC Specification - ' . $pcspec->cpu . ', ' . $pcspec->ram . ', ' . $pcspec->hdd;
    return view('pcspecs.edit', compact('pcspec', 'pageTitle'));
  }

  public function update(StorePcspecRequest $request, Pcspec $pcspec)
  {
    $pcspec->update($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $pcspec->cpu . ', ' . $pcspec->ram . ', ' . $pcspec->hdd);
    Session::flash('message', 'Successfully updated');

    return redirect()->route('pcspecs.index');
  }

}
