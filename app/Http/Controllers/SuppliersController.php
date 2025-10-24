<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;

class SuppliersController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $pageTitle = 'Suppliers';
    $suppliers = Supplier::all();
    return view('suppliers.index', compact('suppliers', 'pageTitle'));
  }

  public function store(StoreSupplierRequest $request)
  {
    $supplier = Supplier::create($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $supplier->name);
    Session::flash('message', 'Successfully created');

    return redirect()->route('suppliers.index');
  }

  public function edit(Supplier $supplier)
  {
    $pageTitle = 'Edit Supplier - ' . $supplier->name;
    return view('suppliers.edit', compact('supplier', 'pageTitle'));
  }

  public function update(UpdateSupplierRequest $request, Supplier $supplier)
  {
    $supplier->update($request->validated());

    Session::flash('status', 'success');
    Session::flash('title', $supplier->name);
    Session::flash('message', 'Successfully updated');

    return redirect()->route('suppliers.index');
  }
}
