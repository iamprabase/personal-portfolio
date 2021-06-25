@extends('layouts.company')

@section('title', 'Show Leave')

@section('stylesheets')

  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
  <style type="text/css">
   .edit, .delete{
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
              <h3 class="box-title">Leave Details</h3>
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
               <th scope="row"> Leave Type</th>

                  <td>{{ getLeaveType($leave->leavetype)['name']}}</td>

                </tr>

                <tr>

                  <th scope="row"> Date (From - To)</th>

                  <td>{{ getDeltaDate(date('Y-m-d', strtotime($leave->start_date))) }}

                    - {{ getDeltaDate(date('Y-m-d', strtotime($leave->end_date))) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Number of Days</th>

                  <td>{{ getDays($leave->start_date,$leave->end_date) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Status</th>

                  <td><span class="">

                    @if($leave->status =='Approved')

                      <span class="label label-success">{{ $leave->status}}</span>
                      
                    @elseif($leave->status =='Pending')

                      <span class="label label-warning">{{ $leave->status}}</span>

                    @else

                      <span class="label label-danger">{{ $leave->status}}</span>

                    @endif

                  </span></td>

                </tr>

                <tr>

                  <th scope="row"> Remarks:</th>

                  <td><span class="">

                    @if($leave->status_reason)

                        <span>{{ $leave->status_reason}}</span>

                      @else

                        <span>Not Yet Available</span>

                      @endif

                  </span></td>

                </tr>

                 <tr>

                  <th scope="row"> Reason:</th>

                  <td><span class="">


                        <span>{!!$leave->leave_desc!!}</span>

                  </span></td>

                </tr>

                </tbody>

              </table>

            </div>

          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

    </div>
    <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
      data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
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
              <input type="hidden" name="id" id="m_id" value="">
              <input type="hidden" name="previous_url" value="{{URL::previous()}}"">
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endsection

    @section('scripts')

      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

      <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

      <script>

          $(function () {

              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)

                  var mid = button.data('mid')

                  var modal = $(this)

                  modal.find('.modal-body #m_id').val(mid);

              })

          });

      </script>

@endsection