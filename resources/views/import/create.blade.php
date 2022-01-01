@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Import Data') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('storeImportData') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                        	@if ($errors->any())
							    <div class="alert alert-danger col-md-12">
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							@endif

                            @if(session()->has('message'))
                                <div class="alert alert-success col-md-12">
                                    {{ session()->get('message') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Excel File') }}:</label>

                            <div class="col-md-6">
                                <input type="file" name="batch" id="batch">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Import') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection