@extends('layouts.company')
@section('title', 'Day Remarks')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
<style>
  img{
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }    

    .modal {
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

    .modal-content {
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
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Day Remarks</h3>
          <div class="page-action pull-right">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
              class="fa fa-arrow-left"></i> Back</a>
            </div>
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
                    <td>{{ getEmployee($getDayRemarkInstance->employee_id)['name']}}</td>
                  </tr>
                  <tr>
                    <th scope="row"> Date</th>
                    <td>                    
                      {{getDeltaDate($getDayRemarkInstance->remark_date)}}
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"> Time</th>
                    <td>{{ date("H:i a", strtotime($getDayRemarkInstance->remark_datetime))}}</td>
                  </tr>
                  <tr>
                    <th scope="row"> Remark</th>
                    <td>{!! $getDayRemarkInstance->remarks !!}</td>
                  </tr>
                  </tr>
                </tbody>
              </table>
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
  <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
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
  </script>
@endsection