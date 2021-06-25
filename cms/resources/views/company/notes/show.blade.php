@extends('layouts.company')
@section('title', 'Note Details')
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

    /* @-webkit-keyframes zoom {
      from {-webkit-transform:scale(0)} 
      to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
      from {transform:scale(0)} 
      to {transform:scale(1)}
    } */

    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #000;
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

    .delete, .edit{
      font-size: 15px !important;
    }
    .fa-edit, .fa-trash-o{
      padding-left: 5px;
    }
    .btn-warning{
      margin-right: 2px !important;
      color: #fff!important;
      background-color: #ec971f!important;
      border-color: #d58512!important;
    }
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <div class="page-action pull-left">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>
          <div class="page-action pull-right">
          {!!$action!!}
          </div>
        </div>
        <div class="box-header with-border">
          <h3 class="box-title">Note Details</h3>
          
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <colgroup>
                  <col class="col-xs-2">
                  <col class="col-xs-7">
                </colgroup>
                <tbody>
                  <tr>
                    <th scope="row"> Party Name</th>
                    <td>{{ getClient($note->client_id)['company_name']}}</td>
                  </tr>
                  <tr>
                    <th scope="row"> Salesman</th>
                    <td>
                      @if($note->employee_id == 0)
                      {{ Auth::user()->name.' (Admin)'}}
                      @else
                      {{ $note->employee_name}}
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"> Date</th>
                    <td>              
                      {{getDeltaDate(date('Y-m-d', strtotime($note->created_at)))}}
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"> Time</th>
                    <td>{{ date("h:i a", strtotime($note->created_at))}}</td>
                  </tr>
                  <tr>
                    <th scope="row"> Notes</th>
                    <td>{!! $note->remark !!}</td>
                  </tr>
                  @if(isset($note->audio_note))
                  <tr>
                    <th scope="row"> Voice Note</th>
                    <td><audio controls><source src="{{ URL::asset('cms'.$note->audio_note) }}" type="audio/mpeg"></audio></td>
                  </tr>
                  @endif
                  @if($images->count()!=0)
                  <tr>
                    <th scope="row"> Picture</th>
                    <td>
                      @foreach($images as $image)
                        <div class="col-xs-4">
                          <div class="imagePreview imageExistsPreview"
                            style="background-position: center center;background-size: contain;background-repeat: no-repeat;">
                            @if(isset($image->image_path))
                            <img class="img-responsive display-imglists" @if(isset($image->image_path))
                            src="{{ URL::asset('cms'.$image->image_path) }}"
                            @endif alt="Picture Displays here" style="max-height: 500px;"/>
                            @else
                            <span class="pull-right">N/A</span>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">

    <div class="modal-dialog" role="document">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>

          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>

        </div>

        <form method="post" class="remove-record-model">

          {{method_field('delete')}}

          {{csrf_field()}}

          <div class="modal-body">

            <p class="text-center">

              Are you sure you want to delete this?

            </p>

            <input type="hidden" name="client_id" id="c_id" value="">
            <input type="hidden" name="prev_url" value="{{URL::previous()}}">

          </div>

          <div class="modal-footer">

            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>

          </div>

        </form>

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