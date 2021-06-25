@extends('layouts.company')


@section('title', 'Units')



@section('stylesheets')



  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

  <style>
    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
    .hide_column {
        display: none;
    }
    .round {
        position: relative;
        width: 15px;
    }
    .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        cursor: pointer;
        height: 15px;
        left: 0;
        position: absolute;
        top: 3px;
        width: 28px;
    }
    .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 0px;
        opacity: 0;
        position: absolute;
        top: 3px;
        transform: rotate(-45deg);
        width: 12px;
    }
    .round input{
        height: 10px;
    }
    .round input[type="checkbox"] {
        visibility: hidden;
    }
    .round input[type="checkbox"]:checked + label {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }
    .round input[type="checkbox"]:checked + label:after {
        opacity: 1;
    }
    .pad-left{
        padding-left: 0px;
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

          <?php if (session()->has('message')) {

              echo '<div class="alert alert-danger alert-dismissible">

                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

                        <h4><i class="icon fa fa-ban"></i> Alert!</h4>';

              echo session()->get('message');

              echo '</div>';

          }?>


        <div class="box">


          <div class="box-header">


            <h3 class="box-title">Units</h3>


            <a href="{{ domain_route('company.admin.unit.create') }}" class="btn btn-primary pull-right"
               style="margin-left: 5px;">


              <i class="fa fa-plus"></i> Create New


            </a>


            <span id="unitexports" class="pull-right"></span>


          </div>


          <!-- /.box-header -->


          <div class="box-body">


            <table id="units" class="table table-bordered table-striped">


              <thead>


              <tr>


                <th>#</th>


                <th>Name</th>


                <th>Symbol</th>


                <th>Status</th>


                <th>Action</th>


              </tr>


              </thead>


              <tbody>


              @php($i = 0)



              @foreach($units as $unit)



                @php($i++)



                <tr>


                  <td>{{ $i }}</td>


                  <td>{{ $unit->name}}</td>


                  <td>{{ $unit->symbol }}</td>


                  <td>

                    <a href="#" class="edit-modal" data-id="{{$unit->id}}"

                       data-status="{{$unit->status}}">


                      @if($unit->status =='Active')



                        <span class="label label-success">{{ $unit->status}}</span>



                      @else



                        <span class="label label-danger">{{ $unit->status}}</span>



                      @endif

                    </a>


                  </td>


                  <td>


                    <a href="{{ domain_route('company.admin.unit.edit',[$unit->id]) }}" class="btn btn-warning btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>


                    @if(checkUnitToDelete($unit->id))


                    @if($unit->conversionunits->count()<1 && $unit->convertedunits->count()<1)
                      <a class="btn btn-danger btn-sm" data-mid="{{ $unit->id }}"
                         data-url="{{ domain_route('company.admin.unit.destroy', [$unit->id]) }}" data-toggle="modal"
                         data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>

                    @endif

                    @endif

                  </td>


                </tr>



              @endforeach


              </tbody>


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



  <div class="modal modal-default fade" id="delete" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">


    <div class="modal-dialog" unit="document">


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

            <!-- <p class="text-center" style="color:red">

             Note: Deleting the group will delete all the users associated with this group.

           </p> -->


            <input type="hidden" name="unit_id" id="m_id" value="">


          </div>


          <div class="modal-footer">


            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}


            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>


          </div>


        </form>


      </div>


    </div>


  </div>



  <div id="myModal" class="modal fade" role="dialog">

    <div class="modal-dialog">

      <!-- Modal content-->

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title"></h4>

        </div>

        <div class="modal-body">

          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/unit/changeStatus')}}">

            {{csrf_field()}}

            <input type="hidden" name="unit_id" id="unit_id" value="">

            <div class="form-group">

              <label class="control-label col-sm-2" for="name">Status</label>

              <div class="col-sm-10">

                <select class="form-control" id="status" name="status">

                  <option value="Active">Active</option>

                  <option value="Inactive">Inactive</option>

                </select>

              </div>

            </div>


            <div class="modal-footer">

              <button type="button" class="btn actionBtn" onclick="confirmation()">

                <span id="footer_action_button" class='glyphicon'> </span> Change

              </button>

              {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">

                <span class='glyphicon glyphicon-remove'></span> Close

              </button> --}}

            </div>

          </form>

        </div>

      </div>

    </div>

  </div>





<form method="post" action="{{domain_route('company.admin.unit.customPdfExport')}}" class="pdf-export-form hidden"
    id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>

@endsection







@section('scripts')



  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>



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

      var table;
      $(document).ready(function () {

          @if (strpos(URL::previous(), domain_route('company.admin.unit')) === false)  
                  var activeRequestsTable = $('#units').DataTable();
                  activeRequestsTable.state.clear();  // 1a - Clear State
                  activeRequestsTable.destroy();   // 1b - Destroy
          @endif
          table = $('#units').DataTable({
              "columnDefs": [ {
                "targets": 4,
                "orderable": false
                }],
                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
              stateSave:true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'colvis',
                      order: 'alpha',
                      className: 'dropbtn',
                      columns:[0,1,2,3],
                      text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                      columnText: function ( dt, idx, title ) {
                          return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                      }
                  },

                  {

                      extend: 'excelHtml5',

                      title: 'Unit List',
                      
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },

                  },

                  {

                      extend: 'pdfHtml5',
                      action: function ( e, dt, node, config ) {
                        newExportAction( e, dt, node, config );
                      },
                      title: 'Unit List',
                      
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },

                  },

                  {

                      extend: 'print',

                      title: 'Unit List',
                      
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },

                  },

              ]


          });


          table.buttons().container()


              .appendTo('#unitexports');


      });


      $(document).on('click', '.edit-modal', function () {

          // $('#footer_action_button').text(" Change");

          $('#footer_action_button').addClass('glyphicon-check');

          $('#footer_action_button').removeClass('glyphicon-trash');

          $('.actionBtn').addClass('btn-success');

          $('.actionBtn').removeClass('btn-danger');

          $('.actionBtn').addClass('edit');

          $('.modal-title').text('Change Status');

          $('.deleteContent').hide();

          $('.form-horizontal').show();

          $('#unit_id').val($(this).data('id'));

          $('#remark').val($(this).data('remark'));

          $('#status').val($(this).data('status'));

          $('#warning').hide();

          $('#myModal').modal('show');

      });


      function confirmation() {

          var result = confirm('Confirm to change the status?');

          if (result == true) {

              $('#changeStatus').submit();

          } else {
          }

      }

      $(document).on('change', '#status', function () {

          if ($('#status option:selected').val() == 'Inactive')

              $("#warning").show();

          else

              $("#warning").hide();

      });

      function customExportAction(config, data, cols, props){
        $('#exportedData').val(JSON.stringify(data));
        $('#pageTitle').val(config.title);
        $('#columns').val(cols);
        $('#properties').val(props);
        $('#pdf-generate').submit();
      }
      
      var newExportAction = function (e, dt, button, config) {
        var self = this;
        var data = [];
        var count = 0;
        // table.rows({"search":"applied" }).every( function () {
        //   var row = {};
        //   row["id"] = ++count;
        //   row["name"] = this.data()[1];
        //   row["symbol"] = this.data()[2];
        //   row["status"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
        //   data.push(row);
        // });
        var columnsArray = [];
        var propertiesArray = [];
        var visibleColumns = dt.settings()[0].aoColumns.map(setting => {
                                if(setting.bVisible){
                                  let title = setting.sTitle.replace(/<[^>]*>?/gm, '');
                                  columnsArray.push(title);
                                  switch(title){
                                    case "#":
                                      propertiesArray.push("id")
                                      break;
                                    case "Name":
                                      propertiesArray.push("name")
                                      break;
                                    case "Symbol":
                                      propertiesArray.push("symbol")
                                      break;
                                    case "Status":
                                      propertiesArray.push("status")
                                      break;
                                    default:
                                      propertiesArray.push("action")
                                      break;
                                  }
                                } 
                              })    
        columnsArray.pop("Action")
        propertiesArray.pop("action")
        table.rows({"search":"applied" }).every( function () {
          var row = {};          
          if($.inArray("id", propertiesArray) != -1) row["id"] = ++count;
          if($.inArray("name", propertiesArray) != -1) row["name"] = this.data()[1];
          if($.inArray("symbol", propertiesArray) != -1) row["symbol"] = this.data()[2];
          if($.inArray("status", propertiesArray) != -1)  row["status"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
          data.push(row);
        });
        var properties = JSON.stringify(propertiesArray);
        var columns = JSON.stringify(columnsArray);
        customExportAction(config, data, columns, properties);
      };
      $(document).on('click','.buttons-columnVisibility',function(){
          if($(this).hasClass('active')){
              $(this).find('input').first().prop('checked',true);
              console.log($(this).find('input').first().prop('checked'));
          }else{
              $(this).find('input').first().prop('checked',false);
              console.log($(this).find('input').first().prop('checked'));
          }
      });

      $(document).on('click','.buttons-colvis',function(e){
          var filterBox = $('.dt-button-collection');
          filterBox.find('li').each(function(k,v){
              if($(v).hasClass('active')){
                  $(v).find('input').first().prop('checked',true);
              }else{
                  $(v).find('input').first().prop('checked',false);
              }
          });
      });

  </script>







@endsection