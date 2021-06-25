<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Not Checked-in Today</h3>
                    <span id="dailyempreportexportsabsent" class="pull-right">
                        
                    </span>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="empfilterabsent"
                        style="background: #fff; cursor: pointer;position: absolute;margin-left: 15%;z-index: 999;width:250px;">
                    </div>
                    <div id="salesmfilterabsent"></div>
                    <table id="dailyempabsentreport" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Employee Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=0 @endphp
                            @forelse($attendance['absent_employees'] as $absent)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$absent->name}}</td>
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