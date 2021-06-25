
<tr>
    <td class="hidden"></td>
    <td>{{ $report->created_at->format('Y-m-d')}}</td>
    <td>{{ strtoupper($report->report_cat)}}</td>
    @if(isset($report->start_date) && isset($report->end_date))
    @if($report->start_date == $report->end_date)
    <td>{{getDeltaDateFormat($report->start_date)}}</td>
    @else
    <td>{{getDeltaDateFormat($report->start_date)}} to {{getDeltaDateFormat($report->end_date)}}</td>
    @endif
    @else
    <td>{{$report->date_range}}</td>
    @endif
    <td>
    <a href="{{ $report->download_link}}">
        <i class="fa fa-download" aria-hidden="true"></i>
    </a>
    </td>
</tr>
