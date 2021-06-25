@include('layouts/partials.front.header-inner')
<style>
  p {

    font-family: "Montserrat", sans-serif !important;
  }
</style>
<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;,sans-serif;margin: 90px 0 30px;font-weight: bold;color: #fff;text-transform: uppercase;font-size: 36px;">
          Sales Rep Tracking App Features </h1>
        <!--   <h2 style="margin: 0PX;">Features</h2> -->
      </div>
    </div>
  </div>
</div>
<!-- banner end -->
<div class="tracking-section feature">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="sales-title">Sales Manager Dashboard</h2>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking  tracking01">
          <div class="feature-icon"><a href="{{ route('real-time-gps-tracking') }}" style="text-decoration:double;"><i
                  class="fas fa-map-marker-alt tracking-01"></i></a></div>
          <h3><i class="fas fa-map-marker-alt f-mobile-view"></i><a href="{{ route('real-time-gps-tracking') }}"
                                                                    style="text-decoration:double;"> Employee real time
              tracking</a></h3>
          <p>Know where your sales employees are during office hours</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('maintain-attendance') }}" style="text-decoration:double;"><i
                  class="fas fa-fingerprint tracking-01"></i></a></div>
          <h3><i class="fas fa-fingerprint f-mobile-view"></i><a href="{{ route('maintain-attendance') }}"
                                                                 style="text-decoration:double;"> Maintain
              attendance </a></h3>
          <p>Check and manage your employee attendance</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-sales-expense') }}" style="text-decoration:double;"><i
                  class="fas fa-file-invoice-dollar tracking-01"></i></a></div>
          <h3><i class="fas fa-file-invoice-dollar f-mobile-view"></i><a href="{{ route('manage-sales-expense') }}"
                                                                         style="text-decoration:double;"> Manage
              expense </a></h3>
          <p>Approve and manage expense for your salesman</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('travel-distance-calculator') }}" style="text-decoration:double;"><i
                  class="fas fa-calculator tracking-01"></i></a></div>
          <h3><i class="fas fa-calculator f-mobile-view"></i><a href="{{ route('travel-distance-calculator') }}"
                                                                style="text-decoration:double;"> Travel distance
              calculator </a></h3>
          <p>Automatic calculation of distance covered by your salesman</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('tasks-assignment') }}" style="text-decoration:double;"><i
                  class="far fa-edit tracking-01"></i></a></div>
          <h3><i class="far fa-edit f-mobile-view"></i><a href="{{ route('tasks-assignment') }}"
                                                          style="text-decoration:double;"> Assign tasks </a></h3>
          <p>Update your salesman about the task they need to carry out</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('monthly-sales-target') }}" style="text-decoration:double;"><i
                  class="fas fa-signal tracking-01"></i></a></div>
          <h3><!-- <i class="fas fa-chart-line f-mobile-view"> --><i class="fas fa-signal f-mobile-view"></i><a
                href="{{ route('monthly-sales-target') }}" style="text-decoration:double;"> Monthly sales target </a>
          </h3>
          <p>Assign sales target to your salesman</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('leave-application') }}" style="text-decoration:double;"><i
                  class="far fa-calendar-alt tracking-01"></i></a></div>
          <h3><i class="far fa-calendar-alt f-mobile-view"></i><a href="{{ route('leave-application') }}"
                                                                  style="text-decoration:double;"> Leave management </a>
          </h3>
          <p>Approve/reject sales employee leave application</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('announcement') }}" style="text-decoration:double;"><i
                  class="fas fa-bullhorn tracking-01"></i></a></div>
          <h3><i class="fas fa-bullhorn f-mobile-view"></i><a href="{{ route('announcement') }}"
                                                              style="text-decoration:double;"> Announcements </a></h3>
          <p>Create announcements and update all your sales people at once </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-products') }}" style="text-decoration:double;"><i
                  class="fas fa-th tracking-01"></i></a></div>
          <h3><i class="fas fa-th f-mobile-view"></i><a href="{{ route('manage-products') }}"
                                                        style="text-decoration:double;"> Manage Products </a></h3>
          <p>Create and update your product list including pricing details</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-clients') }}" style="text-decoration:double;"><i
                  class="fas fa-user-plus tracking-01"></i></a></div>
          <h3><i class="fas fa-user-plus f-mobile-view"></i><a href="{{ route('manage-clients') }}"
                                                               style="text-decoration:double;"> Manage Clients </a></h3>
          <p>Maintain all your client records along with their contact details and location</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('mark-client-location') }}" style="text-decoration:double;"><i
                  class="fas fa-location-arrow tracking-01"></i> </a></div>
          <h3><i class="fas fa-location-arrow f-mobile-view"></i><a href="{{ route('mark-client-location') }}"
                                                                    style="text-decoration:double;"> Client location
              mapping </a></h3>
          <p>Map client's location with GPS coordinates </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-orders') }}" style="text-decoration:double;"><i
                  class="fas fa-tasks tracking-01"></i></a></div>
          <h3><i class="fas fa-tasks f-mobile-view"></i><a href="{{ route('manage-orders') }}"
                                                           style="text-decoration:double;"> Manage Orders </a></h3>
          <p>Get notified instantly for orders received by salesman </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-collections') }}" style="text-decoration:double;"><i
                  class="fas fa-hand-holding-usd tracking-01"></i></a></div>
          <h3><i class="fas fa-hand-holding-usd f-mobile-view"></i><a href="{{ route('manage-collections') }}"
                                                                      style="text-decoration:double;"> Manage
              Payments/Collection </a></h3>
          <p>Get notified when your salesman has collected payment </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('meeting-records') }}" style="text-decoration:double;"><i
                  class="fas fa-file-invoice tracking-01"></i></a></div>
          <h3><i class="fas fa-file-invoice f-mobile-view"></i><a href="{{ route('meeting-records') }}"
                                                                  style="text-decoration:double;"> Meeting Records </a>
          </h3>
          <p>Maintain detailed record of meeting clients</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('sales-employee-reports') }}" style="text-decoration:double;"><i
                  class="fas fa-user-edit tracking-01"></i></a></div>
          <h3><i class="fas fa-user-edit f-mobile-view"></i><a href="{{ route('sales-employee-reports') }}"
                                                               style="text-decoration:double;"> Sales Employee
              Reports </a></h3>
          <p> Maintain reports of every salesman including their working hours, distance travelled, GPS location, orders
            and collection</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('measure-sales-performance') }}"
                                       style="text-decoration:double;"><i class="fas fa-balance-scale tracking-01"></i></a>
          </div>
          <h3><i class="fas fa-balance-scale f-mobile-view"></i><a href="{{ route('measure-sales-performance') }}"
                                                                   style="text-decoration:double;"> Measure salesman
              performance </a></h3>
          <p>Get the overall details of your salesman activities, measure performance by comparing it with assigned
            target</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-enquiries') }}" style="text-decoration:double;"><i
                  class="fas fa-info-circle tracking-01"></i></a></div>
          <h3><i class="fas fa-info-circle f-mobile-view"></i><a href="{{ route('manage-enquiries') }}"
                                                                 style="text-decoration:double;"> Enquiry
              Management </a></h3>
          <p>Manage and convert leads generated by your employees into sales</p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="tracking-section feature salesman-sec">
  <div class="container">
    <div class="row">
      <!-- sames man app start -->
      <div class="col-sm-12">
        <h3 class="sales-title"
            style="font-size: 40px;font-family: &quot;Montserrat&quot;, sans-serif;text-transform: uppercase;font-weight: bold;">
          Salesman App</h3>
        <h3 class="using" style="font-family: &quot;Montserrat&quot;,sans-serif;">Using our Sales app, your sales rep
          would be able to </h3>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('maintain-attendance') }}" style="text-decoration:double;"><i
                  class="fas fa-check tracking-01"></i></a></div>
          <h3><i class="fas fa-check f-mobile-view"></i><a href="{{ route('maintain-attendance') }}"
                                                           style="text-decoration:double;"> Mark Attendance </a></h3>
          <p>Maintain daily attendance on the app</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-orders') }}" style="text-decoration:double;"><i
                  class="fas fa-list-ul tracking-01"></i></a></div>
          <h3><i class="fas fa-list-ul f-mobile-view"></i><a href="{{ route('manage-orders') }}"
                                                             style="text-decoration:double;"> Manage Orders </a></h3>
          <p> Input client orders instantly</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-collections') }}" style="text-decoration:double;"><i
                  class="fas fa-money-check-alt tracking-01"></i></a></div>
          <h3><i class="fas fa-money-check-alt f-mobile-view"></i><a href="{{ route('manage-collections') }}"
                                                                     style="text-decoration:double;"> Add
              Collections </a></h3>
          <p> Maintain the details of payments received from clients</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('add-daily-remarks') }}" style="text-decoration:double;"><i
                  class="fas fa-list-ol tracking-01"></i></a></div>
          <h3><i class="fas fa-list-ol f-mobile-view"></i><a href="{{ route('add-daily-remarks') }}"
                                                             style="text-decoration:double;"> Add Daily remarks </a>
          </h3>
          <p>Report important information to the manager/admin</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-clients') }}" style="text-decoration:double;"><i
                  class="fas fa-user-plus tracking-01"></i></a></div>
          <h3><i class="fas fa-user-plus f-mobile-view"></i><a href="{{ route('manage-clients') }}"
                                                               style="text-decoration:double;"> Manage Clients </a></h3>
          <p>Manage the details of clients/parties </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('manage-sales-expense') }}" style="text-decoration:double;"><i
                  class="fas fa-file-invoice-dollar tracking-01"></i></a></div>
          <h3><i class="fas fa-file-invoice-dollar f-mobile-view"></i><a href="{{ route('manage-sales-expense') }}"
                                                                         style="text-decoration:double;"> Add
              Expenses </a></h3>
          <p>Record daily expenses and get approvals</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('leave-application') }}" style="text-decoration:double;"><i
                  class="fas fa-file tracking-01"></i></a></div>
          <h3><i class="fas fa-file f-mobile-view"></i><a href="{{ route('leave-application') }}"
                                                          style="text-decoration:double;"> Leave Application </a></h3>
          <p>Apply for leaves and get notified about its acceptance</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('tasks-assignment') }}" style="text-decoration:double;"><i
                  class="fas fa-thumbtack tracking-01"></i></a></div>
          <h3><i class="fas fa-thumbtack f-mobile-view"></i><a href="{{ route('tasks-assignment') }}"
                                                               style="text-decoration:double;"> Manage tasks </a></h3>
          <p>Manage tasks based on deadlines</p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
          <div class="feature-icon"><a href="{{ route('works-offline') }}" style="text-decoration:double;"><i
                  class="fas fa-question-circle tracking-01"></i></a></div>
          <h3><i class="fas fa-question-circle f-mobile-view"></i><a href="{{ route('works-offline') }}"
                                                                     style="text-decoration:double;"> Works offline </a>
          </h3>
          <p>The app works offline, and data is automatically synced once internet coverage returns</p>
        </div>
      </div>

      <!--<div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
        <div class="feature-icon"><i class="fas fa-location-arrow tracking-01"></i> </div>
        <h3>Map Client’s location</h3>
          <p> Know your clients’/parties’ location with GPS coordinates </p>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 feature-01 slideanim">
        <div class="tracking tracking01">
        <div class="feature-icon"><i class="fas fa-map-marked-alt tracking-01"></i> </div>
        <h3>Real time GPS tracking</h3>
          <p>On mobile GPS and let the app track you</p>
        </div>
      </div>-->

    </div>
  </div>
</div>
<!-- blog section end -->
@include('layouts/partials.front.footer')