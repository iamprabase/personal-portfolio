@extends('layouts.company')
@section('title', 'Party types')
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  #mainBox .tree, .tree ul {
    margin: 0;
    padding: 0;
    list-style: none
  }
  #mainBox .tree ul {
    margin-left: 1em;
    position: relative
  }
  #mainBox .tree ul ul {
    margin-left: .5em
  }
  #mainBox .tree ul:before {
    content: "";
    display: block;
    width: 0;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    border-left: 1px solid
  }
  #mainBox .tree li {
    margin: 0;
    padding: 0 1em;
    line-height: 2em;
    color: #369;
    font-weight: 700;
    position: relative
  }
  #mainBox .tree ul li:before {
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
  #mainBox .tree ul li:last-child:before {
    background: #fff;
    height: auto;
    top: 1em;
    bottom: 0
  }
  #mainBox .indicator {
    margin-right: 5px;
  }
  #mainBox .tree li a {
    text-decoration: none;
    color: #369;
  }
  #mainBox .tree li button, .tree li button:active, .tree li button:focus {
    text-decoration: none;
    color: #369;
    border: none;
    background: transparent;
    margin: 0px 0px 0px 0px;
    padding: 0px 0px 0px 0px;
    outline: 0;
  }
  #tree1 li .btn{
    padding: 10px 1px 10px 5px;
  }
  #tree2 li .btn{
    padding: 10px 1px 10px 5px;
  }

  #mainBox .button-red i{
    display: block!important;
    color: red;
  }
  #mainBox .button-red:active{
    -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    -moz-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
  }
  #mainBox .button-blue i{
    display: block!important;
    color: blue;
  }
  #mainBox .button-blue:active{
    -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    -moz-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
  }
  .btn-custom-primary{
    color:white;
    background-color:#337ab7;
    border-color: #337ab7;
    border-radius: 10px; 
  }
  .btn-custom-primary:hover{
    color:white;
    background-color:#175a93;
    border-color: #337ab7;
    border-radius: 10px;
  }
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
      @endif

      @if (\Session::has('error'))
      <div class="alert alert-warning">
        <p>{{ \Session::get('error') }}</p>
      </div><br />
      @endif
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Party Types</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" id="mainBox">
          <div class="row">
            <div class="col-xs-6">
              <ul id="tree1">
              @foreach($partytypes as $partytype)
                <li>
                  {{ $partytype->name }} @if($partytype->childs->count() == 0) <a data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue @if(!(Auth::user()->can('partytype-update')))hide @endif"><i class="fa fa-edit"></i></a>
                  <span area-id="{{$partytype->id}}" destroy-url="{{domain_route('company.admin.partytype.destroy',[$partytype->id])}}" class="btn btn-sm button-red @if(!(Auth::user()->can('partytype-delete')))hide @endif"><i class="fa fa-trash" ></i></span>
                  @else   
                  <p data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue @if(!(Auth::user()->can('partytype-update')))hide @endif"><i class="fa fa-edit" ></i></p>
                  @endif
                  @if(count($partytype->childs))
                    @include('company.partytypes.managePartyChild',['childs' => $partytype->childs])
                  @endif
                </li>
              @endforeach
              </ul>
            </div>
            <div class="col-xs-6 @if(!(Auth::user()->can('partytype-create')))hide @endif">
              <h3>Add New Party Type</h3>

              {!! Form::open(array('url' => url(domain_route("company.admin.partytype.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true, 'autocomplete'=>"off")) !!}

              <input type="hidden" name="company_id" value="{{config('settings.company_id')}}">

              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                {!! Form::label('Name:') !!}
                {!! Form::text('name', old('name'), ['class'=>'form-control', 'placeholder'=>'Enter Name']) !!}
                <span class="text-danger">{{ $errors->first('name') }}</span>
              </div>

              <div class="form-group {{ $errors->has('sname') ? 'has-error' : '' }}">
                {!! Form::label('Short Name:') !!}
                {!! Form::text('short_name', old('short_name'), ['class'=>'form-control', 'placeholder'=>'Enter Short Name','maxlength'=>"3"]) !!}
                <span class="text-danger">{{ $errors->first('sname') }}</span>
              </div>

              <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                {!! Form::label('Superior:') !!}
                {{-- {!! Form::select('parent_id',$partytypes, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
                <select id="select_party_types" name="parent_id" class="form-control" placeholder="Select Party Type">
                  <option>Select Party Type</option>
                  @foreach($partytypes as $party)
                    <option value="{{$party['id']}}">{{$party['name']}}</option>
                  @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
              </div>

              <div class="form-group">
                <input style="height: 12px;" type="checkbox" name="display_status" checked="checked" />leave tick to display or untick to hide in app
              </div>

              <div class="form-group">
                <button id="btnAddParty" class="btn btn-custom-primary">Add New</button>
              </div>

              {!! Form::close() !!}

            </div>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <!-- Modal Section -->
    <div class="modal fade" id="modalEditPartyName" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="editPartyName">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Update Party Type
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input id="party_edit_company_id" type="hidden" name="company_id" value="{{config('settings.company_id')}}">
            <label>Name</label>
            <input id="party_type_nameonly" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_nameonly" class="form-control" type="text" name="short_name">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
      </form>
    </div>

    <div class="modal fade" id="modalEditPartyType" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="editPartyType">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Update Party Type
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input id="party_edit_company_id" type="hidden" name="company_id" value="{{config('settings.company_id')}}">
            <label>Name</label>
            <input id="party_type_name" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_name" class="form-control" type="text" name="short_name">
             <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
              {!! Form::label('Superior:') !!}
              {{-- {!! Form::select('parent_id',$partytypes, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
              <select id="party_parent" name="parent_id" class="form-control" placeholder="Select Party Type">
                <option>Select Party Type</option>
                @foreach($partytypes as $party)
                  <option value="{{$party['id']}}">{{$party['name']}}</option>
                @endforeach
              </select>
              <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div>

          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
      </form>
    </div>

    <div class="modal fade" id="modalDeletePartyType" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="delPartyType">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Are you sure you want to delete this party type?
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </h4>
          </div>
          <div class="modal-body">
            <input id="del_company_id" type="hidden" name="company_id" value="{{config('settings.company_id')}}">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-warning">Yes,Delete</button>
          </div>
        </div>
      </div>
      </form>
    </div>
  <!-- End Modal Section -->


</section>

@endsection

@section('scripts')

<script>
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

  $('#tree1').treed();
  //Party Type Section
  $('#tree1').on('click','span', function(){
    $('#modalDeletePartyType').modal('show');
    $('#delPartyType').attr('action',$(this).attr('destroy-url'));
  });
  $('#tree1').on('click','a', function(){
    var superior_id =  $(this).attr('superior-id');
    var party_id = $(this).attr('data-id');
    $('#modalEditPartyType').modal('show');
    $('#editPartyType').attr('action',$(this).attr('edit-url'));
    $('#party_type_name').val($(this).attr('data-name'));
    $('#party_type_short_name').val($(this).attr('data-short-name'));
    $('#party_parent option').removeAttr('selected');
    if($(this).attr('data-ticked')==0){
      $('#partyType_display_status').attr('checked',false);
    }else{
      $('#partyType_display_status').attr('checked',true);
    }
    var url = "{{domain_route('company.admin.partytype.getPartyTypeList')}}";
    var company_id = $('#party_edit_company_id').val();
    var myId = $(this).attr('data-id');
    $.ajax({
      url: url,
      type: "GET",
      data:
      {
        'company_id':company_id,
        'myId':myId,
      },
      success: function (data) {
        $('#modalEditPartyName').modal('hide');
        $('#party_parent').empty();
        $('<option></option>').text('Select Party Type').appendTo('#party_parent');
        $.each(data['partytypes'],function(i,v){
          if(v.id == superior_id){
            $('<option selected></option>').val(v.id).text(v.name).appendTo('#party_parent');
          }else{
            $('<option></option>').val(v.id).text(v.name).appendTo('#party_parent');
          }
        });
      }
    });
  });
  $('#tree1').on('click','p', function(){
    var superior_id =  $(this).attr('superior-id');
    var party_id = $(this).attr('data-id');
    $('#modalEditPartyName').modal('show');
    $('#editPartyName').attr('action',$(this).attr('edit-url'));
    $('#party_type_nameonly').val($(this).attr('data-name'));
    $('#party_type_short_nameonly').val($(this).attr('data-short-name'));
    if($(this).attr('data-ticked')==0){
      $('#tickedSalemanAllowed').attr('checked',false);
    }else{
      $('#tickedSalemanAllowed').attr('checked',true);
    }
  });
  $('#editPartyName').on('submit',function(event){
    event.preventDefault();
    var url = $(this).attr('action');
    var company_id = $('#party_edit_company_id').val();
    var party_type = $('#party_type_nameonly').val();
    var short_name = $('#party_type_short_nameonly').val();
    if($('#tickedSalemanAllowed').prop('checked')==true){
      display_status=1;
    }else{
      display_status=0;
    }
    $.ajax({
      url: url,
      type: "POST",
      data:
      {
        'company_id':company_id,
        'party_type':party_type,
        'party_type_short_name':short_name,
        'display_status':display_status,
      },
      success: function (data) {
        $('#modalEditPartyName').modal('hide');
        $('#tree1').html(data['tree']);
        $('#tree1').treed();
        $('#select_party_types').empty();
        $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
        $.each(data['partytypes'],function(i,v){
          $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
        });
      }
    });
  });

  $('#editPartyType').on('submit',function(event){
    event.preventDefault();
    var url = $(this).attr('action');
    var company_id = $('#party_edit_company_id').val();
    var party_type = $('#party_type_name').val();
    var party_type_short_name = $('#party_type_short_name').val();
    var party_parent = $('#party_parent').val();
    var display_status = $('#partyType_display_status').val();
    if($('#partyType_display_status').prop('checked')==true){
      display_status=1;
    }else{
      display_status=0;
    }
    $.ajax({
      url: url,
      type: "POST",
      data:
      {
        'company_id':company_id,
        'party_type':party_type,
        'party_parent':party_parent,
        'party_type_short_name':party_type_short_name,
        'display_status':display_status,
      },
      success: function (data) {
        $('#modalEditPartyType').modal('hide');
        $('#tree1').html(data['tree']);
        $('#tree1').treed();
        $('#select_party_types').empty();
        $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
        $.each(data['partytypes'],function(i,v){
          $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
        });
      }
    });
  });
  $('#delPartyType').on('submit',function(event){
    event.preventDefault();
    var url = $(this).attr('action');
    var company_id = $('#del_company_id').val();
    $.ajax({
      url: url,
      type: "POST",
      data:
      {
        'company_id':company_id,
      },
      success: function (data) {
        $('#modalDeletePartyType').modal('hide');
        if(data.status == false){
          alert(data.message);
        }else{
          $('#tree1').html(data['tree']);
          $('#tree1').treed();
          $('#select_party_types').empty();
          $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
          $.each(data['partytypes'],function(i,v){
            $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
          });
          alert("Party Type Deleted Successfully");
        }
      }
    });
  });

</script>

@endsection