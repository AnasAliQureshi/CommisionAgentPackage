@extends('layouts.app')
@section('title', 'Sales Goal Report')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Sales Goal Report
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Monitor Sales Goals
                Achievement</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('user', 'User' . ':') !!}
                            {!! Form::select('user_id', $users, null, [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'user_select',
                                'placeholder' => __('messages.all'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('report_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('report_date_range', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'id' => 'report_date_range',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="sales_goal_report_table">
                            <thead>
                                <tr>
                                    <th>Salesperson</th>
                                    <th>Target Sales</th>
                                    <th>Actual Sales</th>
                                    <th>Status</th>
                                    <th>Commission</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-right">
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td class="grand_total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#report_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#report_date_range').val(
                        start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                    );
                }
            );
            const table = $('#sales_goal_report_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('salesGoalReport') }}',
                    data: function(d) {
                        d.user_id = $('#user_select').val();
                        d.start_date = $('#report_date_range').data('daterangepicker').startDate.format(
                            'YYYY-MM-DD');
                        d.end_date = $('#report_date_range').data('daterangepicker').endDate.format(
                            'YYYY-MM-DD');
                    }
                },
                columns: [{
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'target_sales',
                        name: 'target_sales',
                    },
                    {
                        data: 'total_sales',
                        name: 'total_sales',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'additional_commission',
                        name: 'additional_commission',
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td:eq(1)', row).css('text-align', 'right');
                    $('td:eq(2)', row).css('text-align', 'right');
                    $('td:eq(4)', row).css('text-align', 'right');
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // Total over all pages
                    total = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(4).footer()).html(
                        total.toFixed(2)
                    );
                }
            });

            $('#user_select, #goal_status, #report_date_range').change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
