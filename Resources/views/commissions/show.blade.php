@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Commission Details</h1>
        <table class="table table-bordered mt-3">
            <tr>
                <th>User</th>
                <td>{{ $commission->user->first_name }} {{ $commission->user->last_name }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $commission->category->name }}</td>
            </tr>
            <tr>
                <th>Sales Amount</th>
                <td>{{ $commission->sales_amount }}</td>
            </tr>
            <tr>
                <th>Commission Type</th>
                <td>{{ $commission->commission_type }}</td>
            </tr>
            <tr>
                <th>Commission Amount</th>
                <td>{{ $commission->commission_amount }}</td>
            </tr>
            <tr>
                <th>Transaction Date</th>
                <td>{{ $commission->transaction_date }}</td>
            </tr>
        </table>
        <a href="{{ route('commissions.index') }}" class="btn btn-primary">Back to List</a>
    </div>
@endsection
