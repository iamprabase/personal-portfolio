@extends('layouts.company')
@section('title', 'Dashboard')
@section('title', 'Company Dashboard')
@section('stylesheets')
<style> 
.grt {
	/* max-width: 476px; */
	font-family: "Lato", sans-serif;
	font-size: 16px;
	line-height: 27px;
	padding: 14px;
}
.grt h1 {
  font-family: inherit;
	font-size: 23px;
  font-weight: 600;
	line-height: 36px;
	margin-top: 0;
	margin-bottom: 14px;
}
.grt h2 {
  font-family: inherit;
	font-size: 18px;
  font-weight: 600;
	line-height: 29px;
	margin-top: 0;
	margin-bottom: 9px;
}

.mb-11 {
  margin-bottom: 11px!important;
}
.grt p, .grt ul, .grt .footnotes {
	margin-bottom: 23px;
}
.grt ul {
	margin-left: 17px;
}
.grt li {
	margin-bottom: 9px;
}
.grt li a i {
  opacity: 0;
  visibility: hidden;
  margin-left: 7px;
}

.grt li a:hover i {
  opacity: 1;
  visibility: visible;
}
.grt .footnotes {
	font-size: 14px;
	line-height: 19px;
	padding-top: 14px;
	border-top: 1px solid rgba(0,0,0,0.15);
}
.grt .footnotes p {
	margin-bottom: 14px;
}
.grt .footnotes :last-child {
	margin-bottom: 0;
}
@media all and (min-width: 522px) {
	.grt {
		padding: 23px;
	}
}

.table-bordered {
  border: 1px solid rgba(0,0,0,0.15);
}

.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
  border: 1px solid rgba(0,0,0,0.15);
}

.grt table.table.table-striped {
  margin-bottom: 23px!important;
}

</style>

@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-lg-6">
    <div class="box box-default">
        <div class="grt">
          <h1>Help us with your genuine feedback and earn $50 Credits! 💰</h1>
          <p>Earn <strong>$25 Credit</strong> by participating in a <strong>10 minute</strong> interview session with us, which would be recorded and used as a <strong>video testimonial</strong> for Delta Sales App. Email us your interest at <a href="mailto:sales@deltatechnepal.com">sales@deltatechnepal.com</a>, and we will schedule a meeting.</p>
          <p>Write a genuine review for us in the below mentioned platforms and earn additional $25 Credits in Delta Sales App account on recurring subscriptions.</p>
          <h2>Platforms 🌎:</h2>
          <ul>
            <li>Captera:  <a href="https://reviews.capterra.com/new/178423" target="_blank">https://reviews.capterra.com/new/178423  <i class="fa fa-external-link"></i> </a></li>
            <li>FinancesOnline: <a href="https://reviews.financesonline.com/p/deltasalesapp/" target="_blank">https://reviews.financesonline.com/p/deltasalesapp/  <i class="fa fa-external-link"></i>  </a></li>
            <li>G2: <a href="https://www.g2.com/products/deltasalesapp/reviews" target="_blank">https://www.g2.com/products/deltasalesapp/reviews  <i class="fa fa-external-link"></i> </a></li>
            <li>Software Suggest: <a href="https://www.softwaresuggest.com/us/deltasalesapp/write-review" target="_blank">https://www.softwaresuggest.com/us/deltasalesapp/write-review <i class="fa fa-external-link"></i> </a></li>
          </ul>
           <p>Once you have put your review, please share the account name, URL and snapshot so that credits are applied to your next invoice.</p>
           <p>Email us all the proof at <a href="mailto:sales@deltatechnepal.com">sales@deltatechnepal.com</a> with subject line "<b>Feedback of Delta Sales App</b>"</p>
           <p class="mb-11">What you need to email us?</p>
           <ul>
               <li>Email used for feedback 📧 </li>
               <li>Name of the person 👦🏾 </li>
               <li>Screenshot of your feedback 💻 </li>
           </ul>           
           <p> 🎉 Let's grow together!</p>
           <div class="footnotes">
             <p>* Credits would be applied to the next billing. It can be carried forward to a maximum of 3 billings for monthly subscribers.</p>
           </div>           
        </div>
      </div>  
    </div>
    <div class="col-lg-6">
      <div class="box box-default">
        <div class="grt">
            <h1>Refer Delta Sales App to your friends and earn credits! 🔉</h1>
           <p>We offer free usage for your users if you refer us to clients who go for a yearly subscription, and free credits if they go for a monthly subscription.</p>
           <table class="table table-bordered table-striped">
             <tbody>
               <tr>
                 <th></th>
                 <th>Monthly Subscription</th>
                 <th>Yearly Subscription</th>
               </tr>
               <tr>
                 <td><strong>6-10 users</strong></td>
                 <td>$10 credit/month for max. 3 months</td>
                 <td>1 month free for 2 users</td>
               </tr>  
               <tr>
                 <td><strong>11-25 users</strong></td>
                 <td>$20 credit/month for max. 3 months</td>
                 <td>2 months free for 3 users</td>
               </tr>
               <tr>
                 <td><strong>26-50 users</strong></td>
                 <td>$50 credit/month for max. 3 months</td>
                 <td>2 months free for 5 users</td>
               </tr>
               <tr>
                 <td><strong>51-99 users</strong></td>
                  <td>$100 credit/month for max. 3 months</td>
                 <td>3 months free for 7 users</td>
               </tr>
               <tr>
                 <td><strong>100+ users</strong></td>
                 <td>$150 credit/month for max. 3 months</td>
                 <td>6 months free for 9 users</td>
               </tr>                          
             </tbody>
           </table>
           <div class="footnotes">
             <p>*  Monthly referral credit will be given for 3 consecutive months. Referred clients must be subscribed for a minimum of 3 months. Credits will only be applied for months used by the referred client.</p>
             <p>* Credits would be applied to the next billing. It can be carried forward to a maximum of 3 billings for monthly subscribers.</p>
        </div>
      </div>
    </div>
  </div>      
</section>
@endsection