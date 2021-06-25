@extends('layouts.company')

@section('title','Notification')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <style>
        .notification-sec {
            border-bottom: 1px solid #d2d6de;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .notification-sec a {
            text-decoration: none;
        }

        .notification-sec a:hover {
            text-decoration: none;
        }

        .user-block01 {
            margin-bottom: 15px;
        }

        .user-block01 img {
            width: 40px;
            height: 40px;
            float: left;
        }

        .img-bordered-sm1 {
            width: 40px;
            height: 40px;
        }

        .img-bordered-sm1 {
            border: 2px solid #d2d6de;
            padding: 2px;
        }

        .img-circle {
            border-radius: 50%;
        }

        .user-block01, .username1 {
            font-size: 16px;
            font-weight: normal;

        }

        .user-block01 {
            display: block;
            margin-left: 0px;
            border-bottom: 1px solid #ccc;
        }

        .description1 {
            display: block;
            /*margin-left: 60px;*/
            color: #999;
            font-size: 13px;
        }

        .username1 {
            display: block;
            /*margin-left: 60px;*/
        }

        .pagination > li > span {
            padding: 10px 12px;
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
                    </div><br/>
                @endif
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Notifications List</h3>
                        <span id="leaveexports" class="pull-right"> {{ $notifications->links() }}</span>
                    </div>
                    <div class="box-body">

                        @if($notifications)
                            <div>
                                @foreach($notifications as $notification)
                                    <div class="notification-sec">
                                        <div class="media">
                                            <div class="media-left">
                                                @if($notification->image)
                                                    <img class="img-circle img-bordered-sm1 media-object"
                                                         src="{{ URL::asset('/cms'.$notification->image_path) }}"
                                                         alt="user image">
                                                @else
                                                    @if($notification->gender='Male')
                                                        <img class="img-circle img-bordered-sm1 media-object"
                                                             src="{{ URL::asset('cms/storage/app/public/uploads/default_m.png') }}"
                                                             alt="user image">
                                                    @else
                                                        <img class="img-circle img-bordered-sm1 media-object"
                                                             src="{{ URL::asset('/storage/uploads/default_f.png') }}"
                                                             alt="user image">
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="media-body">
                                              @if($notification->data_type!='remark')
                                                <a href="{{ empty($notification->data_type)?'#':domain_route('company.admin.'.$notification->data_type.'.show',[json_decode($notification->data)->id ]) }}">
                                                    <h4 class="media-heading">{{ ($notification->title)?$notification->title:'' }}</h4>
                                                    <span class="username1">{{ $notification->name }}</span>
                                                    <p class="description1">{{ ($notification->created_at)?date('d M Y', strtotime($notification->created_at)):'' }}</p>
                                                    <p>{{ ($notification->description)?$notification->description:'' }}</p>
                                                </a>
                                              @else
                                                <a href="{{ empty($notification->data_type) || (empty($notification->data)) ?'#':domain_route('company.admin.'.$notification->data_type.'.show',[json_decode($notification->data)->id ]) }}">
                                                    <h4 class="media-heading">{{ ($notification->title)?$notification->title:'' }}</h4>
                                                    <span class="username1">{{ $notification->name }}</span>
                                                    <p class="description1">{{ ($notification->created_at)?date('d M Y', strtotime($notification->created_at)):'' }}</p>
                                                    <p>{{ ($notification->description)?$notification->description:'' }}</p>
                                                </a>
                                              @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div style="float: right">
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <input type="hidden" name="leave_id" id="leave_id" value="">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="id">Remark</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark"
                                          cols="50" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Status</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Pending" selected="selected">Pending</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="deleteContent">
                        Are you Sure you want to delete <span class="dname"></span> ? <span
                                class="hidden did"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn actionBtn" data-dismiss="modal">
                            <span id="footer_action_button" class='glyphicon'> </span>
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>


@endsection