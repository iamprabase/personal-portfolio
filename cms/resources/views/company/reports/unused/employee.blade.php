@extends('layouts.company')@section('stylesheets')@endsection@section('content')  <section class="content">    <div class="row">      <div class="col-xs-12">          <?php          if($_SESSION['sessVar']['user_role_id'] == 3){?>        <div class="box box-primary">          <div class="box-header">            <h3 class="box-title"><b>Add New Collection</b></h3>            <a href="add-collection-report.php" class="btn btn-primary pull-right">Add New</a>          </div>        </div>      <?php }?>      <!-- /.box -->        <div class="box">          <div class="box-header">            <form class="form-horizontal" id="" method="post" action="#" enctype="multipart/form-data">              <div class="row">                <div class="col-sm-4">                  <div class="form-group" style="margin-left: 0px;">                    <label>Party name:</label>                    <select class="form-control select2" style="width: 100%;">                      <option disabled="" selected="" value="">-- Select Party name --</option>                      <option value="6">ABC Technology</option>                      <option value="280">Mujtaba</option>                      <option value="339">Mathuram</option>                      <option value="309">Montee</option>                      <option value="338">tyere</option>                      <option value="269">Nauman</option>                      <option value="273">Naveen</option>                    </select>                  </div>                </div>                <div class="col-sm-4">                  <label>Salesman:</label>                  <select class="form-control select2" style="width: 100%;">                    <option disabled="" selected="" value="">-- Select Salesman --</option>                    <option value="175">Cassandra Lim [MTEM175]</option>                    <option value="31">Dalene Mae Bury [MTEM31]</option>                    <option value="5">Dilip Developer [MTEM5]</option>                    <option value="285">Mr. Abilash [MTEM285]</option>                    <option value="281">Mr. Ajay [MTEM281]</option>                    <option value="325">Mr. Amit Singh [MTEM325]</option>                    <option value="211">Mr. Amro Khalil [MTEM211]</option>                    <option value="331">Mr. Andrew [MTEM331]</option>                    <option value="329">Mr. Ankur Gupta [MTEM329]</option>                    <option value="327">Mr. Bikash [MTEM327]</option>                    <option value="54">Mr. Cardozo [MTEM54]</option>                    <option value="178">Mr. Carlos [MTEM178]</option>                    <option value="263">Mr. Chandan pareek [MTEM263]</option>                    <option value="320">Mr. Chandhramouli [MTEM320]</option>                    <option value="293">Mr. Charles [MTEM293]</option>                    <option value="305">Mr. Charles Kipyegon Chepkwony [MTEM305]</option>                    <option value="203">Mr. David Vousden [MTEM203]</option>                    <option value="314">Mr. Deepak [MTEM314]</option>                    <option value="334">Mr. DHEENADHAYALAN SELVARAJ [MTEM334]</option>                    <option value="259">Mr. Ganesh Chandak [MTEM259]</option>                    <option value="186">Mr. Giorgio [MTEM186]</option>                    <option value="311">Mr. Harsh Gupta [MTEM311]</option>                    <option value="262">Mr. Hemant [MTEM262]</option>                    <option value="317">Mr. Itechnotion [MTEM317]</option>                    <option value="321">Mr. Jass [MTEM321]</option>                    <option value="330">Mr. Javed Iqbal [MTEM330]</option>                    <option value="313">Mr. Jay gawade [MTEM313]</option>                    <option value="246">Mr. Jaydev [MTEM246]</option>                    <option value="248">Mr. Jonathan J Quintanilla [MTEM248]</option>                    <option value="296">Mr. Juan calle [MTEM296]</option>                    <option value="268">Mr. K Mujtaba [MTEM268]</option>                    <option value="267">Mr. Kaleem Mazhar [MTEM267]</option>                    <option value="6">Mr. Kapil [MTEM6]</option>                    <option value="280">Mr. Khawaja Mujtaba [MTEM280]</option>                    <option value="326">Mr. Mahendra Rajdhami [MTEM326]</option>                    <option value="339">Mr. Mathuram [MTEM339]</option>                    <option value="309">Mr. Montee [MTEM309]</option>                    <option value="338">Mr. Nabin Kumar Rawat [MTEM338]</option>                    <option value="269">Mr. Nauman Tasfir [MTEM269]</option>                    <option value="273">Mr. Naveen [MTEM273]</option>                    <option value="308">Mr. Niraj Jain [MTEM308]</option>                    <option value="291">Mr. Pankaj [MTEM291]</option>                    <option value="108">Mr. Phani [MTEM108]</option>                    <option value="337">Mr. Prabu T [MTEM337]</option>                    <option value="258">Mr. Pulkit [MTEM258]</option>                    <option value="274">Mr. Rahul Dubey [MTEM274]</option>                    <option value="328">Mr. Raj [MTEM328]</option>                    <option value="312">Mr. Rajnish Rajput [MTEM312]</option>                    <option value="306">Mr. Ramu N [MTEM306]</option>                    <option value="294">Mr. Rania dweik [MTEM294]</option>                    <option value="292">Mr. RAVI CHAURASIA [MTEM292]</option>                    <option value="333">Mr. Rishab Bhuwalka [MTEM333]</option>                    <option value="180">Mr. Robert [MTEM180]</option>                    <option value="286">Mr. Rupesh Singh [MTEM286]</option>                    <option value="271">Mr. SAHILL RAJWANI [MTEM271]</option>                    <option value="266">Mr. SANJAY [MTEM266]</option>                    <option value="287">Mr. Saran [MTEM287]</option>                    <option value="278">Mr. Sathish [MTEM278]</option>                    <option value="272">Mr. Satish [MTEM272]</option>                    <option value="310">Mr. Sean Michelsen [MTEM310]</option>                    <option value="290">Mr. Sherif [MTEM290]</option>                    <option value="279">Mr. Sovit [MTEM279]</option>                    <option value="265">Mr. Srinivas [MTEM265]</option>                    <option value="316">Mr. Subham [MTEM316]</option>                    <option value="295">Mr. Sunny chauhan [MTEM295]</option>                    <option value="283">Mr. Suresh Nair [MTEM283]</option>                    <option value="332">Mr. Tark [MTEM332]</option>                    <option value="174">Mr. Thomas [MTEM174]</option>                    <option value="319">Mr. VISHAL BHANDARI [MTEM319]</option>                    <option value="264">Mr. Walls and Acres [MTEM264]</option>                    <option value="270">Mr. Wilman [MTEM270]</option>                    <option value="256">Mr. Zahed [MTEM256]</option>                    <option value="255">Mr.Subhanshu [MTEM255]</option>                    <option value="257">Ms. ANKITA RATHORE [MTEM257]</option>                    <option value="289">Ms. Arshi [MTEM289]</option>                    <option value="315">Ms. Ekta [MTEM315]</option>                    <option value="210">Ms. Paridhi [MTEM210]</option>                    <option value="158">Ms. Salena [MTEM158]</option>                    <option value="288">Ms. Seema [MTEM288]</option>                    <option value="120">Ms. Veera [MTEM120]</option>                    <option value="307">Navneet [MTEM307]</option>                    <option value="261">Social Organics [MTEM261]</option>                  </select>                </div>                <style type="text/css">                  .salesman1 {                    margin: 5px 0px;                  }                  .salesman2 {                    margin-top: 28px;                  }                </style>              </div>              <div class="row">                <div class="col-sm-4 salesman1">                  <label>From:</label>                  <div class="input-group date">                    <div class="input-group-addon">                      <i class="fa fa-calendar"></i>                    </div>                    <input type="text" class="form-control pull-right" id="datepicker">                  </div>                </div>                <div class="col-sm-4 salesman1">                  <label>To:</label>                  <div class="input-group date">                    <div class="input-group-addon">                      <i class="fa fa-calendar"></i>                    </div>                    <input type="text" class="form-control pull-right" id="datepicker1">                  </div>                </div>                <div class="col-sm-3">                  <button value="get_report" name="search" type="submit" class="btn btn-info btn"                          style="margin-top: 30px;">Search                  </button>                </div>              </div>            </form>          </div>          <!-- /.box-header -->          <div class="box-body">            <table id="example1" class="table table-bordered table-striped">              <thead>              <tr>                <th>S.No.</th>                <th>Salesman Name</th>                <th>Date</th>                <th>Amount</th>              </tr>              </thead>              <tbody>              <tr>                <td>1</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>45</td>              </tr>              <tr>                <td>2</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>34</td>              </tr>              <tr>                <td>3</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>44</td>              </tr>              <tr>                <td>4</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>46</td>              </tr>              <tr>                <td>5</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>34</td>              </tr>              <tr>                <td>6</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>55</td>              </tr>              <tr>                <td>7</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>44</td>              </tr>              <tr>                <td>8</td>                <td>Dalene Mae Bury [MTEM31]</td>                <td>04/03/2018</td>                <!--  -->                <td>2</td>              </tr>              </tbody>              <tfoot>              <tr>                <th>S.No.</th>                <th>Salesman Name</th>                <th>Date</th>                <th>Amount</th>              </tr>              <tfoot>              <tr>                <th rowspan="1" colspan="1"></th>                <th rowspan="1" colspan="1"></th>                <th colspan="1" rowspan="1">Total Amount</th>                <th rowspan="1" colspan="1">700.00</th>              </tr>              </tfoot>              </tfoot>            </table>          </div>          <!-- /.box-body -->        </div>        <!-- /.box -->      </div>      <!-- /.col -->    </div>    <!-- /.row -->  </section>@endsection@section('scripts')  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>  <script type="text/javascript">      $(function () {          $("#from_date").datepicker({              format: "yyyy-mm-dd"          }).datepicker("setDate", "0");          $("#to_date").datepicker({              format: "yyyy-mm-dd"          }).datepicker("setDate", "0");      });      $(document).ready(function () {          $(document).on('change', '.employee_id', function () {              employeeTotalOrders();              //        var disctype=$(this).val();              // alert(disctype);              // alert('hi');          });      });      function employeeTotalOrders() {          var start = $('input#from_date').data('datepicker').startDate.format('YYYY-MM-DD');          var end = $('input#to_date').data('datepicker').endDate.format('YYYY-MM-DD');          var data_expense = {              employee_id: $('select#employee_id').val(),              start_date: start,              end_date: end          }          $('span#sr_total_sales').html(__fa_awesome());          $.ajax({              method: "GET",              url: '/reports/sales-representative-total-sell',              dataType: "json",              data: data_expense,              success: function (data) {                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));              }          });      }      function employeeTotalCollections() {          var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');          var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');          var data_expense = {              created_by: $('select#sr_id').val(),              location_id: $('select#sr_business_id').val(),              start_date: start,              end_date: end          }          $('span#sr_total_sales').html(__fa_awesome());          $.ajax({              method: "GET",              url: '/reports/sales-representative-total-sell',              dataType: "json",              data: data_expense,              success: function (data) {                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));              }          });      }      function employeeTotalMeetings() {          var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');          var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');          var data_expense = {              created_by: $('select#sr_id').val(),              location_id: $('select#sr_business_id').val(),              start_date: start,              end_date: end          }          $('span#sr_total_sales').html(__fa_awesome());          $.ajax({              method: "GET",              url: '/reports/sales-representative-total-sell',              dataType: "json",              data: data_expense,              success: function (data) {                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));              }          });      }  </script>@endsection