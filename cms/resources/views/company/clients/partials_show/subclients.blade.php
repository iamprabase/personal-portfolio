<div class="box">
	<div class="box-body">
	<table id="{{$key}}client" class="table table-bcollectioned table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Superior</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if($countArray==0)
				<tr>
					<td>{{$value['company_name'][0]}}</td>
					<td>{{$value['superior'][0]}}</td>
					<td>Update/Delete</td>			
				</tr>
			@else
				@for($i=0;$i<$countArray;$i++)
				<tr>
					<td>{{$value['company_name'][$i]}}</td>
					<td>{{$value['superior'][$i]}}</td>
					<td>Update/Delete</td>			
				</tr>
				@endfor
			@endif
		</tbody>
	</table>
	</div>
</div>