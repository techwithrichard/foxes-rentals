<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExpensesController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view expense'), 403);
        if (\request()->ajax()) {


            $expenses = Expense::query()
                ->with('landlord:id,name', 'property:id,name', 'category:id,name', 'house:id,name')
                ->select('expenses.*')
                ->latest();


            return DataTables::of($expenses)
                ->filter(function ($query) {
                    if (request()->filled('category_filter')) {
                        $query->where('expense_type_id', request('category_filter'));
                    }
                    if (request()->filled('date_filter')) {
                        $query->where('incurred_on', request('date_filter'));
                    }

                }, true)
                ->addIndexColumn()
                ->addColumn('landlord', function ($expense) {
                    return $expense->landlord->name ?? '';
                })
                ->addColumn('property', function ($expense) {
                    return $expense->property->name ?? '';
                })
                ->addColumn('category', function ($expense) {
                    return $expense->category->name ?? __('Default');
                })
                ->addColumn('house', function ($expense) {
                    return $expense->house->name ?? '';
                })
                ->addColumn('actions', function ($expense) {
                    return view('admin.expenses.partials.actions', compact('expense'));
                })
                ->editColumn('amount', function ($expense) {
                    return setting('currency_symbol') . ' ' . number_format($expense->amount, 2);
                })
                ->editColumn('incurred_on', function ($expense) {
                    return $expense->incurred_on->format('d M, Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = ExpenseType::orderBy('name')->pluck('name', 'id');


        return view('admin.expenses.index', compact('categories'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create expense'), 403);
        return view('admin.expenses.create');
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view expense'), 403);
        $expense = Expense::query()
            ->with('landlord:id,name', 'property:id,name', 'category:id,name', 'house:id,name')
            ->findOrFail($id);

        return view('admin.expenses.show', compact('expense'));
    }


    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit expense'), 403);
        return view('admin.expenses.edit', compact('id'));
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete expense'), 403);
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', __('Expense deleted successfully'));
    }
}
