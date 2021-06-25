{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>--}}
{{--<meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
{{--<head>--}}
{{--    <title>{{$pageTitle}}</title>--}}
{{--    <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">--}}
{{--    <style>--}}
{{--        * {--}}
{{--            margin: 5px;--}}
{{--            font-family: Noto Sans, sans-serif;--}}
{{--        }--}}

{{--        .text-center {--}}
{{--            font-weight: 600;--}}
{{--            color: #1a2b4c !important;--}}
{{--        }--}}

{{--        table td,--}}
{{--        table th {--}}
{{--            -webkit-box-sizing: content-box;--}}
{{--            box-sizing: content-box;--}}
{{--            font-size: 12px;--}}
{{--            text-align: left;--}}
{{--            width: max-content;--}}
{{--        }--}}

{{--        th {--}}
{{--            background: #1a2b4c !important;--}}
{{--            color: #fff !important;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}

{{--<body>--}}

{{--<h2 class="text-center">{{$pageTitle}}</h2>--}}

{{--<section class="content">--}}
{{--    <table id="order" class="table table-bordered table-striped">--}}
{{--        <thead style="vertical-align: middle;">--}}
{{--        <tr>--}}
{{--            @foreach($main_data as $key => $data)--}}
{{--                @if($key == 0)--}}
{{--                    <th>ID</th>--}}
{{--                    @foreach($data as $key =>$value)--}}

{{--                        @if($key == 'user_id')--}}
{{--                            <th>{{'Created By'}}</th>--}}
{{--                        @else--}}
{{--                            <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        </tr>--}}
{{--        </thead>--}}
{{--        <tbody>--}}
{{--        @foreach($main_data as $key => $data)--}}
{{--            <tr>--}}
{{--                <td>{{$key + 1}}</td>--}}
{{--                @foreach($data as $value)--}}
{{--                    @if(!is_array($value))--}}
{{--                        <td>{!! $value !!} </td>--}}
{{--                    @else--}}
{{--                        <td>--}}
{{--                            @foreach($value as $unit)--}}
{{--                                @if(!$loop->last)--}}
{{--                                    {!! $unit !!},--}}
{{--                                @else--}}
{{--                                    {!! $unit !!}--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                    @endif--}}
{{--                @endforeach--}}

{{--            </tr>--}}
{{--        @endforeach--}}
{{--        </tbody>--}}
{{--    </table>--}}
{{--</section>--}}

{{--</body>--}}
{{--</html>--}}

        <!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<head>
    <title>{{$pageTitle}}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">
    <style>
        * {
            margin: 5px;
            font-family: Noto Sans, sans-serif;
        }

        .text-center {
            font-weight: 600;
            color: #1a2b4c !important;
        }

        table td,
        table th {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            font-size: 12px;
            text-align: left;
            width: max-content;
        }

        th {
            background: #1a2b4c !important;
            color: #fff !important;
        }

        img {
            height: 50px !important;
            width: 50px !important;
        }
    </style>
</head>

<body>

<h2 class="text-center">{{$pageTitle}}</h2>

<section class="content">
    <table id="order" class="table table-bordered table-striped">
        <thead style="vertical-align: middle;">
        <tr>
            @foreach($columns as $column)
                <th>{{$column}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @php $num=1 @endphp
        @foreach($getExportData as $data)
            <tr>
                @foreach($properties as $property)
                    @if(!is_array($data->$property))
                        <td>{!! $data->$property !!}</td>
                    @else
                        <td>
                            @foreach($data->$property as $option)
                                @if($loop->last)
                                    {!! $option !!}
                                @else
                                    {!! $option!!} ,<br>
                                @endif
                            @endforeach
                        </td>
                    @endif
                @endforeach
            </tr>
            @php ++$num @endphp
        @endforeach
        </tbody>
    </table>
</section>

</body>
</html>