<?php

namespace App\Http\Controllers;

use App\Budget;
use App\Division;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Budgets\StoreBudgetRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class BudgetsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $pageTitle = 'Budgets';
    $budgets = Budget::all();
    $divisions = Division::all();
    return view('budgets.index', compact('budgets', 'divisions', 'pageTitle'));
  }

  public function store(StoreBudgetRequest $request)
  {
    $budget = Budget::create($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', 'Budget for ' . $budget->year);
    Session::flash('message', 'Successfully created');

    return redirect()->route('budgets.index');
  }

  public function edit(Budget $budget)
  {
    $pageTitle = 'Edit Budget - ' . $budget->division->name . ' ' . $budget->year;
    $divisions = Division::all();
    return view('budgets.edit', compact('budget', 'divisions', 'pageTitle'));
  }

  public function update(StoreBudgetRequest $request, Budget $budget)
  {
    $budget->update($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', 'Budget for ' . $budget->year);
    Session::flash('message', 'Successfully updated');

    return redirect()->route('budgets.index');
  }
}
