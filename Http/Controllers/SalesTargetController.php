<?php

namespace Modules\CommissionAgent\Http\Controllers;

use App\Category;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\CommissionAgent\Entities\SalesTarget;
use Illuminate\Routing\Controller;

class SalesTargetController extends Controller
{

    public function index()
    {
        if (!auth()->user()->can('sales_targets.view') && !auth()->user()->can('sales_targets.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $salesTargets = SalesTarget::with(['user', 'category'])
                ->select(['sales_targets.*'])->latest();

            return Datatables::of($salesTargets)
                ->addIndexColumn()
                ->editColumn('user_id', function ($row) {
                    return $row->user->first_name ?? '-';
                })
                ->editColumn('category_id', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('type', function ($row) {
                    return $row->commission_type === 'percentage'
                        ? '<span class="badge bg-green">Percentage</span>'
                        : '<span class="badge bg-blue">Fixed</span>';
                })
                ->addColumn('sales_target', function ($row) {
                    return number_format($row->minimum_sales, 2) . ' - ' . number_format($row->maximum_sales, 2);
                })
                ->addColumn('period', function ($row) {
                    return \Carbon\Carbon::parse($row->start_date)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('M d, Y');
                })

                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d') ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('sales_targets.update')) {
                        $actions .= '<a href="' . route('sales-targets.edit', $row->id) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary" title=' . __('messages.edit') . '><i class="glyphicon glyphicon-edit"></i> </a>&nbsp;';
                    }

                    if (auth()->user()->can('sales_targets.delete')) {
                        $actions .= '<button data-href="' . route('sales-targets.destroy', $row->id) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error delete_sales_target_button" title=' . __('messages.delete') . '><i class="glyphicon glyphicon-trash"></i></button>';
                    }

                    return $actions;
                })
                ->filterColumn('user_id', function ($query, $keyword) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                    });
                })
                ->filterColumn('category_id', function ($query, $keyword) {
                    $query->whereHas('category', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('type', function ($query, $keyword) {
                    $query->where('commission_type', 'like', "%{$keyword}%");
                })
                ->filterColumn('commission_value', function ($query, $keyword) {
                    $query->where('commission_value', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'sales_target', 'period', 'type'])
                ->make(true);
        }

        return view('commissionagent::sales_targets.index');
    }


    public function create()
    {
        $users = User::pluck('first_name', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();
        $targetType = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];
        return view('commissionagent::sales_targets.create', compact('users', 'categories', 'targetType'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('sales_targets.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'minimum_sales' => 'required|numeric|min:0|max:9999999999999.99',
            'maximum_sales' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
                'gte:minimum_sales',
            ],
            'commission_type' => 'required|in:fixed,percentage',
            'commission_value' => 'required|numeric|min:0|max:9999999999999.99',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Custom validation
        $existingTarget = SalesTarget::where('user_id', $request->user_id)
            ->where('category_id', $request->category_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->first();

        if ($existingTarget) {
            return redirect()->back()->withErrors(['error' => 'Sales target already exists for this user, category, and date range.']);
        }

        try {
            SalesTarget::create($request->all());
            $output = [
                'success' => true,
                'msg' => 'Sales Target created successfully.',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => 'An error occurred while creating the Sales Target.',
            ];
        }
        return redirect()->route('sales-targets.index')->with('status', $output);
    }

    public function edit(SalesTarget $salesTarget)
    {
        $users = User::pluck('first_name', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();
        $targetType = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];
        return view('commissionagent::sales_targets.edit', compact('salesTarget', 'users', 'categories', 'targetType'));
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('sales_targets.update')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'minimum_sales' => 'required|numeric|min:0|max:9999999999999.99',
            'maximum_sales' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
                'gte:minimum_sales',
            ],
            'commission_type' => 'required|in:fixed,percentage',
            'commission_value' => 'required|numeric|min:0|max:9999999999999.99',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {

            $salesTarget = SalesTarget::findOrFail($id);
            $salesTarget->update($request->all());
            $output = [
                'success' => true,
                'msg' => 'Sales Target updated successfully.',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => 'An error occurred while updating the Sales Target.',
            ];
        }
        return redirect()->route('sales-targets.index')->with('status', $output);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('sales_targets.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $salesTarget = SalesTarget::findOrFail($id);
                $salesTarget->delete();
                $output = [
                    'success' => true,
                    'msg' => 'Sales Target deleted successfully.',
                ];
            } catch (\Exception $e) {
                \Log::error('Error deleting Sales Target: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                    'sales_target_id' => $id,
                ]);
                $output = [
                    'success' => false,
                    'msg' => 'An error occurred while deleting the Sales Target.',
                ];
            }
            return response()->json($output);
        }
    }
}
