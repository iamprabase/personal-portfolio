@extends('layouts.app')

@section('title', 'Companies')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  .flexBtnDiv{
    display: inline-flex;
  }

  .btnsFlex{
    margin-right: 2px;
  }
  .box-loader{
    opacity: 0.5;
  }

  .stsfilter{
    margin-right: 4px;
  }

  #loader1{
    position: absolute;
    margin: auto 30%;
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

          </div><br/>

        @endif

        <div class="box">

          <div class="box-header">

            <h3 class="box-title">Company List</h3>

            <a href="{{ route('app.company.create') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">

              <i class="fa fa-plus"></i> Create New

            </a>

            <span id="companyexports" class="pull-right"></span>
            <div class="dropdown pull-right tips" style="margin-right: 5px;">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">⋮</button>
              <ul class="dropdown-menu">
                <li><a href="#" class="subs_filter" data-type="">All Customers & Trial</a></li>
                <li><a href="#" class="subs_filter" data-type="45">Customers whose subscription is expiring in next 45 days</a></li>
                <li><a href="#" class="subs_filter" data-type="30">Customers whose subscription is expiring in next 30 days</a></li>
              </ul>
            </div>
            <input type="hidden" name="sts_days" id="sts_days">

          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <div class="row">
              <div class="col-xs-2"></div>
              <div class="col-xs-3 stsfilter hidden">
                {{-- <div class="stsfilter"> --}}
                  <select class="form-control" id="account_status"
                    style="position: absolute;z-index: 999;">
                    <option value="">Search By Account Status</option>
                    <option value="2">Active</option>
                    <option value="4">Extension</option>
                    <option value="0">Expired</option>
                    <option value="1">Disabled</option>
                  </select>
                {{-- </div> --}}
              </div>
              <div class="col-xs-3 stsfilter hidden">
                {{-- <div class=""> --}}
                  <select class="form-control" id="customer_status"
                    style="position: absolute;z-index: 999;">
                    <option value="">Search By Customer Status</option>
                    <option value="customer">Customer</option>
                    <option value="trial">Trial</option>
                  </select>
                {{-- </div> --}}
              </div>
            </div>
            <div id="mainBox">
              <div id="loader1">
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
              <table id="company" class="table table-bordered table-striped">
  
                <thead>
  
                {{-- @if( !$companies->isEmpty() ) --}}
  
                  <tr>
  
                    <th>#</th>
  
                    <th>Company Name</th>
  
                    <th>Domain</th>
  
                    <th>Contact Person</th>
  
                    <th>Phone</th>
                    
                    <th>Email</th>
  
                    <th>Valid To</th>
  
                    <th>Plan</th>
  
                    <th>Number of Users</th>
  
                    <th>Account Status</th>
  
                    <th>Customer Status</th>
  
                    <th>Action</th>
  
                  </tr>
  
                </thead>
  
                <tbody>
  
                {{-- @php($i = 0) --}}
  
                {{-- @foreach($companies as $company)
  
                  @php($i++)
  
                  <tr>
  
                    <td>{{ $i }}</td>
  
                    <td>{{ $company->company_name}}</td>
  
                    <td>{{ $company->domain}}</td>
  
                    @if(isset($company->contact_name))
                    <td>{{ $company->contact_name }}</td>
                    @elseif($company->managers()->first())
                    <td>{{ $company->managers()->first()->contact_name }}</td>
                    @else
                    <td></td>
                    @endif
  
                    <td>{{ $company->contact_phone}}</td>
  
                    <td>{{ $company->contact_email}}</td>
                    
                    <td>{{ date('d M Y', strtotime($company->end_date)) }}</td>
  
                    <td>{{ $company->plan_name }}</td>
  
                    <td>{{ $company->num_users}}</td>
  
                    <td>
                      @if(date('Y-m-d')>$company->end_date && $company->is_active =='2')
                        <span class="label label-warning" data-attr="Extension">Extension</span>
                      @else
                        @if($company->is_active =='2')
                        
                        <span class="label label-success" data-attr="Active">Active</span>
                        
                        
                        
                        @elseif($company->is_active =='1')
                        
                        <span class="label label-primary" data-attr="Disabled">Disabled</span>
                        
                        
                        
                        @else
                        
                        <span class="label label-danger" data-attr="Expired">Expired</span>
                        
                        
                        
                        @endif
                      @endif
  
                    </td>
  
                    <td>
  
                      <a href="{{ route('app.company.edit',$company->id) }}" class="btn btn-warning btn-xs"
                         style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
  
                      <a href="{{ route('app.company.setting',$company->id) }}" class="btn btn-warning btn-xs" style="padding: 3px 6px;"><i class="fa fa-cog"></i></a>
  
                      <a href="{{ route('app.company.empdownload',$company->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a>
                      
                      <a href="#notes" class="btn btn-warning btn-xs" id="add-note" data-note="{{$company->note}}" data-href="{{ route('app.company.notes',$company->id) }}"><i class="glyphicon glyphicon-book"></i></a>
                    </td>
  
                  </tr>
  
                @endforeach --}}
  
                </tbody>
                {{-- @else
  
                  <tr>
  
                    <td colspan="10">No Record Found.</td>
  
                  </tr>
  
                @endif --}}
  
  
              </table>

            </div>

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

  <div class="modal modal-default fade" id="notes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  
    <div class="modal-dialog" role="document">
  
      <div class="modal-content">
  
        <div class="modal-header">
  
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
  
          <h4 class="modal-title text-center" id="myModalLabel">Add a Note</h4>
  
        </div>
  
        <form method="post" class="add-note-model">
  
          {{method_field('post')}}
  
          {{csrf_field()}}
  
          <div class="modal-body">
  
            <div class="form-group">
              <label for="exampleFormControlTextarea1">Note</label>
              <textarea class="form-control added-note" id="added-note" name="company_note" rows="10"></textarea>
            </div>

  
          </div>
  
          <div class="modal-footer">
  
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
  
            <button type="submit" class="btn btn-warning">Submit</button>
  
          </div>
  
        </form>
  
      </div>
  
    </div>
  
  </div>

  <div class="modal modal-default fade" id="updateStatuses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  
    <div class="modal-dialog" role="document">
  
      <div class="modal-content">
  
        <div class="modal-header">
  
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
  
          <h4 class="modal-title text-center" id="myModalLabel">Update <span id="updateLabel"></span> Status</h4>
  
        </div>
  
        <form method="post" class="update-status-model">
  
          {{method_field('post')}}
  
          {{csrf_field()}}
          <input type="hidden" name="field_type" id="field_type">
          <div class="modal-body">
  
            <div class="form-group" id="status-option">
              
            </div>
  
  
          </div>
  
          <div class="modal-footer">
  
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
  
            <button type="submit" class="btn btn-warning" id="status-submit-btn">Submit</button>
  
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

          $(document).on("click", '#add-note', function()  {

              $("#notes").modal();
              var url = $(this).data('href');
              // $(".remove-record-model").attr("action",url);

              $(".add-note-model").attr("action", url);

              $('#added-note').val($(this).data('note'));



          });

          $(document).on("click", '.update-status-modal', function()  {

              var selectOption;
              var company_id = $(this).data('company_id');
              var url = $(this).data('action');
              var field_type = $(this).data('status-type');
              var currentValue = $(this).data('value');
              $('#updateStatuses').modal('show');
              $(".update-status-model").attr("action", url);

              $(".update-status-model").find('#field_type').val(field_type);

              $(".update-status-model").find('#status-option').html("");
              if(field_type=="is_active"){
                $('#updateLabel').html("Account");
                selectOption = '<select name="chosen_status" class="form-control updateFields"><option value="2">Active</option><option value="1">Disabled</option><option value="0">Expired</option></select><div class="row otp-row"><div class="col-xs-6"><label>Please Enter OTP</label></div><div class="col-xs-8">  <input type="text" name="otp" class="form-control otp"></div><div class="col-xs-4"> <button class="btn btn-info generate-otp" data-company_id='+company_id+'>Genrate And Mail OTP</button></div></div>';
              }else{
                $('#updateLabel').html("Customer");
                selectOption = '<select name="chosen_status" class="form-control updateFields"><option value="trial">Trial</option><option value="customer">Customer</option></select>';
              }

              $(".update-status-model").find('#status-option').html(selectOption).find(".updateFields").val(currentValue);
              // if(currentValue==2) $('.otp-row').attr('hidden', 'hidden');
          });

      });

      const columns = [{ "data": "id" },
      { "data": "company_name" },
      { "data": "domain" },
      { "data": "contact_name" },
      { "data": "contact_phone" },
      { "data": "contact_email" },
      { "data": "end_date" },
      { "data": "company_plan" },
      { "data": "num_users" },
      { "data": "is_active" },
      { "data": "customer_status" },
      { "data": "action" }];
      $(document).ready(function () {

          // var table = $('#company').DataTable({

          //     //lengthChange: false,
              // "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +"<'row'<'col-xs-6'><'col-xs-6'>>" +"<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
          //     buttons: ['excel', 'pdf', 'print']

          // });


          // table.buttons().container()

          //     .appendTo('#companyexports');
          
          // var select = $('<select class="select2" style="background: #fff;width:100% !important; cursor: pointer;position: absolute;z-index: 999;"><option value="">Search By Account Status</option></select>')
          //     .appendTo($('.stsfilter'))
          //     .on('change', function () {
          //         table.column(9)
          //             .search($(this).val())
          //             .draw();
          //     });

          // table.column(9).data().unique().sort().each(function (d, j) {
          //     select.append( '<option value="'+$(d).data('attr')+'">'+$(d).data('attr')+'</option>' )
          // });
          

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
          "dom":  "<''<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +"<'row'<'col-xs-6'><'col-xs-6'>>" +"<''<'col-xs-12 table-responsive't>><'row'<'col-xs-12'ip>>", 
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            },],
          "buttons": [
            {
              extend: 'pdfHtml5',
              orientation: 'landscape', 
              paperSize: 'A4',
              title: 'Company List', 
              exportOptions: {
                columns: [0,1,2,3,4,5,6, 7, 8, 9, 10],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Company List', 
              exportOptions: {
                columns: [0,1,2,3,4,5,6, 7, 8, 9, 10],
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
              title: 'Company List', 
              exportOptions: {
                columns: [0,1,2,3,4,5,6, 7, 8, 9, 10],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('app.company.fetchrecords') }}",
            "dataType": "json",
            "type": "GET",
            "data":{ 
              _token: "{{csrf_token()}}", 
              filterDays: filterDays,
              account_status: account_status,
              customer_status: customer_status
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
          drawCallback:function(settings)
          {
            $('#totalValue').html(settings.json.total);
            $('#pageIds').html(settings.json.prevSelVal);
          }
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

      $(document).on("click", '#status-submit-btn', function(e)  {
        e.preventDefault();
        let current = $(this);
        let valSelected = $(this).parent().parent().find('.updateFields').val();
        let actionUrl = $(this).parent().parent()[0].action;
        let field_type = $(this).parent().parent().find('#field_type').val();
        let otp = $(this).parent().parent().find('.otp').val();
        // if((valSelected == "0" || valSelected == "1") && $('.otp').val()==""){
        if($('.otp').val()==""){
          $('.otp').focus();
          alert("Please Enter OTP");
        }else{
          $.ajax({
            "url": actionUrl,
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              field_type: field_type,
              chosen_status: valSelected,
              otp: otp
            },
            beforeSend:function(url, data){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
              current.attr('disabled', true);
              $('.generate-otp').attr('disabled', true);
            },
            success: function(res){
              alert(res.msg);
              current.attr('disabled', false);
              $('.generate-otp').attr('disabled', false);
              if(res.status===200) location.reload();
            },
            error:function(xhr){
              if(xhr.status==422){
                let msg = xhr.responseJSON.errors.otp[0];
                alert(msg);
                $('.otp').focus();
              }
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
              current.attr('disabled', false);
              $('.generate-otp').attr('disabled', false);
            },
            complete:function(res){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
              $(this).attr('disabled', false);
              $('.generate-otp').attr('disabled', false);
            }
          });
        }
      });
      $(document).on("click", '.generate-otp', function(e)  {
        e.preventDefault();
        let current = $(this);
        let company_id = current.data("company_id");
        $.ajax({
          "url": "{{domain_route('app.company.generateAndMailOTP')}}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            company_id: company_id,
          },
          beforeSend:function(url, data){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            current.attr('disabled', true);
            $('#status-submit-btn').attr('disabled', true);
            // $('.tips').tooltip();
          },
          success: function(res){
            alert(res.msg);
            current.attr('disabled', false)
            $('#status-submit-btn').attr('disabled', false);
          },
          error:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
            current.attr('disabled', false);
            $('#status-submit-btn').attr('disabled', false);
            // $('.tips').tooltip();
          },
          complete:function(res){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
            $(this).attr('disabled', false);
            $('#status-submit-btn').attr('disabled', false);
          }
        });
      });
      // $(document).on("change", '.updateFields', function(e)  {
      //   let currentValue = $('.updateFields').val();
      //   if(currentValue==2) $('.otp-row').attr('hidden', 'hidden');
      //   if(currentValue==1||currentValue==0) $('.otp-row').removeAttr('hidden');
      // });

      $('body').on("click", ".subs_filter",function () {
        var stsDays = $(this).data('type');
        $('#sts_days').val(stsDays);
        $('#company').DataTable().destroy();
        var account_status = $('#account_status').val();
        var customer_status = $('#customer_status').val();
        initializeDT(stsDays, account_status, customer_status);
      });

      $('body').on("change", "#account_status",function () {
        var account_status = $(this).val();
        var stsDays = $('#sts_days').val();
        var customer_status = $('#customer_status').val();
        $('#company').DataTable().destroy();
        initializeDT(stsDays, account_status, customer_status);
      });
      $('body').on("click", "#customer_status",function () {
        var customer_status = $(this).val();
        var account_status = $('#account_status').val();
        var stsDays = $('#sts_days').val();
        $('#company').DataTable().destroy();
        initializeDT(stsDays, account_status, customer_status);
      });
      $('.stsfilter').removeClass('hidden');
  </script>



@endsection