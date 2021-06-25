<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Reminders</h3>

        <span id="reminderexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table id="order" class="table table-bordered table-striped">
          <thead>
          @if( !$tasks->isEmpty() )
            <tr>
              <th>#</th>
              <th>Due Date</th>
              <th>Task assigned to</th>
              <th>Task title</th>
              <th>Task Description</th>
              <th>Priority</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          @php($i = 0)
          @foreach($tasks as $task)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{ date("d M Y", strtotime($task->due_date))}}</td>
              <td>{{ getEmployee($task->assigned_to)['name']}}</td>
              <td>{{ $task->title }}</td>
              <td>{!! $task->description !!}</td>
              <td>{{ $task->priority }}</td>
              <td>{{ $task->status }}</td>
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