@extends('layouts.company')
@section('title', 'Leaves')
@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <style>
    .tree, .tree ul {
      margin: 0;
      padding: 0;
      list-style: none
    }

    .tree ul {
      margin-left: 1em;
      position: relative
    }

    .tree ul ul {
      margin-left: .5em
    }

    .tree ul:before {
      content: "";
      display: block;
      width: 0;
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      border-left: 1px solid
    }

    .tree li {
      margin: 0;
      padding: 0 1em;
      line-height: 2em;
      color: #369;
      font-weight: 700;
      position: relative
    }

    .tree ul li:before {
      content: "";
      display: block;
      width: 10px;
      height: 0;
      border-top: 1px solid;
      margin-top: -1px;
      position: absolute;
      top: 1em;
      left: 0
    }

    .tree ul li:last-child:before {
      background: #fff;
      height: auto;
      top: 1em;
      bottom: 0
    }

    .indicator {
      margin-right: 5px;
    }

    .tree li a {
      text-decoration: none;
      color: #369;
    }

    .tree li button, .tree li button:active, .tree li button:focus {
      text-decoration: none;
      color: #369;
      border: none;
      background: transparent;
      margin: 0px 0px 0px 0px;
      padding: 0px 0px 0px 0px;
      outline: 0;
    }
  </style>
@endsection

@section('content')
  <section class="content">
    <div class="panel panel-primary">
      <div class="panel-heading">Manage Area</div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h3>Area List</h3>
            <ul id="tree1">
              @foreach($marketareas as $marketarea)
                <li>
                  {{ $marketarea->title }}
                  @if(count($marketarea->childs))
                    @include('manageChild',['childs' => $marketarea->childs])
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
          <div class="col-md-6">
            <h3>Add New Area</h3>


            {!! Form::open(['route'=>'add.category']) !!}


            @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
              </div>
            @endif


            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
              {!! Form::label('Name:') !!}
              {!! Form::text('name', old('name'), ['class'=>'form-control', 'placeholder'=>'Enter Name']) !!}
              <span class="text-danger">{{ $errors->first('name') }}</span>
            </div>


            <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
              {!! Form::label('Area:') !!}
              {!! Form::select('parent_id',$allMarketareas, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Area']) !!}
              <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div>


            <div class="form-group">
              <button class="btn btn-success">Add New</button>
            </div>


            {!! Form::close() !!}


          </div>
        </div>


      </div>
    </div>
    <!-- /.row -->
  </section>

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
                action="{{URL::to('admin/leave/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="leave_id" id="leave_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="id">Remark</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                          rows="5"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="status" name="status">
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Pending">Pending</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'></span> Save
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>
          </form>
        </div>
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

          $.fn.extend({
              treed: function (o) {

                  var openedClass = 'glyphicon-minus-sign';
                  var closedClass = 'glyphicon-plus-sign';

                  if (typeof o != 'undefined') {
                      if (typeof o.openedClass != 'undefined') {
                          openedClass = o.openedClass;
                      }
                      if (typeof o.closedClass != 'undefined') {
                          closedClass = o.closedClass;
                      }
                  }
                  ;

                  /* initialize each of the top levels */
                  var tree = $(this);
                  tree.addClass("tree");
                  tree.find('li').has("ul").each(function () {
                      var branch = $(this);
                      branch.prepend("");
                      branch.addClass('branch');
                      branch.on('click', function (e) {
                          if (this == e.target) {
                              var icon = $(this).children('i:first');
                              icon.toggleClass(openedClass + " " + closedClass);
                              $(this).children().children().toggle();
                          }
                      })
                      branch.children().children().toggle();
                  });
                  /* fire event from the dynamically added icon */
                  tree.find('.branch .indicator').each(function () {
                      $(this).on('click', function () {
                          $(this).closest('li').click();
                      });
                  });
                  /* fire event to open branch if the li contains an anchor instead of text */
                  tree.find('.branch>a').each(function () {
                      $(this).on('click', function (e) {
                          $(this).closest('li').click();
                          e.preventDefault();
                      });
                  });
                  /* fire event to open branch if the li contains a button instead of text */
                  tree.find('.branch>button').each(function () {
                      $(this).on('click', function (e) {
                          $(this).closest('li').click();
                          e.preventDefault();
                      });
                  });
              }
          });
          /* Initialization of treeviews */
          $('#tree1').treed();
      });


  </script>

@endsection