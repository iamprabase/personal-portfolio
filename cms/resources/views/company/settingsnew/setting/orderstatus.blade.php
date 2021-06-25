

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

            <h3 class="box-title">Dynamic Order Status</h3>

            <a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal"
               data-target="#AddOrderStatus"> <i class="fa fa-plus"></i> Create New </a>

            <span id="orderstatusexports" class="pull-right"></span>


          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <table id="orderstatus" class="table table-bordered table-striped">

              <thead>

              <tr>

                {{-- <th>#</th> --}}

                <th>Status</th>

                <th>Included in order total</th>

                <th>Editable</th>

                <th>Deletable</th>

                <th>Action</th>

              </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($moduleAttributes as $moduleAttribute)

                @php($i++)

                <tr>

                  {{-- <td>{{ $i }}</td> --}}

                  <td>{{ $moduleAttribute->title}}</td>
                  @if($moduleAttribute->order_amt_flag==1)
                  <td>Yes</td>
                  @else
                  <td>No</td>
                  @endif

                  @if($moduleAttribute->order_edit_flag==1)
                  <td><i class="fa fa-check"></i><span hidden>Yes</span></td>
                  @else
                  <td><i class="fa fa-times"></i><span hidden>No</span></td>
                  @endif

                  @if($moduleAttribute->order_delete_flag==1)
                  <td><i class="fa fa-check"></i><span hidden>Yes</span></td>
                  @else
                  <td><i class="fa fa-times"></i><span hidden>No</span></td>
                  @endif

                  <td>

                    @if($moduleAttribute->title!="Approved" && $moduleAttribute->title!="Pending")
                      <a class="btn btn-warning btn-sm rowEditOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"
                          moduleAttribute-name="{{$moduleAttribute->title}}" @if($moduleAttribute->color) moduleAttribute-color="{{$moduleAttribute->color}}"@endif moduleAttribute-order_amt_flag="{{$moduleAttribute->order_amt_flag}}" moduleAttribute-order_edit_flag="{{$moduleAttribute->order_edit_flag}}" moduleAttribute-order_delete_flag="{{$moduleAttribute->order_delete_flag}}"  style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-sm delete rowDeleteOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"
                        moduleAttribute-name="{{$moduleAttribute->title}}" style="padding: 3px 6px;"><i
                          class="fa fa-trash-o"></i></a>
                    @elseif($moduleAttribute->title=="Approved" || $moduleAttribute->title=="Pending")
                    <a class="btn btn-warning btn-sm rowEditOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"
                      moduleAttribute-name="{{$moduleAttribute->title}}" @if($moduleAttribute->color)
                      moduleAttribute-color="{{$moduleAttribute->color}}"@endif
                      moduleAttribute-order_amt_flag="{{$moduleAttribute->order_amt_flag}}" moduleAttribute-order_edit_flag="{{$moduleAttribute->order_edit_flag}}" moduleAttribute-order_delete_flag="{{$moduleAttribute->order_delete_flag}}" style=" padding: 3px 6px;"><i
                        class="fa fa-edit"></i></a>
                    @endif
                  </td>

                </tr>

              @endforeach

              </tbody>
              <tfoot></tfoot>
            </table>

          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

      <!-- /.col -->

    </div>

    <!-- /.row -->


  <!-- Modal -->
  <!-- Button trigger modal -->
  <!-- Modal -->

  <div class="modal fade" id="AddOrderStatus" tabindex="-1" role="dialog">
    <form id="addNewStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.store')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create New Order Status</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;">
                Name
              </div>
              <div class="col-xs-10">
                <input class="form-control" type="text" name="name" required="">
                <span id='errlabel' class="label" style="color:red">
                  <span></span>
                </span>
              </div>
            </div>
            <div class="row" style="padding-top:5px;">
                <div class="col-xs-2" style="text-align: right;">
                      Color
                </div>
                <div class="col-xs-10">
                  {{-- <input class="form-control" type="text" name="color" value="#00c8f0" id="color" required=""> --}}
                  <div id="color" class="input-group colorpicker-component">
                      <input type="text" name="color" value="#38a677" class="form-control"/>
                      <span class="input-group-addon"><i></i></span>
                  </div>
                </div>
            </div>
            <div class="row" style="padding-top:5px;">
              <div class="col-xs-8 col-xs-offset-2">
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="order_amt_flag" id="order_amt_flag"> Include in order total
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="os_editable_flag" id="os_editable_flag"> Editable
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="os_deleteable_flag" id="os_deleteable_flag"> Deleteable
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
            <button id="addkeystatus" type="submit" class="btn btn-primary">Create</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <div class="modal fade" id="EditOrderStatus" tabindex="-1" role="dialog">
    <form id="editOrderStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.update')}}">@csrf
      <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" align="center">Update Order Status</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;line-height: 2.4em;">
                Name
              </div>
              <div class="col-xs-10">
                <input type="text" name="id" id="edit_id" hidden>
                <input class="form-control" id="edit_name" type="text" name="name" required="">
                <span id='ederrlabel' class="label" style="color:red">
                  <span></span>
                </span>
              </div>
            </div>
            <div class="row" style="padding-top:5px;">
                <div class="col-xs-2" style="text-align: right;">
                        Color
                </div>
                <div class="col-xs-10">
                    {{-- <input class="form-control" id="edit_color" type="color" name="color" required=""> --}}
                    {{-- <input class="form-control" type="text" name="color" id="edit_color" required="" value=""> --}}
                    <div id="aP_edit_color_pick" class="input-group hidden">
                      <input class="form-control" type="text" name="" id="aPedit_color" required value="">
                      <span id="color_span" class="input-group-addon"><i></i></span>
                    </div>
                    <div id="edit_color_pick" class="input-group colorpicker-component">
                        <input class="form-control" type="text" name="color" id="edit_color" required value="">
                        <span id="color_span" class="input-group-addon"><i></i></span>
                    </div>
                    
                </div>
            </div>
            <div class="row" style="padding-top:5px;">
              <div class="col-xs-8 col-xs-offset-2">
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="ed_order_amt_flag" id="ed_order_amt_flag"> Include in order total
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="os_editable_flag" id="ed_os_editable_flag"> Editable
                  </label>
                </div>
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="os_deleteable_flag" id="ed_os_deleteable_flag"> Deleteable
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button id="editkey" type="submit" class="btn btn-primary">Update</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->


  <div class="modal fade" id="DeleteOrderStatus" tabindex="-1" role="dialog">
    <form id="deleteExistingOrderStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.delete')}}">@csrf
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
                  Are you sure you want to Delete Order Status ( <span id="del_title"></span> ) ?
                </div>
                <input type="text" name="id" id="delete_id" hidden>
                <input hidden id="delete_name" type="text" name="name" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button id="delkey" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

