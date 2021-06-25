
@extends('layouts.company')
@section('title', 'Assign Salesman Target')
@section('stylesheets')
    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else 
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
    <style>
        .ms-options-wrap {
            min-width: 120px;
            z-index: 1;
        }

        .select2-selection__placeholder,
        .select2-selection__rendered {
            color: #000 !important;
        }

        .addCancelBtn {
            width: 45px;
        }

        .box-body .btn-success {
            background-color: #00da76 !important;
            border-color: #00da76 !important;
            color: #fff !important;
        }

        .caret {
            position: absolute;
            top: 20px;
        }

        .multiselect.dropdown-toggle.btn.btn-default .caret {
            margin-top: 0px;
        }

        .qty,
        .product_discount,
        .rate,
        .mrp,
        .amt {
            width: 80px !important;
        }

        .pdisinputaddon,
        .discount-symbol-selection,
        .unit-symbol-selection {
            padding: 0px 0px;
            */ font-size: 14px;
            font-weight: normal;
            line-height: 1;
            color: #555;
            text-align: center;
            background-color: #eee;
            border: 0px solid #ccc;
            border-radius: 0px;
        }

        .mrp_addon {
            padding: 0px 0px;
            font-size: 14px;
            font-weight: normal;
            line-height: 1;
            color: #555;
            text-align: center;
            background-color: #eeeeee !important;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .amt {
            padding: 0px;
        }

        .contentFit {
            width: fit-content;
        }

        #loaderDiv img {
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 99;
        }

        .loaderDiv {
            position: absolute;
            z-index: 1;
            left: 30%;
        }

        .loaderOpacityControl {
            opacity: 0.4;
        }

        .contDisp {
            display: -webkit-inline-box;
        }

        .add-on-height {
            height: 40px !important;
        }

    </style>
@endsection

@section('content')
    <section class="content">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Assign Salesman Target</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                {!! Form::open(array('url' => url(domain_route("company.admin.salesmantarget.setconfirm")),
                'method' => 'post', 'id'=>'orderForm')) !!}
                <div class="row" style="margin-top:10px;">
                  <div class="col-md-offset-2 col-md-2">
                    <h5><b>Select SalesMan</b></h5>
                  </div>
                  <div class="col-md-4">
                    {!! Form::select('salesmnaname[0][]',$data['allsalesman'], null, ['class' => 'form-control multClass','id'=>'unit0','data-id'=>'0','required','multiple']) !!}
                  </div>
                </div>
                <div class="row" style="margin-top:10px;margin-bottom:35px;">
                  <div class="col-md-offset-2 col-md-2">
                    <h5><b>Select Target</b></h5>
                  </div>
                  <div class="col-md-4">
                    {!! Form::select('salesmantarget[0][]',$data['alltargets'], null, ['class' => 'form-control targets','id'=>'unit0','data-id'=>'0','required']) !!}
                  </div>
                </div>
                <div class="row" style="margin-bottom:25px;">
                  <div class="col-md-offset-5 col-md-2">
                    {!! Form::submit('Confirm and Assign Target', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry']) !!}
                  </div>
                </div>
                
                {!! Form::close() !!}

            </div>
        </div>

    </section>

@endsection

@section('scripts')

    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
    <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
      $(function () {

        $('.targets').select2();


        $('.multClass').multiselect({
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          enableFullValueFiltering: false,
          enableClickableOptGroups: false,
          includeSelectAllOption: true,
          enableCollapsibleOptGroups : true,
          selectAllNumber: false,
        });


        

      });
    </script>

@endsection
