@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Report 1') }}</div>
                @php($recordsArray = $recordsObj->toArray())
                <div class="card-body">
                    <table>
                        <tr>
                            <th>Adm No</th>
                            <th>Roll No</th>
                            <th>Receipt No</th>
                            <th>Academic Year</th>
                            <th>Due Amount</th>
                            <th>Paid Amount</th>
                            <th>Concession Amount</th>
                            <th>Scholarship Amount</th>
                            <th>Reverse Concession Amount</th>
                            <th>Write Amount</th>
                            <th>Adjust Amount</th>
                            <th>Refund Amount</th>
                            <th>Fund Transfer Amount</th>
                        </tr>
                        @if (!empty($recordsArray['data']))
                            @foreach ($recordsArray['data'] as $info)
                        <tr>
                            <td>{{ $info['id'] }}</td>
                            <td>{{ $info['roll_no'] }}</td>
                            <td>{{ $info['tranid'] }}</td>
                            <td>{{ $info['acadYear'] }}</td>
                            <td>{{ $info['due_amount'] }}</td>
                            <td>{{ $info['amount'] }}</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>{{ $info['duerev'] }}</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>
                            @endforeach
                        @else
                            <tr><td colspan="13">No Data Found</td></tr>
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