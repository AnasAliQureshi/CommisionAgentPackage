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
                    'active' => request()->is('sales-targets*'),
                    'permission' => 'view_sales_targets'
                ]);
                $sub->url(action('Modules\CommissionAgent\Http\Controllers\CommissionController@viewCommissions'), "Commissions", [
                    'active' => request()->is('view-commissions*'),
                    'permission' => 'view_commissions'
                ]);
                $sub->url(action('Modules\CommissionAgent\Http\Controllers\CommissionController@salesGoalReport'), "Sales Goal Report", [
                    'active' => request()->is('view-sales-goal-report*'),
                    'permission' => 'sales_goal_report'
                ]);
            }, ['icon' => ' <svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"></path>
            <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path>
            <path d="M12 6v10"></path>
          </svg>']);
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