<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br/>           
    @endif
    <div class="box">
      <div class="box-header">
        @if(Auth::user()->can('note-create'))
        <a href="{{ domain_route('company.admin.notes.create') }}" class="btn btn-primary pull-right" style="margin-left: 5px;">
          <i class="fa fa-plus"></i> Create New
        </a>
        @endif
        <span id="notesexports" class="pull-right"></span>
      </div> 
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <table id="notesTable" class="table table-bordered table-striped">
          <thead>
          @if( !$meetings->isEmpty() )
            <tr>
              <th>#</th>
              <th>Notes</th>
              <th>Time</th>
              <th>Date</th>
              <th>Salesman</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          @php($i = 0)
          @foreach($meetings as $meeting)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{ $meeting->remark}}</td>
              <td>
                {{ date("h:i A", strtotime($meeting->created_at))}}
              </td>
              <td>{{ getDeltaDate(date("Y-m-d", strtotime($meeting->created_at)))}}</td>
              <td>
              @if($meeting->employee_id == 0)
                {{ Auth::user()->name.' (Admin)'}}
              @else
                @if(in_array($meeting->employee_id, $userJuniors))
                  <a class="empLinks" data-viewable="{{domain_route('company.admin.employee.show',[$meeting->employee_id])}}" href="{{domain_route('company.admin.employee.show',[$meeting->employee_id])}}"> {{ getEmployee($meeting->employee_id)['name']}}</a>
                @else
                  <a class="empLinks" data-viewable=""
                    href="#">
                    {{ getEmployee($meeting->employee_id)['name']}}</a>
                @endif
              @endif
              </td>
              <td>
                    <a href="{{ domain_route('company.admin.notes.show',[$meeting->id]) }}"
                       class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>
                    @if(Auth::user()->can('note-update'))
                    <a href="{{ domain_route('company.admin.notes.edit',[$meeting->id]) }}"
                       class="btn btn-warning btn-sm" style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                    @endif
                    @if(Auth::user()->can('note-delete'))
                    <a class="btn btn-danger btn-sm delete" data-mid="{{ $meeting->id }}"
                       data-backdrop="false"
                       data-url="{{ domain_route('company.admin.notes.destroy', [$meeting->id]) }}"
                       data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
                    @endif
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