@extends('layouts.company')
@section('title', 'Show Collection')
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
      /* background-color: rgb(0,0,0); /* Fallback color */
      /* background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;  
      /* -webkit-animation-name: zoom;
       -webkit-animation-duration: 0.6s;
      animation-name: zoom;
      animation-duration: 0.6s;*/
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
    
    .delete, .edit{
      font-size: 15px !important;
    }
    .fa-edit, .fa-trash-o{
      padding-left: 5px;
    }

    .btn-warning{
      margin-right: 2px !important;
      color: #fff!important;
      background-color: #ec971f!important;
      border-color: #d58512!important;
    }

  </style>
  
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
             <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
                    class="fa fa-arrow-left"></i> Back</a>
            <div class="page-action pull-right">
              {!!$action!!}
            </div>
          </div>
          <div class="box-header with-border">
            <h3 class="box-title">Collection Details</h3>
            {{-- <div class="page-action pull-right">
              {!!$action!!}
              <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
                    class="fa fa-arrow-left"></i> Back</a>
            </div> --}}
          </div>
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
                  <td>{{ getClient($collection->client_id)['company_name']}}</td>
                </tr>
                <tr>
                  <th scope="row"> Employee Name</th>
                  <td>
                    @if($collection->employee_type == "Admin")
                      {{ Auth::user()->managerName($collection->company_id)->name.' (Admin)' }}
                    @elseif($collection->employee_type == "Employee")
                      {{ getEmployee($collection->employee_id)['name'] }}
                    @endif</td>
                </tr>
                <tr>
                  <th scope="row"> Amount</th>
                  <td>{{ config('settings.currency_symbol')}} {{number_format((float)$collection->payment_received,2)}}</td>
                </tr>
                <tr>
                  <th scope="row"> Receive Date</th>
                  <td>{{getDeltaDate(date('Y-m-d', strtotime($collection->payment_date)))}}</td>
                </tr>
                <tr>
                  <th scope="row"> Mode of Payment</th>
                  <td>{{$collection->payment_method}}</td>
                </tr>
                @if(($collection->payment_method=="Cheque" || $collection->payment_method=="Bank Transfer" ) && (isset($collection->bank_id)) )
                  <tr>
                    <th scope="row"> Bank Name</th>
                    <td>{{ $collection->bank()->withTrashed()->first()->name }}</td>
                  </tr>
                @endif
                @if($collection->payment_method=="Cheque" && $collection->cheque_date)
                  <tr>
                    <th scope="row"> Cheque Date</th>
                    <td>{{ getDeltaDate($collection->cheque_date) }}</td>
                  </tr>
                @endif
                @if($collection->payment_status_note)
                  <tr>
                    <th scope="row">Notes</th>
                    <td>{{ $collection->payment_status_note }}</td>
                  </tr>
                @endif
                @if($collection->payment_method=="Cheque" && $collection->payment_status)
                  <tr>

                  <th scope="row"> Cheque Status</th>

                  <td><span class="">

                   <?php $current_date = Carbon\Carbon::now()->format('Y-m-d'); ?>
                      @if($collection->payment_status == 'Pending')
                        @if($collection->cheque_date < $current_date)
                          <span class="label label-primary">Overdue</span>
                        @else
                          <span class="label label-warning">Pending</span>
                        @endif
                      @elseif($collection->payment_status == 'Deposited')
                        <span class="label label-default">{{ $collection->payment_status}}</span>

                      @elseif($collection->payment_status == 'Cleared')
                        <span class="label label-success">{{ $collection->payment_status}}</span>
                      @elseif($collection->payment_status == 'Bounced')
                        <span class="label label-danger">{{ $collection->payment_status}}</span>
                      @else
                        <span class="label label-danger">N/A</span>
                      @endif
                    </span>
                  </td>

                </tr>
                @endif
                @if(count($images)>0)
                <tr>
                  <th scope="row"> Picture</th>
                  <td>
                  @foreach($images as $image)
                      {{-- @if(isset($image->image_path))
                      <div class="col-sm-4">
                        <img class="img-responsive display-imglists"
                             @if(isset($image->image_path)) src="{{ URL::asset('cms'.$image->image_path) }}"
                             @endif alt="Picture Displays here" style="max-height: 400px;height: 222px;"/>
                      </div>
                      @else
                        <span class="pull-right">N/A</span>
                      @endif --}}
                      <div class="col-xs-4">
                        <div class="imagePreview imageExistsPreview"
                          style="background-color: #fff;background-position: center center;background-size: contain;background-repeat: no-repeat;">
                          @if(isset($image->image_path))
                          <img class="img-responsive display-imglists" @if(isset($image->image_path))
                          src="{{ URL::asset('cms'.$image->image_path) }}"
                          @endif alt="Picture Displays here" style="max-height: 500px;"/>
                          @else
                          <span class="pull-right">N/A</span>
                          @endif
                        </div>
                      </div>
                  @endforeach
                </td>
                </tr>
                @endif
                </tbody>
              </table>
            </div>
            <!-- new table end -->
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- ./col -->
      <!-- ./col -->
    </div>

    <div class="modal modal-default fade" id="delete" tabindex="-1" plan="dialog" aria-labelledby="myModalLabel"
      data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" plan="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form method="post" class="remove-record-model">
            {{method_field('delete')}}
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
              <input type="hidden" name="plan_id" id="m_id" value="">
              <input type="hidden" name="prev_url" value="{{URL::previous()}}">
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div id="myModal" class="modal">
      <span class="close">&times;</span>
      <img class="modal-content" id="img01">
      <div id="caption"></div>
    </div>

    @endsection

    @section('scripts')
      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
      <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
      <script>
      // Get the modal
      var modal = document.getElementById("myModal");
      var modalImg = document.getElementById("img01");

      $('.display-imglists').on('click',function(){
        modal.style.display = "block";
        modalImg.src = this.src;
      });

      $('.close').on('click',function(){
        modal.style.display = "none";
      });

      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
      });

      </script>

@endsection