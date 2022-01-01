@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Report 2') }}</div>
                <div class="card-body">
                    <table>
                        <tr>
                            <th>Adm No</th>
                            <th>Roll No</th>
                            <th>Amount</th>
                            <th>Receipt No</th>
                            <th>Receipt Date</th>
                            <th>Academic Year</th>
                            <th>Fee Type</th>
                        </tr>
                        @if (!empty($records['data']))
                            @foreach ($records['data'] as $info)
                        <tr>
                            <td>{{ $info['id'] }}</td>
                            <td>{{ $info['roll_no'] }}</td>
                            <td>{{ $info['amount'] }}</td>
                            <td>{{ $info['tranid'] }}</td>
                            <td>{{ $info['tranDate'] }}</td>
                            <td>{{ $info['acadYear'] }}</td>
                            <td>{{ $info['head_name'] }}</td>
                        </tr>
                            @endforeach
                        @else
                            <tr><td colspan="7">No Data Found</td></tr>
                        @endif
                    </table>
                    <div class="row col-md-12" style="padding-top: 20px; float: right;">
                        {{ $recordsObj->render() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection