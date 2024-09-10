<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExpensesController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {


            $expenses = Expense::query()
                ->with('property:id,name', 'category:id,name', 'house:id,name')
                ->where('landlord_id', auth()->user()->id);


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
                ->addColumn('property', function ($expense) {
                    return $expense->property->name ?? '';
                })
                ->addColumn('category', function ($expense) {
                    return $expense->category->name ?? __('Default');
                })
                ->addColumn('house', function ($expense) {
                    return $expense->house->name ?? '';
                })
                ->editColumn('amount', function ($expense) {
                    return setting('currency_symbol') . ' ' . number_format($expense->amount, 2);
                })
                ->editColumn('incurred_on', function ($expense) {
                    return $expense->incurred_on->format('d M, Y');
                })
                ->addColumn('action', function ($expense) {
                    return view('landlord.expenses.action', compact('expense'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = ExpenseType::orderBy('name')->pluck('name', 'id');


        $current_month_expenses = Expense::query()
            ->whereBetween('incurred_on', [date('Y-m-01'), date('Y-m-t')])
            ->where('landlord_id', auth()->id())
            ->sum('amount');

        return view('landlord.expenses.index', compact('current_month_expenses', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
