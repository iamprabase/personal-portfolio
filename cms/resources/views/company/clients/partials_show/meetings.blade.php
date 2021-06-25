 {{-- <div class="row">
     <div class="col-xs-12">
       @if (\Session::has('success'))
         <div class="alert alert-success">
           <p>{{ \Session::get('success') }}</p>
         </div><br />
        @endif
       <div class="box">
         <div class="box-header">
           <h3 class="box-title">Meeting List</h3>

       <span id="orderexports" class="pull-right"></span>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
           <table id="order" class="table table-bordered table-striped">
             <thead>
               @if( !$meetings->isEmpty() )
             <tr>
               <th>#</th>
               <th>SalesMan</th>
               <th>Remark</th>
               <th>Time</th>
               <th>Date</th>
               <th>Action</th>
             </tr>
             </thead>
             <tbody>
               @php($i = 0)
               @foreach($meetings as $meeting)
                @php($i++)
             <tr>
               <td>{{ $i }}</td>
               <td>{{ getEmployee($meeting->employee_id)['name']}}</td>
               <td>{{ $meeting->remark}}</td>
               <td>{{ $meeting->checkintime}}</td>
               <td>{{ $meeting->meetingdate}}</td>

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
   </div> --}}