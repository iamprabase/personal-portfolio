@extends('layouts.company')
@section('title', 'Day Remarks')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<style>
.updated_time{
    font-size: 10px;
    font-style: italic;
    padding-left:3px;
}
</style>
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                {{-- <h3 class="box-title">Day Remarks</h3> --}}
                <div class="page-action pull-left">
                  <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
                </div>
              </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Day Remarks</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <colgroup>
                                <col class="col-xs-2">
                                <col class="col-xs-7">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th scope="row"> Employee Name</th>
                                    <td>{{ getEmployee($dayremark->employee_id)['name']}}</td>
                                </tr>
                                <tr>
                                    <th scope="row"> Date</th>
                                    <td>                    
                                        {{getDeltaDate($dayremark->remark_date)}}
                                    </td>
                                </tr>
                                <?php $i=0; ?>
                                @foreach($dayremarksOnDate as $remarkTime)
                                <tr>
                                    <th>@if($i==0)Remarks <?php $i++; ?> @endif</th>
                                    <td>

                                  
                                        <span class="dayremark dayremark_{{$remarkTime->id}} viewContent">
                                            @if(Auth::user()->can('dayremark-delete'))
                                            <button class="danger-btn-right deleteContent" type="button" data-id="{{$remarkTime->id}}"><i class="fa fa-trash"></i></button>
                                            @endif
                                            @if(Auth::user()->can('dayremark-update'))
                                            <button class="primary-btn-right editContent" type="button" data-id="{{$remarkTime->id}}"><i class="fa fa-edit"></i></button>
                                            @endif
                                            <span class="content_text_{{$remarkTime->id}}">
                                                {!!$remarkTime->remark_details!!}
                                                <span class="updated_time">
                                                ( last updated at: 
                                                <?php $time_updated_at = Carbon\Carbon::parse($remarkTime->updated_at)->format('h:i A');  
                                                $updated_at = getDeltaDate(Carbon\Carbon::parse($remarkTime->updated_at)->format('Y-m-d')); ?>
                                                {{$updated_at}}&nbsp;{{$time_updated_at}}
                                                )
                                                </span>
                                                <span class="pull-right">
                                                    {{\Carbon\Carbon::parse($remarkTime->created_at)->format('h:i A')}}
                                                </span>
                                            </span> 
                                        </span>
                                        <span class="formContent dayremark_edit_{{$remarkTime->id}} hide">
                                            {{-- <input type="text" class="textbox-ninty textbox-bottom-bordered remark_value_{{$remarkTime->id}}" value="{{$remarkTime->remark_details}}"/> --}}
                                            <textarea class="form-control ckeditor remark_value_{{$remarkTime->id}}" rows="1" placeholder="Order Notes" name="remark{{$remarkTime->id}}" cols="50">{{$remarkTime->remark_details}}</textarea>
                                            <span class="dayremark_update_{{$remarkTime->id}}">
                                                <button class="danger-btn-right cancel_dayremark" data-id="{{$remarkTime->id}}"><i class="fa fa-times"></i></button>
                                                <button class="success-btn-right update_dayremark" data-id="{{$remarkTime->id}}"><i class="fa fa-check"></i></button>
                                            </span>
                                            <span class="dayremark_refresh_{{$remarkTime->id}} hide">
                                                <button class="success-btn-right refreshing"><i class="fa fa-refresh"></i></button>
                                            </span>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Delete Day Remark</h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <div class="text-center">
                                Are you sure you want to delete this day remark?
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button id="btn_delete_dayremark" type="button" class="btn btn-primary actionBtn" data-id="">
                                <span id="footer_action_button" class='glyphicon'></span> Delete
                            </button>
                            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
                                <span class='glyphicon glyphicon-remove'></span> Cancel
                            </button> --}}
                        </div>
                </div>
            </div>
        </div>
    </div>


</section>
<div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

<script>
    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("img01");

    $('.display-imglists').on('click',function(){
        modal.style.display = "block";
        modalImg.src = this.src;
    });

    $('.close').on('click',function(){
        modal.style.display = "none";
    });

    $('.editContent').on('click',function(){
        let id = ($(this).data('id'));
        $('.dayremark_'+ id).addClass('hide');
        $('.dayremark_edit_'+id).removeClass('hide');
        CKEDITOR.instances['remark'+id].resize('100%', 70, true);
    });

    $('.cancel_dayremark').on('click',function(){
        let id = ($(this).data('id'));
        $('.dayremark_edit_'+id).addClass('hide');
        $('.dayremark_'+ id).removeClass('hide');
    });

    $('.textbox-ninty').on('keypress', function(event){
      if(event.keyCode == 13){
        $('.update_dayremark').click();
      }
    });

    $('.update_dayremark').on('click',function(){
        let id = ($(this).data('id'));
        let remark = CKEDITOR.instances['remark'+id].getData();//$('.remark_value_'+id).val();
        let url = '{{domain_route("company.admin.dayremarks.ajaxupdate")}}';
        $('.dayremark_update_'+id).addClass('hide');
        $('.dayremark_refresh_'+id).removeClass('hide');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                "id":id,
                "remark":remark,
                "parsed_remark": jQuery(remark).text().trim()
            },
            success: function (data) {
                console.log('ajax successed');
                if(data.status==true){
                    $('.dayremark_refresh_'+id).addClass('hide');
                    $('.dayremark_update_'+id).removeClass('hide');
                    $('.dayremark_edit_'+id).addClass('hide');
                    $('.dayremark_'+ id).removeClass('hide');
                    $('.content_text_'+id).html(data.remark_time);
                }
            },
            error:function(error){
                alert(error.responseJSON.errors.remark[0]);
                console.log('Oops! Something went Wrong'+error);
                $('.dayremark_refresh_'+id).addClass('hide');
                $('.dayremark_update_'+id).removeClass('hide');
            }
        });
    });

    $('.deleteContent').on('click',function(){
        $('#deleteModal').modal('show');
        $('#btn_delete_dayremark').attr('data-id',$(this).data('id'));
    });

    $('#btn_delete_dayremark').on('click',function(){
        let id = ($(this).data('id'));
        console.log(id);
        let url = '{{domain_route("company.admin.dayremarks.ajaxdelete")}}';
        $('.dayremark_update_'+id).addClass('hide');
        $('.dayremark_refresh_'+id).removeClass('hide');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                "id":id,
            },
            success: function (data) {
                console.log('ajax successed');
                if(data.status==true){

                    alert('Dayremark Deleted Successfully');
                    if(data.exists==true){
                        window.location = window.location.href;
                    }else{
                        window.location = "{{domain_route('company.admin.dayremarks')}}";
                    }
                }
            },
            error:function(error){
                console.log('Oops! Something went Wrong'+error);
                $('.dayremark_refresh_'+id).addClass('hide');
                $('.dayremark_update_'+id).removeClass('hide');
            }
        });
    });
</script>
@endsection