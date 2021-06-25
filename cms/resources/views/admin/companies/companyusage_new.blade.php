@extends('layouts.app')

@section('title', 'Companies Usage')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  #loader1{
    position: absolute;
    margin: auto 30%;
  }

  .box-loader{
    opacity: 0.5;
  }
</style>
@endsection

@section('content')

<section class="content">

  <div class="row">

    <div class="col-xs-12">

      @if (\Session::has('success'))

      <div class="alert alert-success">

        <p>{{ \Session::get('success') }}</p>

      </div><br />

      @endif

      <div class="box">

        <div class="box-header">

          <h3 class="box-title">Company Usage</h3>
{{-- 
          <a href="{{ route('app.company.create') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">

            <i class="fa fa-plus"></i> Create New

          </a> --}}

          <span id="companyexports" class="pull-right"></span>

        </div>

        <!-- /.box-header -->

        <div class="box-body table-responsive" id="mainBox">
          <div id="loader1">
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <table id="company" class="table table-bordered table-striped">

            <thead>

              {{-- @if( !$companies->isEmpty() ) --}}

              <tr>

                <th>#</th>

                <th>Company Name</th>

                <th>Active Users</th>

                <th>Last Activity Time</th>

                <th>Users who used app in Last 3 days</th>

                <th>Users who used app in Last 7 days</th>

              </tr>

            </thead>
{{-- 
            <tbody>

              @php($i = 0)

              @foreach($companies as $company)

              @php($i++)

              <tr>

                <td>{{ $i }}</td>

                <td>{{$company->company_name}}</td>

                <td>{{$company->active_users}}</td>

                <td>{{$company->last_activity_time}}</td>

                <td>{{$company->no_of_active_in_th_days}}</td>

                <td>{{$company->no_of_active_in_sv_days}}</td>

              </tr>

              @endforeach

            </tbody>

            @else

            <tr>

              <td colspan="10">No Record Found.</td>

            </tr>

            @endif --}}

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

<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>

        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>

      </div>

      <form method="post" class="remove-record-model">

        {{method_field('delete')}}

        {{csrf_field()}}

        <div class="modal-body">

          <p class="text-center">

            Are you sure you want to delete this?

          </p>

          <input type="hidden" name="company_id" id="c_id" value="">


        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button>

          <button type="submit" class="btn btn-warning">Yes, Delete</button>

        </div>

      </form>

    </div>

  </div>

</div>



@endsection



@section('scripts')
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

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

      });


      // $(document).ready(function () {

      //     var table = $('#company').DataTable({

      //         //lengthChange: false,

      //         buttons: ['excel', 'pdf', 'print']

      //     });


      //     table.buttons().container()

      //         .appendTo('#companyexports');

      // });

      const columns = [{ "data": "id" },
      { "data": "company_name" },
      { "data": "num_active_users" },
      { "data": "last_activity_time" },
      { "data": "last_th_days" },
      { "data": "last_sv_days" }];
      $(document).ready(function () {
        initializeDT();
      });

      function initializeDT(filterDays=null, account_status=null, customer_status=null){
        table = $('#company').DataTable({
          "stateSave": false,
          language: { search: "" },
          "order": [[0, "desc" ]],
          "serverSide": true,
          "processing": true,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +"<'row'<'col-xs-6'><'col-xs-6'>>" +"<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
          "columnDefs": [
            {
              "orderable": false,
              "targets":[-1, -2, -3, -4]
            },],
          "buttons": [
            {
              extend: 'pdfHtml5',
              orientation: 'landscape', 
              paperSize: 'A4',
              title: 'Company Usage List', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Company Usage List', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              orientation: 'landscape',
              paperSize: 'A4',
              title: 'Company Usage List', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('app.companyusage.fetchUsageCompany') }}",
            "dataType": "json",
            "type": "GET",
            "data":{ 
              _token: "{{csrf_token()}}"
            },
            beforeSend:function(url, data){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
              $('.tips').tooltip();
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
              $('.tips').tooltip();
            },
            complete:function(){
              $('.tips').tooltip();
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": columns,
        });
        table.buttons().container()
            .appendTo('#companyexports');
        var oldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };

        var newExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$companies_count}};
            dt.one('preDraw', function (e, settings) {
              // if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              //   // customExportAction(dt, data, config, settings);
              //   $.each(settings.json.data, function(key, htmlContent){
              //     settings.json.data[key].id = key+1;
              //     settings.json.data[key].partyname = $(settings.json.data[key].partyname)[0].textContent;
              //     settings.json.data[key].createdby = $(settings.json.data[key].createdby)[0].textContent;
              //     settings.json.data[key].orderstatus = $(settings.json.data[key].orderstatus)[0].textContent; 
              //   });
              //   customExportAction(config, settings);
              // }else{
                oldExportAction(self, e, dt, button, config);
              // }
              // oldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      } 


</script>



@endsection