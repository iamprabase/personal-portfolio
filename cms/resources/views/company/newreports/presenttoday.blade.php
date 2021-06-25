<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Checked-in Today</h3>
                    <a href="{{ domain_route('company.admin.home') }}" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i>Back</a>
                    <span id="dailyempreportexportspresent" class="pull-right">
                    </span>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="empfilter"
                        style="background: #fff; cursor: pointer;position: absolute;margin-left: 15%;z-index: 999;width:250px;">
                    </div>
                    <div id="salesmfilter"></div>
                    <table id="dailyemppresentreport" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Employee Name</th>
                                <th>Check-in Time</th>
                                <th>Check-in Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=0; @endphp
                            @forelse($attendance['present_employees'] as $present)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$present->name}}</td>
                                    <td>{{date("h:i A", strtotime($present->atime))}}</td>
                                    <td>{{$present->address}}</td>
                                </tr>
                            @empty
                            @endif
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
