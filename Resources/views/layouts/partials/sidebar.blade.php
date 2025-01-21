<li class="bg-green treeview {{ in_array($request->segment(1), ['commission-agent']) ? 'active active-sub' : '' }}">
    <a href="#">
        <i class="fa fa-dollar-sign"></i>
        <span class="title">@lang('commissionagent::lang.commissions')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        <li class="{{ $request->segment(2) == 'sales-targets' ? 'active active-sub' : '' }}">
            <a href="{{ route('sales-targets.index') }}">
                <i class="fa fa-cogs"></i>
                <span class="title">@lang('commissionagent::lang.sales_targets')</span>
            </a>
        </li>
        <li class="{{ $request->segment(2) == 'commissions' ? 'active active-sub' : '' }}">
            <a href="{{ route('commissions.index') }}">
                <i class="fa fa-money"></i>
                <span class="title">@lang('commissionagent::lang.view_commissions')</span>
            </a>
        </li>
    </ul>
</li>
