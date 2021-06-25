<div class="box-body">
	<form id="UpdateAccountingDetail">
		<div class="row">
		    <div class="col-xs-12">
		        @if(Auth::user()->can('party-update'))
		          <span id="ActivateAccountingEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
		          <span id="ActivateAccountingCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
		          <span id="ActivateAccountingUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
		        @endif
		    </div>
			<div class="col-xs-6">
				<div class="media left-list bottom-border">
				  <div class="media-left">
				    <i class="fa fa-money icon-size"></i>
				  </div>
				  <div class="media-body"><h4 class="media-heading">Opening Balance</h4>
				    <p class="text-display" id="c_opening_balance">{{ ($client->opening_balance)?$client->opening_balance:'N/A' }}</p>
				    <p class="text-form" hidden><input name="opening_balance" class="form-control" type="text" value="{{ ($client->opening_balance)?$client->opening_balance:'' }}" /></p>
				  </div>
				</div>
			</div>

			<div class="col-xs-6">
				<div class="media left-list bottom-border">
				  <div class="media-left">
				    <i class="fa fa-money icon-size"></i>
				  </div>
				  <div class="media-body"><h4 class="media-heading">Credit Limit</h4>
				    <p class="text-display" id="c_credit_limit">{{ ($client->credit_limit)?$client->credit_limit:'N/A' }}</p>
				    <p class="text-form" hidden><input name="credit_limit" class="form-control" type="text" value="{{ ($client->credit_limit)?$client->credit_limit:'' }}" /></p>
				  </div>
				</div>
			</div>
			
			@if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
			<div class="col-xs-6">
				<div class="media left-list bottom-border">
				  <div class="media-left">
				    <i class="fa fa-money icon-size"></i>
				  </div>
				  <div class="media-body"><h4 class="media-heading">Credit Days</h4>
				    <p class="text-display" id="c_credit_days">{{ ($client->credit_days)?$client->credit_days:'N/A' }}</p>
				    <p class="text-form" hidden><input name="credit_days" class="form-control" type="text" value="{{ ($client->credit_days)?$client->credit_days:'' }}" /></p>
				  </div>
				</div>
			</div>
			@endif

		</div>
		<input type="text" name="client_id" value="{{$client->id}}" hidden /> 
	</form>
</div>