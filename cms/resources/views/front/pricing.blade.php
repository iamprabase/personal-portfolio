@include('layouts/partials.front.header-inner')

<!-- CHECK IP ADDRESS -->
<?php

$ip = $_SERVER['REMOTE_ADDR'];

function ip_details($IPaddress)
{
    $json = file_get_contents("http://ipinfo.io/{$IPaddress}");
    $details = json_decode($json);
    return $details;
}

$details = ip_details($ip);

$country = $details->country;

?>
<!-- END OF CHECK IP ADDRESS -->

<style>
  .table > tbody > tr > td {
    vertical-align: middle !important;
  }

  .slideanim .view-more {

    margin-top: 0px;
  }
</style>

<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;,sans-serif;margin: 90px 0 30px;font-weight: bold;color: #fff;text-transform: uppercase;font-size: 36px;">
          Mobile Sales App Pricing </h1>
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
          <br/>
          <br/>
          <div class="table-responsive-sm table-responsive">
            <table cellspacing="5" class="table table-bordered">
              <thead style="background: #F16022; color:#fff; font-size: 16px;">
              <th scope="col" style="border-bottom: 1px solid #F16022"></th>
              <th class="text-center" style="border-bottom: 1px solid #F16022">PRICING <br/></th>
              </thead>
              <tbody>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><strong style="float:left;margin-top: 0px;">Setup Cost
                    (One Time Only)</strong></td>
                <td class="text-center" style="font-size: 18px; background: #c1d2d1;"><strong> NRs 95,000 </strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><b> Subscription fee per Salesman per month (Billed
                    Annually)</b></td>
                <td class="text-center" style="background: #c1d2d1;;"><strong>NRs 950</strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #e9efef;;"><strong>Features</strong></td>
                <td style="background: #e9efef;;"></td>
              </tr>
              <tr>
                <td scope="row">Real time GPS tracking</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Travel distance calculator</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage orders</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Assign task to salesmen</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage payment collection</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage sales expenses</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain party records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Map client's location</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Detailed reports</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain attendance</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Leave application</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Make announcements</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Manage products</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Maintain meeting records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">App Works offline</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Free updates</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr style="background: #e9efef">
                <td scope="row"><strong> Training</strong></td>
                <td class="text-center"><strong>* Two sessions of 2 hours each - one for sales manager/admin, one for
                    salesman</strong></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong>Support by Phone/Email/Chat/Skype</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Branded App (Optional)</strong></td>
                <td class="text-center"><strong> Additional NRs 29,000 per year</strong></td>
              </tr>
              <tr>
                <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
                <td class="text-center"><a href="javascript:void(Tawk_API.toggle())" class="view-more">Buy Now</a></td>
              </tr>
              </tbody>
            </table>
            <br/>
            <br/><br/>
            <p style="margin-top: -57px;">* Additional training session will be charged at NRs 1000/hr.</p>
            <p>* Historical data - 1 year only. For every additional year of data, customer would pay 10% of previous
              year's billing.</p>
            <p>* GPS Maps historical data - 1 week only.</p>
            <p>* The above mentioned rates are excluding VAT.</p>
          </div>
        </div>
      </div>

      <div class="view-mobile">
        <div class="col-sm-12">
          <p style="font-size:16px; border-bottom: 1px solid; "><b> PRICING</b></p>
          <p> Setup Cost [One time only] - NRs 95,000 </p>
          <p> Subscription fee Per Salesman per month [Billed Annually] - NRs 950 </p>
          <p><i class="far fa-check-square m-v-icon"></i> All features</p>
          <p><i class="far fa-check-square m-v-icon"></i> Free Updates </p>
          <p><i class="far fa-check-square m-v-icon"></i> Training</p>
          <p><i class="far fa-check-square m-v-icon"></i> Support by Phone/Email/Chat/Skype </p>
          <br/>
          <p>* Branded App [Optional] - <strong>Additional NRs 29,000 per year </strong>
          <p>* Training includes:- Two sessions of 2 hours each - one for sales manager/admin, one for salesman</p>
          <p>* Additional training session will be charged at NRs 1000/hr.</p>
          <p>* Historical data - 1 year only. For every additional year of data, customer would pay 10% of previous
            year's billing.</p>
          <p>* GPS Maps historical data - 1 week only.</p>
          <p>* The above mentioned rates are excluding VAT.</p>
          <br/>
        </div>
      </div>

      <!-- ///////////////////////////////////////////////////////////////////////////////////////// -->
      <!-- ///////////////////////////////////////// INDIA  /////////////////////////////////// -->
      <!-- ///////////////////////////////////////////////////////////////////////////////////////// -->

        <?php elseif($country == "IN"): ?>
      <div class="desktop-view-sec">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 slideanim reveal">
          <br/>
          <br/>
          <div class="table-responsive-sm table-responsive">
            <table cellspacing="5" class="table table-bordered">
              <thead style="background: #F16022; color:#fff; font-size: 16px;">
              <th scope="col" style="border-bottom: 1px solid #F16022"></th>
              <th class="text-center" style="border-bottom: 1px solid #F16022">PRICING <br/></th>
              </thead>
              <tbody>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><strong style="float:left;margin-top: 0px;">Setup Cost
                    (One Time Only)</strong></td>
                <td class="text-center" style="font-size: 18px; background: #c1d2d1;"><strong> $ 0</strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><b> Subscription fee per Salesman per month </br>(Billed
                    Annually, min. 5 salesmen)</b></td>
                <td class="text-center" style="background: #c1d2d1;;"><strong>$ 14.95</strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #e9efef;;"><strong>Features</strong></td>
                <td style="background: #e9efef;;"></td>
              </tr>

              <tr>
                <td scope="row">Real time GPS tracking</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Travel distance calculator</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage orders</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Assign task to salesmen</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage payment collection</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage sales expenses</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain party records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Map client's location</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Detailed reports</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain attendance</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Leave application</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Make announcements</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Manage products</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Maintain meeting records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">App Works offline</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Free updates</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr style="background: #e9efef">
                <td scope="row"><strong> Training</strong></td>
                <td class="text-center"><strong>* Two sessions of 2 hours each - one for sales manager/admin, one for
                    salesman</strong></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Support by Phone/Email/Chat/Skype</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Branded App (Optional)</strong></td>
                <td class="text-center"><strong> Additional $ 299 per year</strong></td>
              </tr>
              <tr>
                <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
                <td class="text-center"><a href="javascript:void(Tawk_API.toggle())" class="view-more">Buy Now</a></td>
              </tr>
              </tbody>
            </table>
            <br/>
            <br/><br/>
            <p style="margin-top: -57px;">* Historical data - 1 year only. For every additional year of data, customer
              would pay 10% of previous year's billing.</p>
            <p>* GPS Maps historical data - 1 week only.</p>
          </div>
        </div>
      </div>

      <div class="view-mobile">
        <div class="col-sm-12">
          <p style="font-size:16px; border-bottom: 1px solid; "><b> PRICING</b></p>
          <p> Setup Cost [One time only] - $ 0 </p>
          <p> Subscription fee per Salesman per month </br>(Billed Annually, min. 5 salesmen) - $ 14.95 </p>
          <p><i class="far fa-check-square m-v-icon"></i> All features</p>
          <p><i class="far fa-check-square m-v-icon"></i> Free Updates </p>
          <p><i class="far fa-check-square m-v-icon"></i> Training</p>
          <p><i class="far fa-check-square m-v-icon"></i> Support by Phone/Email/Chat/Skype </p>
          <br/>
          <p>* Branded App [Optional] - <strong>Additional $ 299 per year </strong>
          <p>* Training includes:- Two sessions of 2 hours each - one for sales manager/admin, one for salesman</p>
          <p>* Historical data - 1 year only. For every additional year of data, customer would pay 10% of previous
            year's billing.</p>
          <p>* GPS Maps historical data - 1 week only.</p>
          <br/>
        </div>
      </div>
        <?php else: ?>


    <!-- ///////////////////////////////////////////////////////////////////////////////////////// -->
      <!-- ///////////////////////////////////////// others country  /////////////////////////////////// -->
      <!-- ///////////////////////////////////////////////////////////////////////////////////////// -->

      <div class="desktop-view-sec">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 slideanim reveal">
          <br/>
          <br/>
          <div class="table-responsive-sm table-responsive">
            <table cellspacing="5" class="table table-bordered">
              <thead style="background: #F16022; color:#fff; font-size: 16px;">
              <th scope="col" style="border-bottom: 1px solid #F16022"></th>
              <th class="text-center" style="border-bottom: 1px solid #F16022">PRICING <br/></th>
              </thead>
              <tbody>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><strong style="float:left;margin-top: 0px;">Setup Cost
                    (One Time Only)</strong></td>
                <td class="text-center" style="font-size: 18px; background: #c1d2d1;"><strong> $ 0</strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #c1d2d1;;"><b> Subscription fee per Salesman per month </br>(Billed
                    Annually, min. 5 salesmen)</b></td>
                <td class="text-center" style="background: #c1d2d1;;"><strong>$ 14.95</strong></td>
              </tr>
              <tr>
                <td scope="row" style="background: #e9efef;;"><strong>Features</strong></td>
                <td style="background: #e9efef;;"></td>
              </tr>

              <tr>
                <td scope="row">Real time GPS tracking</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Travel distance calculator</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage orders</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Assign task to salesmen</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage payment collection</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Manage sales expenses</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain party records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Map client's location</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Detailed reports</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Maintain attendance</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Leave application</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">Make announcements</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Manage products</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr>
                <td scope="row">Maintain meeting records</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr>
                <td scope="row">App Works offline</td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Free updates</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>

              <tr style="background: #e9efef">
                <td scope="row"><strong> Training</strong></td>
                <td class="text-center"><strong>* Two sessions of 2 hours each - one for sales manager/admin, one for
                    salesman</strong></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Support by Phone/Email/Chat/Skype</strong></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
              </tr>
              <tr style="background: #e9efef">
                <td scope="row"><strong> Branded App (Optional)</strong></td>
                <td class="text-center"><strong> Additional $ 299 per year</strong></td>
              </tr>
              <tr>
                <td scope="row" style="border:1px solid #f5f5f5; border-right:1px solid #ccc;"></td>
                <td class="text-center"><a href="javascript:void(Tawk_API.toggle())" class="view-more">Buy Now</a></td>
              </tr>
              </tbody>
            </table>
            <br/>
            <br/><br/>
            <p style="margin-top: -57px;">* Historical data - 1 year only. For every additional year of data, customer
              would pay 10% of previous year's billing.</p>
            <p>* GPS Maps historical data - 1 week only.</p>
          </div>
        </div>
      </div>

      <div class="view-mobile">
        <div class="col-sm-12">
          <p style="font-size:16px; border-bottom: 1px solid; "><b> PRICING</b></p>
          <p> Setup Cost [One time only] - $ 0 </p>
          <p> Subscription fee per Salesman per month </br>(Billed Annually, min. 5 salesmen) - $ 14.95 </p>
          <p><i class="far fa-check-square m-v-icon"></i> All features</p>
          <p><i class="far fa-check-square m-v-icon"></i> Free Updates </p>
          <p><i class="far fa-check-square m-v-icon"></i> Training</p>
          <p><i class="far fa-check-square m-v-icon"></i> Support by Phone/Email/Chat/Skype </p>
          <br/>
          <p>* Branded App [Optional] - <strong>Additional $ 299 per year </strong>
          <p>* Training includes:- Two sessions of 2 hours each - one for sales manager/admin, one for salesman</p>
          <p>* Historical data - 1 year only. For every additional year of data, customer would pay 10% of previous
            year's billing.</p>
          <p>* GPS Maps historical data - 1 week only.</p>
          <br/>
        </div>
      </div>
        <?php endif ?>
    </div><!--.row slideanim end-->
  </div><!--.container end-->
</section>
<!-- blog section end -->
@include('layouts/partials.front.footer')