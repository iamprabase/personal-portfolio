@extends('layouts.master')



@section('stylesheets')

  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

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

            <h3 class="box-title">Bank List</h3>

            <a href="{{ route('bank.create') }}" class="btn btn-primary pull-right" style="margin-left: 5px;">

              <i class="fa fa-plus"></i> Create New

            </a>

            <span id="bankexports" class="pull-right"></span>

          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <table id="bank" class="table table-bordered table-striped">

              <thead>

              @if( !$banks->isEmpty() )

                <tr>

                  <th>#</th>

                  <th>Name</th>

                  <th>Desc</th>

                  <th>Status</th>

                  <th>Action</th>

                </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($banks as $bank)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>{{ $bank->name}}</td>

                  <td>{{ $bank->desc}}</td>

                  <td>

                    @if($bank->status =='Active')

                      <span class="label label-success">{{ $bank->status}}</span>



                    @elseif($bank->status =='Disabled')

                      <span class="label label-warning">{{ $bank->status}}</span>



                    @else

                      <span class="label label-danger">{{ $bank->status}}</span>



                    @endif

                  </td>

                  <td>

                    <a href="{{ route('bank.show',$bank->id) }}" class="btn btn-success btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>

                    <a href="{{ route('bank.edit',$bank->id) }}" class="btn btn-warning btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>

                    <a class="btn btn-danger btn-sm" data-mid="{{ $bank->id }}"
                       data-url="{!! URL::route('bank.destroy', $bank->id) !!}" data-toggle="modal"
                       data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>

                    <a href="href" class="btn btn-info btn-sm" style="    padding: 3px 6px;"><i
                          class="fa fa-dashboard"></i></a>

                  </td>

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

            <input type="hidden" name="bank_id" id="c_id" value="">


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

  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

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


      $(document).ready(function () {

          var table = $('#bank').DataTable({

              //lengthChange: false,

              buttons: ['excel', 'pdf', 'print']

          });


          table.buttons().container()

              .appendTo('#bankexports');

      });


  </script>



@endsection