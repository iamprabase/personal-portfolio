@extends('layouts.company')

@section('title', 'Cheque Details')

@section('stylesheets')

  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
  <style type="text/css">
    img{
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }    

    .modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1500; /* Sit on top */
      padding-top: 100px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;  
      -webkit-animation-name: zoom;
      -webkit-animation-duration: 0.6s;
      animation-name: zoom;
      animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
      from {-webkit-transform:scale(0)} 
      to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
      from {transform:scale(0)} 
      to {transform:scale(1)}
    }

    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }

    .close:hover,
    .close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }
    }
 

  </style>

@endsection



@section('content')

  <section class="content">

    <div class="row">

      <div class="col-md-12">

        <div class="box box-default">

          <div class="box-header with-border">

            <h3 class="box-title">Cheque Details</h3>

            <div class="page-action pull-right">

              <a href="{{ domain_route('company.admin.cheque.index') }}" class="btn btn-default btn-sm"> <i

                    class="fa fa-arrow-left"></i> Back</a>

            </div>

          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <div class="table-responsive">

              <table class="table table-bordered table-striped">

                <colgroup>

                  <col class="col-xs-2">

                  <col class="col-xs-7">

                </colgroup>

                <tbody>

                  <tr>

                  <th scope="row"> Party Name</th>

                  <td>{{ getClient($cheque->client_id)['company_name']}}</td>

                </tr>

                <tr>

                  <th scope="row"> Bank Name</th>

                  <td>{{ $cheque->bank->name }}</td>

                </tr>

                <tr>

                  <th scope="row"> Employee Name</th>

                  <td>

                    @if($cheque->employee_type == "Admin")

                      {{ Auth::user()->name.' (Admin)' }}

                    @elseif($cheque->employee_type == "Employee")

                      {{ getEmployee($cheque->employee_id)['name'] }}

                    @endif

                  </td>

                </tr>

                <tr>

                  <th scope="row"> Cheque Number</th>

                  <td>{{ $cheque->cheque_no }}</td>

                </tr>

                <tr>

                  <th scope="row"> Cheque Date</th>

                  <td>{{ getDeltaDate($cheque->cheque_date) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Receive Date</th>

                  <td>{{ getDeltaDate($cheque->payment_date) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Amount</th>

                  <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$cheque->payment_received,2)}}</td>

                </tr>

                <tr>

                  <th scope="row"> Cheque Status</th>

                  <td><span class="">

                   <?php $current_date = Carbon\Carbon::now()->format('Y-m-d'); ?>
                      @if($cheque->payment_status == 'Pending')
                        @if($cheque->cheque_date < $current_date)
                          <span class="label label-primary">Overdue</span>
                        @else
                          <span class="label label-warning">Pending</span>
                        @endif
                      @elseif($cheque->payment_status == 'Deposited')
                        <span class="label label-default">{{ $cheque->payment_status}}</span>

                      @elseif($cheque->payment_status == 'Cleared')
                        <span class="label label-success">{{ $cheque->payment_status}}</span>
                      @elseif($cheque->payment_status == 'Bounced')
                        <span class="label label-danger">{{ $cheque->payment_status}}</span>
                      @else
                        <span class="label label-danger">N/A</span>
                      @endif
                    </span>
                  </td>

                </tr>
                
                @if(isset($cheque->payment_status_note))
                <tr>

                  <th scope="row">Notes</th>

                  <td>{{ $cheque->payment_status_note }}</td>

                </tr>
                @endif

                @if(isset($cheque->image_path))
                <tr>
                  <th scope="row"> Picture</th>
                  <td>
                    @foreach($images as $image)
                      @if(isset($image->image_path))
                      <div class="col-sm-4">
                        <img class="img-responsive display-imglists"
                             @if(isset($image->image_path)) src="{{ URL::asset('cms'.$image->image_path) }}"
                             @endif alt="Picture Displays here" style="max-height: 400px;height: 222px;"/>
                      </div>
                      @else
                        <span class="pull-right">N/A</span>
                      @endif
                  @endforeach
                  </td>
                </tr>
                @endif
                </tbody>

              </table>

            </div>

          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

      <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
      </div>

      <!-- ./col -->

      <!-- ./col -->

    </div>



    @endsection



    @section('scripts')

      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

      <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

      <script>

          $(function () {

              // $("#task").DataTable();



              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)

                  var mid = button.data('mid')

                  var modal = $(this)

                  modal.find('.modal-body #m_id').val(mid);

              });



          });

      </script>
      <script>
      // Get the modal
      var modal = document.getElementById("myModal");
      var modalImg = document.getElementById("img01");

      $('.display-imglists').on('click',function(){
        modal.style.display = "block";
        modalImg.src = this.src;
        // captionText.innerHTML = this.alt;
      });

      $('.close').on('click',function(){
        modal.style.display = "none";
      });
      </script>



@endsection