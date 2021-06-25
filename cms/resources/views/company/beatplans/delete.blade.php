

<div class="modal fade" id="del_event_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

  {!! Form::open(array('url' => url(domain_route("company.admin.beatplan.delete")), 'method' => 'post','id'=>'delete_event', 'files'=> true)) !!}

  <div class="modal-dialog modal-dialog-centered" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 text-align="center" class="modal-title" id="exampleModalLongTitle">

          Are you sure you want to delete this beatplan?

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </h4>

      </div>

      <div class="modal-body">

        <input type="text" hidden name="del_id" id="del_id" value="">

        <input type="text" hidden name="beat_del_id" id="beat_del_id" value="">

        <input type="text" hidden name="get_id" id="get_id" value="">

        <input type="text" hidden name="empl_id" id="empl_id" value="">

      </div>

      <div class="modal-footer">

        {{-- <button type="button" class="btn btn-success canclBtn" data-dismiss="modal">No,Cancel</button> --}}

        <button type="submit" class="btn btn-warning delBtn">Yes,Delete</button>

      </div>

    </div>

  </div>

   {!! Form::close() !!}

</div>

