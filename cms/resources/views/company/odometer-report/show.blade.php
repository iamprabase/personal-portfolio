@extends('layouts.company')
@section('title', 'Odometer Report')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/zoomImage/zoomer.css')}}">

    <style>

        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #fff;
            opacity: 1;
        }

        #img-upload {
            width: 80%;
            height: 80%;
        }

        .panel-heading {
            color: #fff !important;
            background-color: #0b7676 !important;
        }

        .del-img {
            position: absolute;
            right: 32px;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }


        .box-body .btn-primary {
            background-color: #079292 !important;
            border-color: #079292 !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:active, .btn-primary.hover {
            background-color: #0b7676 !important;
            border-color: #0b7676 !important;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ccc;
        }


        input[type="checkbox"] {
            vertical-align: middle;
        }

        .order-dtl-bg {
            min-height: 50px !important;
        }

        @media print {
            .noPrint {
                display: none;
            }
        }

    </style>
@endsection

@section('content')
    <section class="content">
        @if (\Session()->has('success'))
            <div class="alert alert-success">
                <p>{{ \Session::get('success') }}</p>
            </div><br/>
        @endif
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border no-print">
                    <a href="{{ domain_route('company.admin.odometer.report.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i>
                        Back</a>
                    <div class="page-action pull-right">
                        {!!$action!!}
                    </div>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Odometer Report Detail</h3>
                </div>
                <div class="box-body">
                    <div id="detail_div">
                        <div class="row">
                            <div class="col-xs-5">
                                <div class="order-dtl-bg">
                                    <strong>Name</strong> : {{$reports->first()->employees->name}} <br>
                                    <strong>Date</strong> : {{ $start_date .' - '. $end_date}} <br>
                                    <strong>Total Distance Traveled</strong> : {{$distance_in_km}} KM
                                    ({{$distance_in_mile}} Mile) <br>
                                    <strong>Reimbursement Amount</strong>
                                    : {{config('settings.currency_symbol')}}  {{round($reports->sum('amount'),2)}}
                                </div>
                            </div>
                        </div>
                        <br/>
                        <table border="1px" id="odometerreport" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Start Meter</th>
                                <th>End Meter</th>
                                <th>Start Location</th>
                                <th>End Location</th>
                                <th>Distance</th>
                                <th>Reimbursement</th>
                                <th>Notes</th>
                                @can('odometer-report-update')
                                    <th class="no-print">Edit</th>
                                @endcan

                            </tr>
                            </thead>

                            <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{$report->start_time}}</td>
                                    <td>{{$report->end_time}}</td>
                                    <td>{{round($report->start_reading,2)}} @if($report->distance_unit == 1) Km @else
                                            Mile @endif</td>
                                    <td>{{round($report->end_reading,2)}} @if($report->distance_unit == 1) Km @else
                                            Mile @endif</td>
                                    <td>{{$report->start_location}}</td>
                                    <td>{{$report->end_location}}</td>
                                    <td>{{round($report->distance,2)}} @if($report->distance_unit == 1) Km @else
                                            Mile @endif</td>
                                    <td>{{config('settings.currency_symbol')}} {{round($report->amount,2)}}</td>
                                    <td>{{$report->notes}}</td>
                                    @can('odometer-report-update')
                                        <td class="no-print"><a href='#' class='btn btn-warning btn-sm edit-name-modal '
                                                                data-id='{{$report->id}}'
                                                                data-start='{{round($report->start_reading,2)}}'
                                                                data-end="{{round($report->end_reading,2)}}"
                                                                data-notes="{{$report->notes}}"
                                                                data-distance-unit="{{$report->distance_unit}}"><i
                                                        class='fa fa-edit'></i></a></td>
                                    @endcan
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
                </div>
                <div class="modal-body">
                    <p class="text-center">
                        Sorry! You are not authorized to view this user info.
                    </p>
                    <input type="hidden" name="expense_id" id="c_id" value="">
                    <input type="text" id="accountType" name="account_type" hidden/>
                </div>
                <div class="modal-footer">
                    {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div id="changeNameModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Odometer Report</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="edit" method="POST"
                          action="{{URL::to('admin/odometer-report/update')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id" value="">
                        <p id="message" style="color: red"></p>

                        <div class="form-group">
                            <label for="odometer_unit">Odometer Unit</label>
                            <span class="checkbox" style="margin-top: 2px;">
                          <label style="padding: 0px;">
                              <input type="radio" name="distance_unit"  class="minimal" value="1"> KM
                          </label>
                              <label>
                               <input type="radio" name="distance_unit"  class="minimal" value="0"> Mile
                              </label>
                            </span>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="start_reading">Start Reading</label>
                                <input type="number" name="start_reading" id="start_reading"
                                       placeholder="Meter Initial Reading"
                                       class="form-control two-digits" required min="1" step="0.01">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_reading">End Reading</label>
                                <input type="number" name="end_reading" id="end_reading" placeholder="Meter End Reading"
                                       class="form-control two-digits" required min="1" step="0.01">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea type="number" name="notes" id="notes"
                                      class="form-control" placeholder="notes"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn actionBtn" id="submit">
                                <span id="footer_action_button" class='glyphicon'> </span> Change
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{asset('assets/plugins/zoomImage/zoomer.js')}}"></script>


    <script>
        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });

        var delayTimer;

        function input(ele) {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function () {
                ele.value = parseFloat(ele.value).toFixed(2).toString();
            }, 800);
        }

        $(function () {
            $('.two-digits').keyup(function () {
                if ($(this).val().indexOf('.') !== -1) {
                    if ($(this).val().split(".")[1].length > 2) {
                        if (isNaN(parseFloat(this.value))) return;
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                }
                return this; //for chaining
            });
        });
        $('.two-digits').on('change', function () {
            $(this).val(parseFloat($(this).val()).toString());
        });


        $(document).on('click', '.edit-name-modal', function () {
            $('#footer_action_button').addClass('glyphicon-check');
            $('#footer_action_button').removeClass('glyphicon-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');
            $('.deleteContent').hide();
            $('#id').val($(this).data('id'));
            $('#start_reading').val($(this).data('start'));
            $('#end_reading').val($(this).data('end'));
            $('#notes').val($(this).data('notes'));
            $("input[name='distance_unit']").val([$(this).data('distance-unit')])
            console.log($(this).data('distance-unit'))
            $('#changeNameModal').modal('show');
            $('#message').text('');
        });

        $(document).on('click', '.alert-modal', function () {
            $('#alertModal').modal('show');
        });

        $('#edit').submit(function (e) {
            var start = $('#start_reading').val();
            var end = $('#end_reading').val();
            if (parseFloat(start) > parseFloat(end)) {
                e.preventDefault();
                $('#message').text('Start Reading cannot be greater than End Reading')
            }
        })

    </script>

@endsection

