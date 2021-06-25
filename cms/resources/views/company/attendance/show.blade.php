@extends('layouts.company')



@section('stylesheets')

  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">

@endsection



@section('content')

  <section class="content">

    <div class="row">

      <div class="col-md-offset-2 col-md-8">

        <div class="box box-default">

          <div class="box-header with-border">

            <h3 class="box-title">Attendance of Employee: {{getEmployee($attendances[0]['employee_id'])['name']}} &emsp;for
              the date: {{date("Y-m-d", strtotime($attendances[0]['check_datetime']))}}</h3>


          </div>

          <!-- /.box-header -->
          @if($attendances)
                <?php $allrec = count($attendances); ?>

            <div class='box-body'>
              <div class='table-responsive'>
                <table class='table no-margin'>
                  <thead>
                  <tr>
                    <th>CheckIn Time</th>
                    <th>CheckOut Time</th>
                  </tr>
                  </thead>
                  <tbody>
                  @if($attendances[0]['check_type'] == 1)
                    <tr>
                      <td>
                        {{ date("H:i:s", strtotime($attendances[0]['check_datetime'])) }}
                      </td>
                      <td>
                        <span class='label label-danger'> N/A </span>
                      </td>
                    </tr>
                    <?php array_shift($attendances) ?>
                    <?php $allrec--; ?>
                  @endif

                  @if($attendances[$allrec-1]['check_type'] == 2)
                      <?php $for_count = $allrec - 1; ?>
                  @elseif($attendances[$allrec-1]['check_type'] == 1)
                      <?php $for_count = $allrec; ?>
                  @endif

                  @if($for_count)
                    @for($i = 0; $i < $for_count; $i++)
                        <?php
                        $checkout = $checkin = '';
                        $checkout = date('H:i:s', strtotime($attendances[$i]['check_datetime']));
                        $i++;
                        $checkin = date('H:i:s', strtotime($attendances[$i]['check_datetime']));
                        ?>
                        <tr>
                          <td>{{$checkin}}</td>
                          <td>{{$checkout}}</td>
                        </tr>
                    @endfor
                  @endif
                  @if($attendances[$allrec-1]['check_type'] == 2)
                    <tr>
                      <td>
                        <span class='label label-danger'> N/A </span>
                      </td>
                      <td>
                        {{ date('H:i:s',strtotime($attendances[$allrec-1]['check_datetime'])) }}
                      </td>
                    </tr>
                    <?php array_pop($attendances); ?>
                    <?php $allrec--; ?>
                    @endif

                    </tr>
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
        @endif

        {{-- <div class="box-body">

          <strong><i class="fa fa-book margin-r-5"></i> Name:</strong>



          <p class="text-muted">

            {{ getEmployee($attendances->employee_id)->name }}

          </p>



          <ul class="list-group list-group-unbordered">



            <li class="list-group-item">

              <b>Date</b> <span class="pull-right">{{ ($attendance->adate)?date('Y-m-d',strtotime($attendance->adate)):'NA' }}</span>

            </li>

            <li class="list-group-item">

              <b>Check In</b> <span class="pull-right">{{ ($attendance->check_in)?$attendance->check_in:'NA' }}</span>

            </li>

            <li class="list-group-item">

              <b>Check Out</b> <span class="pull-right">{{ ($attendance->check_out)?$attendance->check_out:'NA' }}</span>

            </li>

            <li class="list-group-item">

              <b>Remark</b> <span class="pull-right">{{ ($attendance->remark)?strip_tags($attendance->remark):'NA' }}</span>

            </li>



          </ul>



          </div> --}}

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

      <script>

          $(function () {

              $("#company").DataTable();


              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)

                  var mid = button.data('mid')

                  var modal = $(this)

                  modal.find('.modal-body #m_id').val(mid);

              })

          });

      </script>



@endsection