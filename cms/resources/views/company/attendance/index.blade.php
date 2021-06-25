@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Attendance List</h3>
          <!-- <a href="{{ domain_route('company.admin.attendance.create') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a> -->
            <span id="attendanceexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div id="reportrange" name="reportrange"
                 style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: auto;position: absolute;margin-right: 20%;z-index: 999;right:0px;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span> <i class="fa fa-caret-down"></i>
            </div>
            <table id="attendance" class="table table-bordered table-striped">
              <thead>
              @if( !$attendances->isEmpty() )
                <tr>
                  <th>#</th>
                  <th>Employee Name</th>
                  <th>Date</th>
                  <th>Worked Hours</th>
                  <!-- <th>Remark</th> -->
                  {{-- <th>Action</th> --}}
                </tr>
              </thead>
              <tbody>
              @php($i = 0)
              @foreach($attendances as $attendance)

                @php($i++)
                <tr>
                  <td>{{ $i }}</td>
                  <td>{{ getEmployee($attendance->employee_id)['name'] }}</td>
                  <td>{{ date('d M Y',strtotime($attendance->check_datetime)) }}</td>
                  <td>{{ getWorkedHour($attendance->employee_id, date('Y-m-d', strtotime($attendance->check_datetime))) }}
                    @if(getWorkedHour($attendance->employee_id, date('Y-m-d', strtotime($attendance->check_datetime)))!=='0 hr 0 min')
                      <a class="btn btn-success btn-sm hourdetail" data-mid="{{ $attendance->employee_id }}"
                         data-mdate="{{ date('Y-m-d', strtotime($attendance->check_datetime)) }}"
                         data-url="{{ domain_route('company.admin.attendance.destroy', [$attendance->id]) }}"
                         style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>
                    @endif</td>
                <!-- <td>{{ strip_tags($attendance->remark) }}</td> -->
                  
                  {{-- <td>
               <a href="{{ domain_route('company.admin.attendance.show',[$attendance->employee_id, date('Y-m-d', strtotime($attendance->check_datetime))]) }}" class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a> 
                <a href="{{ domain_route('company.admin.attendance.edit',[$attendance->id]) }}" class="btn btn-warning btn-sm" style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                
                  </td> --}}
                </tr>
              @endforeach
              </tbody>
              @else
                <tr>
                  <td colspan="10">No Record Found.</td>
                </tr>
              @endif
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>


  <!-- Modal -->
  <div class="modal modal-default fade" id="workhours" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">CheckIN-CheckOut Details</h4>
        </div>
        <form method="post" class="remove-record-model">
          {{method_field('delete')}}
          {{csrf_field()}}
          <div class="modal-body">
            <div id="hourdetails"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script>
      $(function () {

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget)
              var mid = button.data('mid')
              var url = button.data('url');
              // $(".remove-record-model").attr("action",url);
              $(".remove-record-model").attr("action", url);
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          })

          var table = $('#attendance').DataTable({
              buttons: [

                  {
                      extend: 'excelHtml5',
                      title: 'Attendance List'
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Attendance List'
                  },
                  {
                      extend: 'print',
                      title: 'Attendance List'
                  },
              ]
          });

          table.buttons().container()
              .appendTo('#attendanceexports');

          $('#reportrange').bind('DOMSubtreeModified', function (event) {
              table.draw();
          });

          var start = moment().subtract(29, 'days');
          var end = moment();

          function cb(start, end) {
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#startdate').val(start.format('MMMM D, YYYY'));
              $('#enddate').val(end.format('MMMM D, YYYY'));
          }

          $('#reportrange').daterangepicker({
              startDate: start,
              endDate: end,
              ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
          }, cb);

          cb(start, end);

          $.fn.dataTable.ext.search.push(
              function (settings, data, dataIndex) {
                  var start2 = $('#reportrange').data('daterangepicker').startDate;
                  var end2 = $('#reportrange').data('daterangepicker').endDate;
                  var start_date = Date.parse(start2.format('MMMM D, YYYY'));
                  var end_date = Date.parse(end2.format('MMMM D, YYYY'));
                  var create_date = Date.parse(data[2]); // use data for the age column
                  if (create_date >= start_date && create_date <= end_date) {
                      return true;
                  }
                  return false;
              }
          );

      });


  </script>

  <script>

      $(document).ready(function () {

          $('.hourdetail').click(function () {
              var mapdate = $(this).data('mdate');
              var emp = $(this).data('mid');
              //var rowid = $(this).find('.mapgenerate').data('rowid');
              // alert(mapdate);alert(emp);

              $.ajax({
                  type: 'GET',
                  dataType: 'html',
                  url: "{{ domain_route('company.admin.reports.gethoursdetails') }}",
                  data: {
                      'eid': emp,
                      'mapdate': mapdate,
                  },
                  success: function (data2) {
                      //alert(data2);
                      $('#hourdetails').html(data2);
                      $("#workhours").modal('show');


                  }
              });
          });
      });
  </script>

@endsection