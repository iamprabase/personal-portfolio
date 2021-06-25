<div class="col-xs-12">
  <h3 class="site-tital"> Plan and Modules (Control the features visibility)</h3>
</div>
<div class="info">
<div class="box-header">
	<div class="col-xs-2">
		<h5 class="pull-right"><b>Select Plan</b></h5>
	</div>
	<div class="col-xs-8">
		<select id="choosePlan" class="form-control select2" name="plan" action="{{route('app.setting.getPlanModules',[$clientSettings->company_id])}}">
			@foreach($plans as $p)
				<option @if($plan->id==$p->id) selected="selected" @endif value="{{$p->id}}">{{$p->name}} {{($p->custom!=0)?"(Custom)":""}}</option>
			@endforeach
			@if($plans->where('custom','!=',0)->count()==0)
				<option value="custom">Custom</option>
			@endif
		</select>
	</div>
	<div class="col-xs-2">
		<button class="btn btn-primary" id="changePlan" type="button" action="{{route('app.setting.changePlan',[$clientSettings->company_id])}}">Change Plan</button>
	</div>
</div>
<form id="moduleControl" action="{{route('app.setting.updateModule',[$clientSettings->company_id])}}">
	<table class="table table-striped">
		<tr style="background-color: #f16022;color: white;">
			<th>Modules</th>
			<th>
				<?php $i=0; ?>
				@foreach($mainmodules as $module)
					@if($module->value==1)
					<?php $i++; ?>
					@endif
				@endforeach
				<label class="switch">
					<input class="toggle-all-switches" type="checkbox" @if(count($mainmodules)==$i) checked="checked" @endif>
					<span class="slider round"></span>
				</label>
			</th>
		</tr>
	@foreach($mainmodules as $module)
		<tr>
			<td><b>{{$module->name}}</b></td>
			<td>
				<label class="switch">
					@if($module->field=="attendance")
						<input class="{{$module->field}}" type="checkbox"  checked="checked" disabled="disabled">
						<input name="{{$module->field}}"  type="checkbox"  checked="checked" class="hide">
					@else
			    		<input class="switches {{$module->field}}" name="{{$module->field}}"  type="checkbox" >
			    	@endif

				  <span class="slider round"></span>
				</label>
			</td>
		</tr>
	@endforeach
	</table>
	<div class="form-group moduleupdatekey hide" style="margin-left: 10px;">
		<?php $customPlan = $plans->where('custom','!=',0)->first(); ?>
		<div class="row">
			<div class="col-xs-3">
				<b>Plan Name</b>
			</div>
			<div class="col-xs-3">
				<input type="text" name="plan_id" value="{{isset($customPlan)?$customPlan->id:''}}" hidden>
				<input type="text" class="form-control" name="name" placeholder="Plan Name" value="{{(isset($customPlan))?$customPlan->name:''}}" required>
			</div>
		</div>
	</div>
	<div class="form-group moduleupdatekey hide" style="margin-left: 10px;">
		<div class="row">
			<div class="col-xs-3">
				<b>Plan Description</b>
			</div>
			<div class="col-xs-3">
				<input type="text" class="form-control" name="plan_description" placeholder="Plan Description" value="{{(isset($customPlan))?strip_tags($customPlan->description):''}}" required>
			</div>
		</div>
	</div>
	{{-- <div class="form-group moduleupdatekey hide" style="margin-left: 10px;">
		<div class="row">
			<div class="col-xs-3">
				<b>Users</b>
			</div>
			<div class="col-xs-3">
				<input type="text" class="form-control" name="users" placeholder="No. of Users" value="{{isset($customPlan)?$customPlan->users:''}}" required>
			</div>
		</div>
	</div> --}}

	{{-- <div class="form-group moduleupdatekey hide" style="margin-left: 10px;">
		<div class="row">
			<div class="col-xs-3">
				<b>Duration</b>
			</div>
			<div class="col-xs-3">
				<input type="text" class="form-control" name="duration" placeholder="Duration" value="{{isset($customPlan)?$customPlan->duration:''}}" required>
			</div>
			<div class="col-xs-3">
				@if(isset($customPlan))
				<select name="duration_in" class="form-control" required>
					<option @if($customPlan->duration_in=="Years") selected="selected @endif value="Years">Years</option>
					<option @if($customPlan->duration_in=="Months") selected="selected @endif value="Months">Months</option>
					<option @if($customPlan->duration_in=="Days") selected="selected @endif value="Days">Days</option>
				</select>
				@else
				<select name="duration_in" class="form-control" required>
					<option value="Years">Years</option>
					<option value="Months">Months</option>
					<option value="Days">Days</option>
				</select>
				@endif
			</div>
		</div>
	</div> --}}
	<button type="submit" class="btn btn-primary moduleupdatekey hide">Update</button>
</form>
</div>