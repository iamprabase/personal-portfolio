@extends('layouts.company')
@section('title', 'Show Product')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/zoomImage/zoomer.css')}}">
<style type="text/css">
  .box-default {
    border-radius: 10px;
  }

  .profile-user-img {
    margin: 0 auto;
    width: 180px;
    height: 180px;
    padding: 3px;
    border: 3px solid #0b7676;
    box-shadow: none;
  }

  .table-responsive.show-tab {
    box-shadow: none;
  }

  .btn-danger:hover {
    color: #ac2925 !important;
  }

  .table-striped>tbody>tr:nth-child(even)>td,
  .table-striped>tbody>tr:nth-child(even)>th {
    background-color: #e8eaea;
  }

  .table-striped>tbody>tr:nth-child(odd)>td,
  .table-striped>tbody>tr:nth-child(odd)>th {
    background-color: #edf4f4;
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

  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
  }
</style>
@endsection

@section('content')
<section class="content">
  <div class="box box-default">
    <div class="row">
      <div class=" col-xs-12">

        @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
        @endif

        @if (\Session::has('error'))
        <div class="alert alert-error">
          <p>{{ \Session::get('error') }}</p>
        </div><br />
        @endif

        @if (\Session::has('warning'))
        <div class="alert alert-warning">
          <p>{{ \Session::get('warning') }}</p>
        </div><br />
        @endif

        <div class="box-header with-border">
          <a href="{{ domain_route('company.admin.product') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
          <div class="page-action pull-right">
            {!!$action!!}
          </div>
        </div>
        <div class="box-header with-border">
          <h3 class="box-title">Product Detail</h3>
          <div class="page-action pull-right">
            {{-- {!!$action!!}
            <a href="{{ domain_route('company.admin.product') }}" class="btn btn-default btn-sm"> <i
                class="fa fa-arrow-left"></i> Back</a> --}}
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-3">

              @if(!empty($product->image_path))
              <img src="{{ URL:: asset('cms/'.$product->image_path) }}"
                class="profile-user-img img-responsive display-imglists" alt="User Image">
              @else
              <img src="{{ URL::asset('cms/storage/app/public/uploads/defaultprod.jpg') }}"
                class="profile-user-img img-responsive" alt="User Image">
              @endif


            </div>
            <div class="col-xs-9">
              <div class="table-responsive show-tab">
                @if($product->variant_flag==0)
                  <table class="table table-bordered table-striped">
                    <colgroup>
                      <col class="col-xs-2">
                      <col class="col-xs-7">
                    </colgroup>
                    <tbody>
                      <tr>
                        <th scope="row"> Product</th>
                        <td>{{ $product->product_name }}
                          @if(getUnitName($product->first()->unit)!="")({{getUnitName($product->unit)}})@endif</td>
                      </tr>
                      <tr>
                      <th scope="row">Product Code</th>
                        <td>
                          {{ empty($product->product_code)?'':$product->product_code}}
                        </td>
                    </tr>
                      <tr>
                        <th scope="row"> Brand</th>
                        <td>
                          @if(!empty($product->brand))
                                @if(getBrandName($product->brand)!=""){{ getBrandName($product->brand) }}@endif
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"> Category</th>
                        <td>
                          @if(!empty($product->category_id))
                                {{ (getCategory($product->category_id)['status'] == 'Active')? getCategory($product->category_id)['name']:'NA' }}
                          @endif
                        </td>
                      </tr>
                      <tr @if(config('settings.order_with_amt')==1) hidden @endif>
                        <th scope="row"> Rate{{"(".config('settings.currency_symbol').")"}}</th>
                        <td>{{ ($product->mrp)?$product->mrp:'NA' }}</td>
                      </tr>
                      <tr>
                        <th scope="row"> Status</th>
                        <td>{{ ($product->status)?$product->status:'NA' }}</td>
                      </tr>
                      <tr>
                        <th scope="row"> Marked as Starred</th>
                        <td>{{ ($product->star_product)?"Yes":"No" }}
                      </tr>
                      {{-- @if(getClientSetting()->product_level_tax==1) --}}
                      <tr>
                        <th scope="row"> Tax</th>
                        <td>
                          {{ !empty($taxType)?implode(',' ,$taxType):null }}
                        </td>
                      </tr>
                      {{-- @endif --}}
                      <tr>
                        <th scope="row"> Unit</th>
                        <td>
                          {{getUnitName($product->unit)}}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"> Unit Conversions</th>
                        <td>
                            {{$select_option}}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"> Short Description</th>
                        <td>{{ (isset($product->short_desc)? $product->short_desc:'') }}</td>
                      </tr>
                    </tbody>
                  </table>
                @else
                <table class="table table-bordered table-striped">
                  <colgroup>
                    <col class="col-xs-2">
                    <col class="col-xs-7">
                  </colgroup>
                  <tbody>
                    <tr>
                      <th scope="row"> Name</th>
                        <td>{{ $product->product_name }} @if(getUnitName($product->unit)!="")({{getUnitName($product->unit)}})@endif
                        </td>
                    </tr>
                    <tr>
                      <th scope="row">Product Code</th>
                        <td>
                          {{ empty($product->product_code)?'':$product->product_code}}
                        </td>
                    </tr>
                    <tr>
                      <th scope="row"> Brand</th>
                      <td>
                        @if(!empty($product->brand))
                            @if(getBrandName($product->brand)!=""){{ getBrandName($product->brand) }}@endif
                        @endif
                        {{-- @if(getBrandName($product->brand)!=""){{ getBrandName($product->brand) }}@endif --}}
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"> Category</th>
                      <td>
                        @if(!empty($product->brand))
                            {{ (getCategory($product->category_id)['status'] == 'Active')? getCategory($product->category_id)['name']:'NA' }}
                        @endif
                        {{-- {{ (getCategory($product->category_id)['status'] == 'Active')? getCategory($product->category_id)['name']:'NA' }} --}}
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"> Status</th>
                      <td>{{ ($product->status)?$product->status:'NA' }}</td>
                    </tr>
                    <tr>
                      <th scope="row"> Marked as Starred</th>
                      <td>{{ ($product->star_product)?"Yes":"No" }}
                    </tr>
                    {{-- @if(getClientSetting()->product_level_tax==1) --}}
                    <tr>
                      <th scope="row"> Tax</th>
                      <td>
                        {{ !empty($taxType)?implode(',' ,$taxType):null }}
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"> Unit Conversions</th>
                      <td>
                        {{$select_option}}
                      </td>
                    </tr>
                    {{-- @endif --}}
                  </tbody>
                </table>
                @endif
              </div>
            </div>
          </div>
        </div>
        <!-- /.box-body -->

        @if($product->variant_flag!=0)
        <div class="col-xs-12">

          <div class="table-responsive show-tab" style="margin-bottom: 10px;">
            <table class="table table-bordered table-striped">
              <colgroup>
                <col class="col-xs-2">
                <col class="col-xs-2">
                <col class="col-xs-7">
              </colgroup>
              <thead>
                <tr>
                  <th scope="row"> Variant</th>
                  @if(getClientSetting()->var_colors==1)<th scope="row"> Variant Attributes</th> @endif
                  <th scope="row" @if(config('settings.order_with_amt')==1) hidden @endif> Rate{{"(".config('settings.currency_symbol').")"}}</th>
                  <th scope="row"> Short Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($productVariants as $product_p)
                <tr>
                  <td>{{ ($product_p->variant)?$product_p->variant:'NA' }} ({{getUnitName($product_p->unit)}})</td>
                  @if(getClientSetting()->var_colors==1)<td>{{($product_p->colors->count()>0)?implode(',',$product_p->colors->pluck('name')->toArray()):null}}</td>@endif
                  <td @if(config('settings.order_with_amt')==1) hidden @endif>{{ ($product_p->mrp)?$product_p->mrp:'NA' }}</td>
                  <td>{{ (isset($product_p->short_desc)? $product_p->short_desc:'') }}</td>
                  <td>@if($product->count()>1) @if(getOrderDetails($product_p->id)==0 && Auth::user()->can('product-delete'))<a
                      class="btn btn-danger btn-sm delete" data-pid="" data-vid="{{ $product_p->id }}"
                      data-url="{{ domain_route('company.admin.product.destroy', [$product_p->id]) }}"
                      data-toggle="modal" data-target="#deletevar" style="background: none; border-color: inherit">
                      <i class="fa fa-trash-o"></i></a>@endif @endif</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endif

        <div class="col-xs-12">

          <div class="table-responsive show-tab" style="margin-bottom: 10px;">
            <table class="table table-bordered table-striped">
              <colgroup>
                <col class="col-xs-2">
                <col class="col-xs-7">
              </colgroup>
              <thead>
                <tr>
                  <th scope="row"> Description</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{!! ($product->details)?($product->details):'NA' !!}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box -->
  </div>
  <!-- Modal -->

  <div id="myModal" class="modal custommodal">
    <span class="close zoom-close">&times;</span>
    <img class="modal-content zoom-modal-content" id="img01">
    <div id="caption"></div>
  </div>

  <div class="modal modal-default fade" id="deletevar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
            <input type="hidden" name="id" id="productId">
            <input type="hidden" name="productVariantId" id="productVariantId">
            <input type="hidden" name="prev_url" id="{{URL::previous()}}">
            <input type="hidden" name="from_view" value="true">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endsection

  @section('scripts')
  <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/zoomImage/zoomer.js')}}"></script>
  <script>
    $(function () {
      $('#deletevar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var url = button.data('url');
        var pid = button.data('pid');
        var vid = button.data('vid');
        $(".remove-record-model").attr("action", url);
        var modal = $(this);
        $('#productId').val(pid);
        $('#productVariantId').val(vid);
      });
    });
  </script>

  @endsection