@extends('layouts.company')
@section('title', 'Show Party Collections')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-offset-2 col-md-8">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Collection Details</h3>
          <div class="page-action pull-right">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
          {{-- <strong><i class="fa fa-book margin-r-5"></i> :</strong> --}}

          <p class="text-muted">
          </p>

          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <b>Party Name</b>
              <span class="pull-right">{{ getClient($collection->client_id)['company_name']}}</span>
            </li>
            <li class="list-group-item">
              <b>Employee Name</b>
              <span class="pull-right">
                @if($collection->employee_type == "Admin")
                {{ getCompany($collection->employee_id)['company_name']}} Admin/Manager
                @elseif($collection->employee_type == "Employee")
                {{ getEmployee($collection->employee_id)['name'] }}
                @endif
              </span>
            </li>

            <li class="list-group-item">
              <b>Received Amount</b> <span class="pull-right">{{$collection->payment_received}}</span>
            </li>
            <li class="list-group-item">
              <b>Date of Payment</b><span
                class="pull-right">{{date('d M Y', strtotime($collection->payment_date))}}</span>
            </li>

            <li class="list-group-item">
              <b>Mode of Payment</b> <span class="pull-right">{{$collection->payment_method}}</span>
            </li>

            <li class="list-group-item">
              <b>Payment Notes</b> <span class="pull-right">{{$collection->payment_note}}</span>
            </li>

            <li class="list-group-item">
              <b>Expense Photo</b>
              @if(isset($collection->image_path))
              <img class="img-responsive" @if(isset($collection->image_path))
              src="{{ URL::asset('cms'.$collection->image_path) }}"
              @endif alt="Picture Displays here" style="max-height: 500px;"/>
              @else
              <span class="pull-right">N/A</span>
              @endif
            </li>
          </ul>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- ./col -->
    <!-- ./col -->
  </div>

  @endsection

  @section('scripts')
  <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

  @endsection