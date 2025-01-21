<?php

namespace Modules\CommissionAgent\Http\Controllers;

use Illuminate\Routing\Controller;
use Nwidart\Menus\Facades\Menu;

class DataController extends Controller
{
    public function modifyAdminMenu()
    {
        Menu::modify('admin-sidebar-menu', function ($menu) {
            $menu->dropdown("Commission Agent", function ($sub) {
                $sub->url(action('Modules\CommissionAgent\Http\Controllers\SalesTargetController@index'), "Sales Targets", [
                    'icon' => 'fa fa-cogs',
                    'active' => request()->is('commission-agent/sales-targets*'),
                    'permission' => 'view_sales_targets'
                ]);
                $sub->url(action('Modules\CommissionAgent\Http\Controllers\CommissionController@viewCommissions'), "Commissions", [
                    'icon' => 'fa fa-dollar-sign',
                    'active' => request()->is('commission-agent/commissions*'),
                    'permission' => 'view_commissions'
                ]);
                $sub->url(action('Modules\CommissionAgent\Http\Controllers\CommissionController@salesGoalReport'), "Sales Goal Report", [
                    'icon' => 'fa fa-chart-line',
                    'active' => request()->is('commission-agent/sales-goal-report*'),
                    'permission' => 'sales_goal_report'
                ]);
            });
        });
    }

    public function user_permissions()
    {
        return [
            [
                'value' => 'sales_targets.index',
                'label' => __('View Sales Targets'),
                'default' => true
            ],
            [
                'value' => 'sales_targets.create',
                'label' => __('Create Sales Targets'),
                'default' => false
            ],
            [
                'value' => 'sales_targets.update',
                'label' => __('Update Sales Targets'),
                'default' => false
            ],
            [
                'value' => 'sales_targets.destroy',
                'label' => __('Delete Sales Targets'),
                'default' => false
            ],
            [
                'value' => 'view_commissions',
                'label' => __('View Commissions'),
                'default' => true
            ],
            [
                'value' => 'sales_goal_report',
                'label' => __('Sales Goal Report'),
                'default' => true
            ]
        ];
    }
}
