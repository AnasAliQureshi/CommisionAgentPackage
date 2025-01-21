@extends('layouts.app')
@section('title', __('user.add_sales_target'))
@section('content')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Add Sales Target</h1>
    </section>
    <section class="content">
        {!! Form::open([
            'url' => action([\Modules\CommissionAgent\Http\Controllers\SalesTargetController::class, 'store']),
            'method' => 'post',
            'id' => 'sales_target_add_form',
        ]) !!}
        @if ($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                @component('components.widget')
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('user', 'User' . ':*') !!}
                            {!! Form::select('user_id', $users, old('user_id'), [
                                'class' => 'form-control select2',
                                'id' => 'user_select',
                                'placeholder' => __('messages.please_select'),
                            ]) !!}
                            @if ($errors->has('user_id'))
                                <span class="text-danger">{{ $errors->first('user_id') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('category', 'Category' . ':*') !!}
                            {!! Form::select('category_id', $categories, old('category_id'), [
                                'class' => 'form-control select2',
                                'id' => 'category_select',
                                'placeholder' => __('messages.please_select'),
                            ]) !!}
                            @if ($errors->has('category_id'))
                                <span class="text-danger">{{ $errors->first('category_id') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('minimum_sales', 'Minimum Sales' . ':*') !!}
                            {!! Form::number('minimum_sales', old('minimum_sales'), [
                                'class' => 'form-control text-right',
                            ]) !!}
                            @if ($errors->has('minimum_sales'))
                                <span class="text-danger">{{ $errors->first('minimum_sales') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('maximum_sales', 'Maximum Sales' . ':*') !!}
                            {!! Form::number('maximum_sales', old('maximum_sales'), [
                                'class' => 'form-control text-right',
                            ]) !!}
                            @if ($errors->has('maximum_sales'))
                                <span class="text-danger">{{ $errors->first('maximum_sales') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('commission_type', 'Commission Type' . ':*') !!}
                            {!! Form::select('commission_type', $targetType, old('commission_type'), [
                                'class' => 'form-control select2',
                                'id' => 'commission_type_select',
                            ]) !!}
                            @if ($errors->has('commission_type'))
                                <span class="text-danger">{{ $errors->first('commission_type') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('commission_value', 'Commission Value' . ':*') !!}
                            {!! Form::number('commission_value', old('commission_value'), [
                                'class' => 'form-control text-right',
                            ]) !!}
                            @if ($errors->has('commission_value'))
                                <span class="text-danger">{{ $errors->first('commission_value') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('start_date', 'Start Date' . ':*') !!}
                            {!! Form::date('start_date', old('start_date'), [
                                'class' => 'form-control',
                            ]) !!}
                            @if ($errors->has('start_date'))
                                <span class="text-danger">{{ $errors->first('start_date') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('end_date', 'End Date' . ':*') !!}
                            {!! Form::date('end_date', old('end_date'), [
                                'class' => 'form-control',
                            ]) !!}
                            @if ($errors->has('end_date'))
                                <span class="text-danger">{{ $errors->first('end_date') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endcomponent
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">
                        @lang('messages.save')
                    </button>
                </div>
            </div>

            {!! Form::close() !!}
    </section>
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();

            // Add frontend validation
            $('form#sales_target_add_form').validate({
                rules: {
                    user_id: {
                        required: true
                    },
                    category_id: {
                        required: true
                    },
                    minimum_sales: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 9999999999999.99
                    },
                    maximum_sales: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 9999999999999.99,
                        greaterThanEqualTo: "#minimum_sales"
                    },
                    commission_type: {
                        required: true
                    },
                    commission_value: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 9999999999999.99
                    },
                    start_date: {
                        required: true,
                        date: true
                    },
                    end_date: {
                        required: true,
                        date: true,
                        greaterThan: "#start_date"
                    }
                },
                messages: {
                    user_id: {
                        required: "Please select a user."
                    },
                    category_id: {
                        required: "Please select a category."
                    },
                    minimum_sales: {
                        required: "Please enter a minimum sales value.",
                        number: "Please enter a valid number.",
                        min: "Minimum sales cannot be negative.",
                        max: "Value cannot exceed 15 digits with 2 decimal places."
                    },
                    maximum_sales: {
                        required: "Please enter a maximum sales value.",
                        number: "Please enter a valid number.",
                        min: "Maximum sales cannot be negative.",
                        max: "Value cannot exceed 15 digits with 2 decimal places.",
                        greaterThanEqualTo: "Maximum sales must be greater than or equal to minimum sales."
                    },
                    commission_type: {
                        required: "Please select a commission type."
                    },
                    commission_value: {
                        required: "Please enter a commission value.",
                        number: "Please enter a valid number.",
                        min: "Commission value cannot be negative.",
                        max: "Value cannot exceed 15 digits with 2 decimal places."
                    },
                    start_date: {
                        required: "Please select a start date.",
                        date: "Please enter a valid date."
                    },
                    end_date: {
                        required: "Please select an end date.",
                        date: "Please enter a valid date.",
                        greaterThan: "End date must be after the start date."
                    }
                },

                // This will be triggered after validation
                submitHandler: function(form) {
                    form.submit();
                },

                // This will remove error messages after the user fixes the input
                unhighlight: function(element) {
                    $(element).closest('.form-group').find('span.text-danger')
                        .remove();
                },

                // This will trigger validation on every input change, so it clears the error message when it's valid
                highlight: function(element) {
                    $(element).closest('.form-group').find('span.text-danger')
                        .remove();
                }
            });

            // Custom method for greaterThan
            $.validator.addMethod("greaterThan", function(value, element, param) {
                return this.optional(element) || new Date(value) > new Date($(param).val());
            }, "End date must be after the start date.");

            // Custom method for greaterThanEqualTo
            $.validator.addMethod("greaterThanEqualTo", function(value, element, param) {
                return this.optional(element) || parseFloat(value) >= parseFloat($(param).val());
            }, "This value must be greater than or equal to the referenced value.");

            // Manually trigger validation check for each input field when changed
            $('form#sales_target_add_form input, form#sales_target_add_form select').on('change', function() {
                $(this).valid(); // Revalidate the field on change
            });

        });
    </script>
@stop
