<?php

namespace Modules\CommissionAgent\Http\Controllers;

use App\Category;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CommissionAgent\Entities\SalesTarget;
use Yajra\DataTables\DataTables;

class CommissionController extends Controller
{
    public function viewCommissions(Request $request)
    {
        if (!auth()->user()->can('sales_targets.view_commissions')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $salesQuery = DB::table('transaction_sell_lines')
                ->join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('users', 'transactions.created_by', '=', 'users.id')
                ->select(
                    'categories.name as category_name',
                    'users.id as user_id',
                    'users.first_name',
                    'products.category_id',
                    'transactions.created_by',
                    DB::raw('ROUND(SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_before_discount), 2) as total_sales')
                )
                ->groupBy('products.category_id', 'transactions.created_by');

            // Apply filters
            if ($request->user_id) {
                $salesQuery->where('transactions.created_by', $request->user_id);
            }
            if ($request->product_category) {
                $salesQuery->where('products.category_id', $request->product_category);
            }
            if ($request->start_date && $request->end_date) {
                $salesQuery->whereBetween('transactions.transaction_date', [$request->start_date, $request->end_date]);
            }

            return DataTables::of($salesQuery)
                ->addColumn('total_commission', function ($data) use ($request) {
                    $commissionRule = SalesTarget::where('user_id', $data->user_id)
                        ->where('category_id', $data->category_id)
                        ->when($request->commission_type, function ($query) use ($request) {
                            return $query->where('commission_type', $request->commission_type);
                        })
                        ->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date)
                        ->first();

                    $commission = 0;
                    if ($commissionRule) {
                        if ($commissionRule->commission_type == 'percentage') {
                            $commission = ($data->total_sales * $commissionRule->commission_value) / 100;
                        } elseif ($commissionRule->commission_type == 'fixed') {
                            $commission = $commissionRule->commission_value;
                        }
                    }

                    return number_format($commission, 2);
                })
                ->addColumn('commission_type', function ($data) use ($request) {
                    $commissionRule = SalesTarget::where('user_id', $data->user_id)
                        ->where('category_id', $data->category_id)
                        ->when($request->commission_type, function ($query) use ($request) {
                            return $query->where('commission_type', $request->commission_type);
                        })
                        ->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date)
                        ->first();

                    return $commissionRule ? ucfirst($commissionRule->commission_type) : '--';
                })
                ->with([
                    'total_sales' => function () use ($salesQuery) {
                        return $salesQuery->sum(DB::raw('transaction_sell_lines.quantity * transaction_sell_lines.unit_price_before_discount'));
                    },
                    'total_commission' => function () use ($salesQuery, $request) {
                        $totalCommission = 0;

                        foreach ($salesQuery->get() as $data) {
                            $commissionRule = SalesTarget::where('user_id', $data->user_id)
                                ->where('category_id', $data->category_id)
                                ->when($request->commission_type, function ($query) use ($request) {
                                    return $query->where('commission_type', $request->commission_type);
                                })
                                ->where('start_date', '<=', $request->end_date)
                                ->where('end_date', '>=', $request->start_date)
                                ->first();

                            if ($commissionRule) {
                                if ($commissionRule->commission_type == 'percentage') {
                                    $totalCommission += ($data->total_sales * $commissionRule->commission_value) / 100;
                                } elseif ($commissionRule->commission_type == 'fixed') {
                                    $totalCommission += $commissionRule->commission_value;
                                }
                            }
                        }

                        return number_format($totalCommission, 2);
                    }
                ])
                ->make(true);
        }

        $users = User::pluck('first_name', 'id')->toArray();
        $categories = Category::pluck('name', 'id')->toArray();
        $targetType = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];

        return view('commissionagent::commissions.index', compact('users', 'categories', 'targetType'));
    }

    public function salesGoalReport(Request $request)
    {
        if (!auth()->user()->can('sales_targets.sales_goal_report')) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->ajax()) {
            $salesQuery = DB::table('transaction_sell_lines')
                ->join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('users', 'transactions.created_by', '=', 'users.id')
                ->select(
                    'users.id as user_id',
                    'users.first_name',
                    DB::raw('ROUND(SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_before_discount), 2) as total_sales')
                )
                ->groupBy('users.id');

            // Apply filters
            if ($request->user_id) {
                $salesQuery->where('transactions.created_by', $request->user_id);
            }
            if ($request->start_date && $request->end_date) {
                $salesQuery->whereBetween('transactions.transaction_date', [$request->start_date, $request->end_date]);
            }

            $salesData = DataTables::of($salesQuery)
                ->addColumn('status', function ($data) use ($request) {
                    $salesTarget = SalesTarget::where('user_id', $data->user_id)
                        ->where(function ($query) use ($request) {
                            $query->where('start_date', '<=', $request->end_date)
                                ->where('end_date', '>=', $request->start_date);
                        })
                        ->first();

                    if (!$salesTarget) return 'No Goal Set';
                    if ($data->total_sales < $salesTarget->minimum_sales) {
                        return 'Not Achieved';
                    }
                    return 'Achieved';
                })
                ->addColumn('additional_commission', function ($data) use ($request) {
                    $salesTarget = SalesTarget::where('user_id', $data->user_id)
                        ->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date)
                        ->first();

                    if (!$salesTarget) return '0.00';

                    $commission = 0;
                    if ($data->total_sales >= $salesTarget->minimum_sales) {
                        if ($salesTarget->commission_type == 'percentage') {
                            $commission = ($data->total_sales * $salesTarget->commission_value) / 100;
                        } elseif ($salesTarget->commission_type == 'fixed') {
                            $commission = $salesTarget->commission_value;
                        }
                    }

                    return number_format($commission, 2);
                })->editColumn('total_sales', function ($data) {
                    return number_format($data->total_sales, 2);
                })
                ->addColumn(
                    'target_sales',
                    function () use ($request) {
                        $targetSales = SalesTarget::where('start_date', '<=', $request->end_date)
                            ->where('end_date', '>=', $request->start_date)
                            ->max('minimum_sales');
                        return number_format($targetSales, 2);
                    }
                )->make(true);

            return $salesData;
        }

        $users = User::pluck('first_name', 'id')->toArray();
        return view('commissionagent::commissions.sales_goal_report', compact('users'));
    }
}
