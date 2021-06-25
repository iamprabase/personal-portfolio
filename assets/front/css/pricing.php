<?php include 'includes/header-inner.php'; ?>

<!-- CHECK IP ADDRESS -->
<?php

$ip = $_SERVER['REMOTE_ADDR'];

function ip_details($IPaddress) 
{
	$json = file_get_contents("http://ipinfo.io/{$IPaddress}");
	$details = json_decode($json);
	return $details;
}

$details    =   ip_details($ip);

$country = $details->country;

?>
<!-- END OF CHECK IP ADDRESS -->


<div class="banner pricing01">
	<div class="container">
		<div class="row slideanim">
			<div class="col-sm-12 col-md-10 col-md-offset-1">
				<h2>Pricing</h2>
			</div>
		</div>
	</div>
</div>
<!-- banner end -->


<!-- pricing start -->
<section class="price-table price-table-2">
	<div class="container">
		<div class="row">
			<?php if ($country == "NP"): ?>
				<div class="desktop-view-sec">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 slideanim reveal">
						<!-- <h3 class="text-center sales-title" style="margin-top:0;">Standard Plan</h3> -->
						<!-- <h4 class="text-center">For Nepal</h4> -->
						<br/>
						<br/>
						<div class="table-responsive-sm table-responsive">
							<table cellspacing="5" class="table table-bordered">
								<thead style="background: #F16022; color:#fff; font-size: 16px;">
									<th scope="col" style="border-bottom: 1px solid #F16022"></th>
									<th class="text-center" style="border-bottom: 1px solid #F16022">Starter <br/> [up to 5 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Standard  <br/> [6-20 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Pro<br/> [21-50 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Super <br/> [51+ salesmen]</th>
								</thead>
								<tbody>
									<tr>
										<td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
										<td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> NRs 35,000</td>
									</tr>

									<tr>
										<td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
										<td class="text-center"><strong>NRs 800</strong></td>
										<td class="text-center"><strong>NRs 700</strong></td>
										<td class="text-center"><strong>NRs 600</strong></td>
										<td class="text-center"><strong>NRs 500</strong></td>
									</tr>
									<tr>
										<td scope="row"><strong>Features</strong></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>

									<tr>
										<td scope="row">Real Time GPS Tracking</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Travel distance calculator</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Mark Attendance </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Manage Clients</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Map Client's location</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Enquiries</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Order</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Collection </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Leave Application </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Add Daily Remark</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage salesmen expenses  </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Assign tasks</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Assign sales target </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Announcements</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Products </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Maintain Meeting Records</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Sales Employee Reports</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">App Works Offline </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Free Updates</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Training</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Video Demos and Tutorials</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									</tr>
								</tbody>
							</table> 
							<br/>
							<br/><br/>
							<p style="letter-spacing: 0.7px;"><strong style="
							border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
							<p>Additional setup charges - NRs 25,000</p>
							<p>* Customization is available only in white label plan and would be chargeable.</p>
							<p>* The above mentioned rates are excluding VAT.</p>
							<p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
						</div>
					</div><!--col-lg-12 col-md-12 col-sm-12 col-xs-12 end-->
				</div>

			    <div class="view-mobile">
					<div class="col-sm-12">
					    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingOne">
							      <h4 class="panel-title">
							        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							          Starter [up to 5 salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseOne" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
							        <div class="panel-body">	          
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								                <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> NRs 35,000</b></td>
									                </tr>
									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>NRs 800</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr>
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>
									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								              </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - NRs 25,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
								        </div>					 
							        </div>
							    </div>
							</div>					
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingTwo">
								    <h4 class="panel-title">
								        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								          Standard [6-20 salesmen]
								        </a>
								    </h4>
							    </div>
							    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							        <div class="panel-body">
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								               <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> NRs 35,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>NRs 700</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								                </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - NRs 25,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
							            </div>
							        </div>
							    </div>
							</div>
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingThree">
							      <h4 class="panel-title">
							        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							          Pro [21-50 salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							        <div class="panel-body">
							            <div class="col-sm-12">
							                <div class="table-responsive-sm ">
									            <table cellspacing="5" class="table table-bordered">
									              <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> NRs 35,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>NRs 600</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
									              </tbody>
									            </table> 
									            <br/>
									            <br/><br/>
									            <p style="letter-spacing: 0.7px;"><strong style="
									            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
									            <p>Additional setup charges - NRs 25,000</p>
									            <p>* Customization is available only in white label plan and would be chargeable.</p>
									            <p>* The above mentioned rates are excluding VAT.</p>
									            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
									        </div>
							            </div>
							        </div>
							    </div>
							</div>
						    <div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingFour">
							      <h4 class="panel-title">
							        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
							          Super [51+ salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
							        <div class="panel-body">			        
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								                <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> NRs 35,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>NRs 500</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
									            </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - NRs 25,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
							            </div>			    
							        </div>
							    </div>
						    </div>								
				    	</div>           
		    	    </div>
	            </div>
			<?php elseif($country == "IN"): ?>
				<div class="desktop-view-sec">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 slideanim reveal">
						<!-- <h3 class="text-center sales-title" style="margin-top:0;">Standard Plan</h3> -->
						<!-- <h4 class="text-center">For Nepal</h4> -->
						<br/>
						<br/>
						<div class="table-responsive-sm table-responsive">
							<table cellspacing="5" class="table table-bordered">
								<thead style="background: #F16022; color:#fff; font-size: 16px;">
									<th scope="col" style="border-bottom: 1px solid #F16022"></th>
									<th class="text-center" style="border-bottom: 1px solid #F16022">Starter <br/> [up to 5 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Standard  <br/> [6-20 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Pro<br/> [21-50 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Super <br/> [51+ salesmen]</th>
								</thead>
								<tbody>
									<tr>
										<td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
										<td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> INR 25,000</td>
									</tr>

									<tr>
										<td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
										<td class="text-center"><strong>INR 700</strong></td>
										<td class="text-center"><strong>INR 600</strong></td>
										<td class="text-center"><strong>INR 500</strong></td>
										<td class="text-center"><strong>INR 400</strong></td>
									</tr>
									<tr>
										<td scope="row"><strong>Features</strong></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>

									<tr>
										<td scope="row">Real Time GPS Tracking</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Travel distance calculator</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Mark Attendance </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Manage Clients</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Map Client's location</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Enquiries</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Order</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Collection </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Leave Application </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Add Daily Remark</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage salesmen expenses  </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Assign tasks</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Assign sales target </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Announcements</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Manage Products </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Maintain Meeting Records</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Sales Employee Reports</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">App Works Offline </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Free Updates</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Training</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Video Demos and Tutorials</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									</tr>
								</tbody>
							</table> 
							<br/>
							<br/><br/>
							<p style="letter-spacing: 0.7px;"><strong style="
							border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
							<p>Additional setup charges - INR 15,000</p>
							<p>* Customization is available only in white label plan and would be chargeable.</p>
							<p>* The above mentioned rates are excluding VAT.</p>
							<p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
						</div>
					</div><!--col-lg-12 col-md-12 col-sm-12 col-xs-12 end-->
				</div>

				<div class="view-mobile">
					<div class="col-sm-12">
					    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingOne">
							      <h4 class="panel-title">
							        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							          Starter [up to 5 salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseOne" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
							        <div class="panel-body">	          
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								                <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> INR 25,000</b></td>
									                </tr>
									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>INR 700</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr>
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>
									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								              </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - INR 15,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
								        </div>					 
							        </div>
							    </div>
							</div>					
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingTwo">
								    <h4 class="panel-title">
								        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								          Standard [6-20 salesmen]
								        </a>
								    </h4>
							    </div>
							    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							        <div class="panel-body">
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								               <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> INR 25,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>INR 600</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								                </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - INR 15,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
							            </div>
							        </div>
							    </div>
							</div>
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingThree">
							      <h4 class="panel-title">
							        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							          Pro [21-50 salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							        <div class="panel-body">
							            <div class="col-sm-12">
							                <div class="table-responsive-sm ">
									            <table cellspacing="5" class="table table-bordered">
									              <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> INR 25,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>INR 500</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
									              </tbody>
									            </table> 
									            <br/>
									            <br/><br/>
									            <p style="letter-spacing: 0.7px;"><strong style="
									            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
									            <p>Additional setup charges - INR 15,000</p>
									            <p>* Customization is available only in white label plan and would be chargeable.</p>
									            <p>* The above mentioned rates are excluding VAT.</p>
									            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
									        </div>
							            </div>
							        </div>
							    </div>
							</div>
						    <div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingFour">
							      <h4 class="panel-title">
							        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
							          Super [51+ salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
							        <div class="panel-body">			        
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								                <tbody>
									                <tr>
									                  <td scope="row" style=""><strong style="float:left;">Setup Cost (One Time Only)</strong></td>
									                  <td colspan="4" class="text-center"  style=" border:none; font-size: 16px;"> <!--<strong style="float:left;">Setup Cost (One Time Only)</strong><br/>--> <b> INR 25,000</b></td>
									                </tr>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>INR 400</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
									            </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - INR 15,000</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
							            </div>			    
							        </div>
							    </div>
						    </div>								
				    	</div>           
		    	    </div>
	            </div>
			<?php else: ?>
				<div class="desktop-view-sec">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 slideanim reveal">
						<!-- <h3 class="text-center sales-title" style="margin-top:0;">Standard Plan</h3> -->
						<!-- <h4 class="text-center">For Nepal</h4> -->
						<br/>
						<br/>
						<div class="table-responsive-sm table-responsive">
							<table cellspacing="5" class="table table-bordered">
								<thead style="background: #F16022; color:#fff; font-size: 16px;">
									<th scope="col" style="border-bottom: 1px solid #F16022"></th>
									<th class="text-center" style="border-bottom: 1px solid #F16022">Monthly<br/> [Min. 10 salesmen]</th>
									<th class="text-center" scope="col" style="border-bottom: 1px solid #F16022">Yearly  <br/> [Min. 1 salesman]</th>
								</thead>
								<tbody>
									<tr>
										<td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
										<td class="text-center"><strong>$ 18</strong></td>
										<td class="text-center"><strong>$ 15</strong></td>
									</tr>
									<tr>
										<td scope="row"><strong>Features</strong></td>
										<td></td>
										<td></td>
									</tr>

									<tr>
										<td scope="row">Real Time GPS Tracking</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Travel distance calculator</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Mark Attendance </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Manage Clients</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>         
									</tr>

									<tr>
										<td scope="row">Map Client's location</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>       
									</tr>

									<tr>
										<td scope="row">Manage Enquiries</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>        
									</tr>

									<tr>
										<td scope="row">Manage Order</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>     
									</tr>

									<tr>
										<td scope="row">Manage Collection </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>         
									</tr>

									<tr>
										<td scope="row">Leave Application </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>       
									</tr>

									<tr>
										<td scope="row">Add Daily Remark</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>         
									</tr>

									<tr>
										<td scope="row">Manage salesmen expenses  </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>       
									</tr>

									<tr>
										<td scope="row">Assign tasks</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>           
									</tr>

									<tr>
										<td scope="row">Assign sales target </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>             
									</tr>

									<tr>
										<td scope="row">Announcements</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>  
									</tr>

									<tr>
										<td scope="row">Manage Products </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Maintain Meeting Records</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr>
										<td scope="row">Sales Employee Reports</td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>           
									</tr>

									<tr>
										<td scope="row">App Works Offline </td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>      
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Free Updates</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Training</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>      
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Video Demos and Tutorials</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>       
									</tr>

									<tr style="background: #e9efef">
										<td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>
										<td class="text-center"> <i class="fas fa-check"></i></td>    
									</tr>

									<tr>
										<td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
										<td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									</tr>
								</tbody>
							</table> 
							<br/>
							<br/><br/>
							<p style="letter-spacing: 0.7px;"><strong style="
							border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
							<p>Additional setup charges - USD 500</p>
							<p>* Customization is available only in white label plan and would be chargeable.</p>
							<p>* The above mentioned rates are excluding VAT.</p>
							<p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
						</div>
					</div><!--col-lg-12 col-md-12 col-sm-12 col-xs-12 end-->
				</div>

			    <div class="view-mobile">
					<div class="col-sm-12">
					    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingOne">
							      <h4 class="panel-title">
							        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							          Monthly [Min. 10 salesmen]
							        </a>
							      </h4>
							    </div>
							    <div id="collapseOne" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
							        <div class="panel-body">	          
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								                <tbody>
									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>$ 18</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>
									                <tr>
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>
									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								              </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - USD 500</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
								        </div>					 
							        </div>
							    </div>
							</div>

							<div class="panel panel-default">
							    <div class="panel-heading" role="tab" id="headingTwo">
								    <h4 class="panel-title">
								        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								          Yearly [Min. 1 salesman]
								        </a>
								    </h4>
							    </div>
							    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							        <div class="panel-body">
							            <div class="table-responsive-sm ">
								            <table cellspacing="5" class="table table-bordered">
								               <tbody>

									                <tr>
									                  <td scope="row"><b> Subscription fee (Per Salesman per month)</b></td>
									                  <td class="text-center"><strong>$ 15</strong></td>
									                </tr>
									                <tr>
									                  <td scope="row"><strong>Features</strong></td>
									                  <td></td>
									                </tr>

									                <tr>
									                  <td scope="row">Real Time GPS Tracking</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Travel distance calculator</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Mark Attendance </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>
									                </tr>

									                <tr>
									                  <td scope="row">Manage Clients</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Map Client's location</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Enquiries</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Order</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Collection </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Leave Application </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Add Daily Remark</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>         
									                </tr>

									                <tr>
									                  <td scope="row">Manage salesmen expenses  </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign tasks</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Assign sales target </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr>
									                  <td scope="row">Announcements</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Manage Products </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Maintain Meeting Records</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">Sales Employee Reports</td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr>
									                  <td scope="row">App Works Offline </td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Free Updates</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>            
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Training</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Video Demos and Tutorials</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>           
									                </tr>

									                <tr >
									                  <td scope="row"><strong> Support by Phone/Email/Chat</strong></td>
									                  <td class="text-center"> <i class="fas fa-check"></i></td>          
									                </tr>

									                <tr>
									                  <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
									                  <td class="text-center"> <a href="#" class="view-more">Buy Now</a></td>
									                </tr>
								                </tbody>
								            </table> 
								            <br/>
								            <br/><br/>
								            <p style="letter-spacing: 0.7px;"><strong style="
								            border-bottom: 1px solid #000;">White Label Plan (App in Play Store with your company name and above features)  </strong></p>
								            <p>Additional setup charges - USD 500</p>
								            <p>* Customization is available only in white label plan and would be chargeable.</p>
								            <p>* The above mentioned rates are excluding VAT.</p>
								            <p>* Training includes:- 2 sessions of 2 hours each - one for sales manager/admin, one for salesmen</p>
							            </div>
							        </div>
							    </div>
							</div>

				    	</div>           
		    	    </div>
	            </div>
			<?php endif ?>
		</div><!--.row slideanim end-->
	</div><!--.container end-->
</section>
<!-- blog section end -->
<?php include 'includes/footer.php'; ?>