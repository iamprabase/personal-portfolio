@extends('layouts.company')
@section('title', 'Visit Details')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
  <style type="text/css">
    img{
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }    

    .modal#myModal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1500; /* Sit on top */
      padding-top: 100px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    #myModal .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;  
      -webkit-animation-name: zoom;
      -webkit-animation-duration: 0.6s;
      animation-name: zoom;
      animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
      from {-webkit-transform:scale(0)} 
      to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
      from {transform:scale(0)} 
      to {transform:scale(1)}
    }

    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }

    .close:hover,
    .close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }
    }
    .imgdiv{
      max-width: 200px;
      max-height: inherit;
    }

    .delete, .edit{
      font-size: 15px;
    }
    .fa-edit, .fa-trash-o{
      padding-left: 5px;
    }

    .btn-warning{
      margin-right: 2px;
      color: #fff;
      background-color: #ec971f;
      border-color: #d58512;
    }

    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }

    .order-dtl-bg{
      margin-bottom: 20px;
    }

    .detail-box{
      margin-bottom: 10px;
    }

    td{
      width: 70%;
    }

    .btn-sm{
      font-size: 14px;
    }

    .fa-map-marker{
      color:#098309;
    }
  </style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
            <div class="page-action pull-right">
              {!!$action!!}
            </div>
          </div>
          <div class="box-header with-border">
            <h3 class="box-title">Visit Details</h3>
          </div>
          <!-- /.box-header -->
          @include('company.employees.partial_show.visit_detail_partial', [
            'action' => $action,
            'checkin' => $checkin, 
            'checkout' => $checkout, 
            'date' => $date, 
            'employee_name' => $employee_name, 
            'empVisits' => $empVisits, 
            'total_duration' => $total_duration, 
          ])
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>
    <iframe hidden id="printf" name="printf" src="{{domain_route('company.admin.employee.empClientVisitDetailPrint', ['id' => $employee_id, 'date' => $date])}}" frameborder="0"></iframe>
    {{-- <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
      data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form method="post" class="remove-record-model">
            {{method_field('delete')}}
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
              <input type="hidden" name="expense_id" id="c_id" value="">
              <input type="text" id="accountType" name="account_type" hidden />
              <input type="hidden" name="prev_url" value="{{URL::previous()}}">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div> --}}
  </section>
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>
@endsection
@section('scripts')
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
  // $(function () {
  //     $('#delete').on('show.bs.modal', function (event) {
  //       var button = $(event.relatedTarget)
  //       var mid = button.data('mid')
  //       var url = button.data('url');
  //       $(".remove-record-model").attr("action", url);
  //       var modal = $(this)
  //       modal.find('.modal-body #m_id').val(mid);
  //     });

  //     $('.delete').on('click',function(){
  //       $('#accountType').val($(this).attr('data-type'));
  //     });
  // });
  var modal = document.getElementById("myModal");
  var modalImg = document.getElementById("img01");

  $('.display-imglists').on('click',function(){
    modal.style.display = "block";
    modalImg.src = this.src;
  });

  $('.close').on('click',function(){
    modal.style.display = "none";
  });
  function print() {
    window.frames["printf"].focus();
    window.frames["printf"].print();
    // var print_div = document.getElementById("printArea");
    // var print_area = window.open();
    // print_area.document.write(print_div.innerHTML);
    // print_area.document.close();
    // print_area.focus();
    // print_area.print();
    // print_area.close();
  }
</script>
@endsection