@extends('layouts.app')
@section('title', 'Sales Targets')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Sales Targets
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Manage Sales Targets</small>
        </h1>
        <!-- <ol class="breadcrumb">
                                                                                                                                                                                                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                                                                                                                                                                                                        <li class="active">Here</li>
                                                                                                                                                                                                    </ol> -->
    </section>
    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'All Your Sales Targets'])
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full"
                        href="{{ action([\Modules\CommissionAgent\Http\Controllers\SalesTargetController::class, 'create']) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="data_table">
                    <thead>
                        <tr>
                            <th>User @show_tooltip('Commission Agent')</th>
                            <th>Category @show_tooltip('Product Category')</th>
                            <th>Period @show_tooltip('Duration for Sales Target')</th>
                            <th>Sales Target @show_tooltip('Minimum To Maximum Range of Sales Targets')</th>
                            <th>Type @show_tooltip('Commission Type')</th>
                            <th>Commission Amt. @show_tooltip('Commission Amount')</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade user_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        //Roles table
        $(document).ready(function() {
            var dataTable = $('#data_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                scrollX: false,
                ajax: '/sales-targets',
                columnDefs: [{
                    "targets": [7],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: "user_id"
                    },
                    {
                        data: "category_id"
                    },
                    {
                        data: "period"
                    },
                    {
                        data: "sales_target"
                    },
                    {
                        data: "type"
                    },
                    {
                        data: "commission_value"
                    },

                    {
                        data: "created_at"
                    },
                    {
                        data: "action"
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(3).addClass('text-right');
                    $('td', row).eq(4).addClass('text-center');
                    $('td', row).eq(5).addClass('text-right');
                }
            });

            $(document).on('click', 'button.delete_sales_target_button', function() {
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_user,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    dataTable.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
