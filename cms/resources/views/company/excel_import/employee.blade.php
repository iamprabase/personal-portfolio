@extends('layouts.company')
@section('title', 'Import-Employee')

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @include('company.excel_import.partial._alert_message')
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Import Employees</h3>
          </div>
          <div class="box-body" style="padding: 20px !important;">
            @include('company.excel_import.partial._documentation')
            <div class="row" style="margin-top: 20px;">
              <div class="col-xs-3"></div>
              <div class="col-xs-6 text-center">
                @include('company.excel_import.partial._employee_modal')
              </div>
              <div class="col-xs-3"></div>
            </div>
            <hr>
            <div class="row" style="margin-top: 20px;">
              <div class="col-xs-12">
                @include('company.excel_import.partial._form', ['route' => domain_route('company.admin.import.employee.excel')])
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
