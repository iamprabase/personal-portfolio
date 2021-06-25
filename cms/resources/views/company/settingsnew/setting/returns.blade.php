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

        <div class="">

          <div class="box-header">

          <h3 class="box-title">Return Reasons</h3>

            <a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal" data-target="#AddReturnReasons"> <i class="fa fa-plus"></i> Create New </a>
          <span id="returnreasonsexports" class="pull-right"></span>


          </div>

          <!-- /.box-header -->

          <div class="box-body">

          <div id="retTableView">
                @include('company.settingsnew.setting.returnreasonpartial')
            </div>

          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

      <!-- /.col -->

    </div>

    <!-- /.row -->

    
    <div class="modal fade" id="AddReturnReasons" tabindex="-1" role="dialog">
        <form id="addNewReturnReason" method="post" action="{{domain_route('company.admin.returnreason.store')}}">@csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Create New Return Reason</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-2" style="text-align: right;">
                                Name
                            </div>
                            <div class="col-xs-10">
                                <input class="form-control" type="text" name="name" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                        <button id="addkey" type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
    
    <div class="modal fade" id="EditReturnReason" tabindex="-1" role="dialog">
        <form id="editReturnReason" method="post" action="{{domain_route('company.admin.returnreason.update')}}">
            @csrf
            <div class="modal-dialog small-modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" align="center">Update Return Reason</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-2" style="text-align: right;line-height: 2.4em;">
                                Name
                            </div>
                            <div class="col-xs-10">
                                <input type="text" name="id" id="editreturn_reason_id" hidden>
                                <input class="form-control" id="editreturn_reason_name" type="text" name="name" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
                        <button id="updatekey" type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
    
    
    <div class="modal fade" id="DeleteReturnReason" tabindex="-1" role="dialog">
        <form id="deleteReturnReason" method="post" action="{{domain_route('company.admin.returnreason.delete')}}">
            @csrf
            <div class="modal-dialog small-modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" align="center">Deletion Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div align="center">
                                    Are you sure you want to Delete Return reason?
                                </div>
                                <input type="text" name="id" id="delete_return_reason_id" hidden>
                                <input hidden id="delete_return_reason_name" type="text" name="name" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
                        <button id="delreturnreasonkey" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
