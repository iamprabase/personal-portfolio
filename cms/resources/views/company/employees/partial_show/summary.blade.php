<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<style>

.line-chart-height {
  height: 250px !important;
}

.small-box {
  background-color: #0b7676 !important;
  color: #ffffff;
} 

.highcharts-credits {
  display: none;
}

#reportrange {
  cursor: pointer;
  padding: 10px;
} 

</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box" id="mainBox">
          <div class="box-header">
            <div class="row">
              @if(config('settings.ncal')==0)
                <div class="col-xs-6">
                  <div id="reportrange" name="reportrange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>
                </div>
              @else
                <div class="col-xs-6">
                  <div class="input-group" id="nepCalDiv">
                    <input id="start-ndate" class="form-control nepali-date" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off" />
                    <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end-ndate" class="form-control nepali-date" type="text" name="end_ndate" placeholder="End Date" autocomplete="off" />
                  </div>
                </div>
                <div class="col-xs-6">
                  <button type="button" class="btn btn-sm btn-success" id="submit-nepali-date">Submit</button>
                </div>
              @endif
            </div>
          </div>
          <hr>

          <div class="row">
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-orders">0</h3>            
                  <p>Total Orders</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-zero-orders">0</h3>
                  <p>Total Zero Orders</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-order-value">0</h3>
                  <p>Total Order Value</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="average-order-value">0</h3>            
                  <p>Average Order Value</p>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-pending-collection-amount">0</h3>            
                  <p>Cheque to be deposited</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-cleared-collection-amount">0</h3>            
                  <p>Total Collection Amount</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-expense">0</h3>
                  <p>Approved Expenses</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="days-present">0</h3>
                  <p>Present Days</p>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="leaves">0</h3>
                  <p>Leaves</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="scheduled-effective-calls">0</h3>
                  <p>Scheduled Effective Calls</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="unscheduled-effective-calls">0</h3>
                  <p>Unscheduled Effective Calls</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="total-effective-calls">0</h3>            
                  <p>Total Effective Calls</p>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="target-visits">0</h3>            
                  <p>Target Calls</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="parties-added">0</h3>
                  <p>Parties Added</p>
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="small-box">
                <div class="inner">
                  <h3 id="average-working-hours">0</h3>            
                  <p>Average Daily Working Hours</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <div id="order-number-chart" style="width:100%; height:400px;"></div>
            </div>
            <div class="col-xs-6">
              <div id="order-value-chart" style="width:100%; height:400px;"></div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-xs-6">
              <div id="collection-amount-chart" style="width:100%; height:400px;"></div>
            </div>
            <div class="col-xs-6">
              <div class="nav-tabs-custom client-tab" id="employeetabs">
                <ul class="nav nav-pills" id="tabs">
                  <li class="active"><a href="#order-based-tab" name="order_based_tab" data-toggle="tab">Order Based</a></li>
                  <li><a href="#collection-based-tab" name="collection_based_tab" data-toggle="tab">Collection Based</a></li>
                </ul>
                <div class="tab-content">
                  <div class="active tab-pane" id="order-based-tab">
                    <div id="top-ten-parties" style="width:100%; height:400px;"></div>
                  </div>
                  <div class="tab-pane" id="collection-based-tab">
                    <div id="top-ten-parties-collection" style="width:100%; height:400px;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

    </div>
</div>

@section('analytics-scripts')

    <script src="{{ asset('assets/bower_components/momentjs/moment.js') }}"></script>
    <script src="{{ asset('assets/bower_components/highchart.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <script>
    
        jQuery(function($) {
      
          let page_url = $(location).attr('href'),
            id = page_url.substr(page_url.indexOf("ee/")+3);

          @if(config('settings.ncal')==0)
            let start = moment(),
              end = moment();
            
            countParameters(id, startDate="", endDate="", type="");
            getGraphs(id, startDate="", endDate="", type="");
            getTopTenParties(id, startDate="", endDate="", type="");

            $(document).on('click',".applyBtn",function () {
              date = $(".drp-selected").html();
              dateArray = date.split(" - ");
              $('#reportrange span').html(moment(dateArray[0]).format('MMM D, YYYY') + ' - ' + moment(dateArray[1]).format('MMM D, YYYY'));
              startDate = moment(dateArray[0]).format("YYYY-MM-DD");
              endDate = moment(dateArray[1]).format("YYYY-MM-DD");
              countParameters(id, startDate, endDate, type="");
              getGraphs(id, startDate, endDate, type="");
              getTopTenParties(id, startDate, endDate, type="");
            });

            function dateFilter(start, end,check) {
              if(start.format('YYYY-MM-DD')>end.format('YYYY-MM-DD') || check==true){
                $('#reportrange span').html("All Time");
                check=false;
              }else if(end.isValid() == false) {
                $('#reportrange span').html(start.format('MMM D, YYYY'));
              }else {
                $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
              }
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                  'All Time': [moment().add(30,'days'),moment().subtract(30,'days')],
                  'Today': [moment(), null],
                  'Yesterday': [moment().subtract(1, 'days'), null],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, dateFilter);
            var check = true;

            dateFilter(start, end,check);

            $(".ranges ul li").on('click', function() {
              let dates;
              let dataRange = $(this).attr('data-range-key');
              switch (dataRange) {
                case "All Time":
                  countParameters(id, startDate="", endDate="", type="");
                  getGraphs(id, startDate="", endDate="", type="");
                  getTopTenParties(id, startDate="", endDate="", type="");
                  break;

                case "Today":
                  startDate = moment().format("YYYY-MM-DD");
                  endDate = moment().format("YYYY-MM-DD")
                  
                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");

                  break;

                case "Yesterday":
                  startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
                  endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
                  
                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");
                  break;

                case "Last 7 Days":
                  startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');
                  endDate = moment().format('YYYY-MM-DD');

                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");
                  break;

                case "Last 30 Days":
                  startDate = moment(moment().subtract(29, 'days')).format('YYYY-MM-DD');
                  endDate = moment().format('YYYY-MM-DD');

                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");
                  break;

                case "This Month":
                  startDate = moment(moment().startOf('month')).format('YYYY-MM-DD');
                  endDate = moment().format("YYYY-MM-DD");

                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");
                  break;

                case "Last Month":
                  startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
                  endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');

                  countParameters(id, startDate, endDate, type="");
                  getGraphs(id, startDate, endDate, type="");
                  getTopTenParties(id, startDate, endDate, type="");
                  break;

                case "Custom Range":
                  $(".applyBtn").click(function () {
                    date = $(".drp-selected").html();
                    dateArray = date.split(" - ");

                    startDate = moment(dateArray[0]).format("YYYY-MM-DD");
                    endDate = moment(dateArray[1]).format("YYYY-MM-DD");

                    countParameters(id, startDate, endDate, type="");
                    getGraphs(id, startDate, endDate, type="");
                    getTopTenParties(id, startDate, endDate, type="");
                  });
                  break;
              
                default:
                  break;
              }
            });
          @else

            $.get("{{ route('get-employee-first-date') }}", {id}, function(response) {
              let start = response.data,
                end = moment();

              $("#start-ndate").attr("value", AD2BS(moment(start).format("YYYY-MM-DD")));
              $("#end-ndate").attr("value", AD2BS(moment(end).format("YYYY-MM-DD")));

              countParameters(id, moment(start).format("YYYY-MM-DD"), moment(end).format("YYYY-MM-DD"), type="nepali");
              getGraphs(id, moment(start).format("YYYY-MM-DD"), moment(end).format("YYYY-MM-DD"), type="nepali");
              getTopTenParties(id, moment(start).format("YYYY-MM-DD"), moment(end).format("YYYY-MM-DD"), type="nepali");

            });
            
            $(".nepali-date").nepaliDatePicker();
            $("#submit-nepali-date").on('click', function() {
              startDate = BS2AD($("#start-ndate").val());
              endDate = BS2AD($("#end-ndate").val());
              countParameters(id, startDate, endDate, type="nepali");
              getGraphs(id, startDate, endDate, type="nepali");
              getTopTenParties(id, startDate, endDate, type="nepali");
            });
          @endif

          function daysDiff(d1, d2) {
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
          }
          
          function getGraphs(id, startDate, endDate, type) {
            let orderDates = [],
              orderValues = [],
              orderValueDates = [],
              orverValueValues = [],
              collectionDates = [],
              collectionValues = [];

            let diffInDays = daysDiff(new Date(startDate), new Date(endDate));

            $.ajax({
              url:"{{ route('get-no-of-orders') }}",
              type:"GET",
              data:{
                id, 
                start_date: startDate, 
                end_date: endDate, type
              },
              beforeSend:function() {
                $('#mainBox').addClass('box-loader');
                $('#loader1').removeAttr('hidden');
              },
              success:function(response) {
                if (type == undefined || type == "") {
                  orderDates = response.data.dates;
                  orderValues = response.data.values;
                } else {
                  if(diffInDays > 45) {
                    orderDates = response.data.dates;
                  } else {
                    for (let index = 0; index < response.data.dates.length; index++) {
                      const element = response.data.dates[index];
                      orderDates.push(AD2BS(element));
                    }
                  }
                  orderValues = response.data.values;
                }
                orderNumberGraph(orderDates, orderValues, response.data.currency_symbol);
              },
              complete:function() {
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
              }
            });
            $.get("{{ route('get-order-value') }}", {id, start_date: startDate, end_date: endDate, type}, function(response) {
              if (type == undefined || type == "") {
                orderValueDates = response.data.dates;
                orderValueValues = response.data.values;
              } else {
                if (diffInDays > 45) {
                  orderValueDates = response.data.dates;
                } else {
                  for (let index = 0; index < response.data.dates.length; index++) {
                    const element = response.data.dates[index];
                    orderValueDates.push(AD2BS(element));
                  }
                }
                orderValueValues = response.data.values;
              }
              orderValueGraph(orderValueDates, orderValueValues, response.data.currency_symbol);
            });
            $.get("{{ route('get-collection-amount') }}", {id, start_date: startDate, end_date: endDate, type}, function(response) {
              if (type == undefined || type == "") {
                collectionDates = response.data.dates;
                collectionValues = response.data.values;
              } else {
                if (diffInDays > 45) {
                  collectionDates = response.data.dates; 
                } else {
                  for (let index = 0; index < response.data.dates.length; index++) {
                    const element = response.data.dates[index];
                    collectionDates.push(AD2BS(element));
                  }
                }
              }
              collectionValues = response.data.values;
              collectionAmountGraph(collectionDates, collectionValues, response.data.currency_symbol);
            });
          }
 
          function countParameters(id, startDate, endDate, type) {
            $.get("{{ route('count-parameters') }}", {id, start_date: startDate, end_date: endDate}, function(response) {
              $("#total-orders").html(response.data['total_orders']);
              $("#total-zero-orders").html(response.data['total_zero_orders']);
              $("#total-order-value").html(response.data['currency_symbol'] + " " + response.data['total_order_value']);
              $("#average-order-value").html(response.data['currency_symbol'] + " " + response.data['average_order_value']); 
              $("#total-pending-collection-amount").html(response.data['currency_symbol'] + " " + response.data['total_pending_collection_amount']);
              $("#total-cleared-collection-amount").html(response.data['currency_symbol'] + " " + response.data['total_cleared_collection_amount']);
              $("#total-expense").html(response.data['currency_symbol'] + " " + response.data['total_expense']);
              $("#days-present").html(response.data['present_days'] + " days");
              let leaves = (response.data['leaves'] != null) ? response.data['leaves'] : 0;
                leaves = (leaves == 1) ? "1 day" : leaves+" days";
              $("#leaves").html(leaves);
              $("#scheduled-effective-calls").html(response.data['scheduled_effective_calls']);
              $("#unscheduled-effective-calls").html(response.data['unscheduled_effective_calls']);
              total_effective_calls = parseInt(response.data['scheduled_effective_calls']) + parseInt(response.data['unscheduled_effective_calls']);
              $("#total-effective-calls").html(total_effective_calls);
              $("#target-visits").html(response.data['total_target_clients']);
              $("#parties-added").html(response.data['parties_added']);
              $("#average-working-hours").html(response.data['average_working_hours']);
            });
          }

          function getTopTenParties(id, startDate, endDate) {
            $.get("{{ route('top-parties') }}", {id, start_date: startDate, end_date: endDate}, function(response) {
              if (response.data.order_sum.every(item => item === 0)) {
                $("#top-ten-parties").replaceWith(`<div id="escape-chart-1" class="text-center mt-4"><h3>Top Parties</h3><br><br><h4>No Data Found</h4></h4></div>`);
              } else {
                $("#escape-chart-1").replaceWith(`<div id="top-ten-parties" style="width:100%; height:400px;"></div>`);
                topTenParties(response.data.client_name, response.data.order_sum, response.data.currency_symbol); 
              }
              if(response.data.collection_sum.every(item => item === 0)) {
                $("#top-ten-parties-collection").replaceWith(`<div id="escape-chart-2" class="text-center mt-4"><h3>Top Parties</h3><br><br><h4>No Data Found</h4></h4></div>`);
              } else {
                $("#escape-chart-2").replaceWith(`<div id="top-ten-parties-collection" style="width:100%; height:400px;"></div>`);
                topTenPartiesCollection(response.data.client_name_for_collection, response.data.collection_sum, response.data.currency_symbol);
              }
            });
          }

          function orderNumberGraph(dates, values, currencySymbol) {
            Highcharts.setOptions({
                lang: {
                    thousandsSep: ','
                }
            });
            Highcharts.chart('order-number-chart', {
              title: {
                text: 'Total no of orders',
              },
              yAxis: {
                title: {
                  text: 'Number of Orders'
                }
              },
              xAxis: {
                categories: dates,
                labels: {
                  enabled: true,
                }
              },
              legend: {
                enabled: false
              },
              plotOptions: {
                series: {
                  label: {
                    connectorAllowed: true
                  },
                  marker: {
                    enabled: true,
                    radius: 3
                  },
                },
                line: {
                  color: '#0b7676'
                }
              },
              series: [{
                name: currencySymbol,
                data: values,
              }],

              responsive: {
                rules: [{
                  condition: {
                    maxWidth: 1366
                  },
                }]
              }

            });
 
          }

          function orderValueGraph(dates, values, currencySymbol) {
            Highcharts.setOptions({
              lang: {
                thousandsSep: ','
              }
            });
            Highcharts.chart('order-value-chart', {
              title: {
                text: 'Total Order Value'
              },
              yAxis: {
                title: {
                  text: 'Orders value'
                },
                allowDecimals: false,
              },
              xAxis: {
                categories: dates,
                labels: {
                  enabled: true,
                }
              },
              tooltip: {
                valueDecimals: 2, 
              }, 
              legend: {
                enabled: false
              },
              plotOptions: {
                series: {
                  label: {
                    connectorAllowed: true
                  },
                  marker: {
                    enabled: true,
                    radius: 3
                  }
                },
                line: {
                  color: '#0b7676',
                }
              },
              series: [{
                name: currencySymbol,
                data: values
              }],

              responsive: {
                rules: [{
                  condition: {
                    maxWidth: 1366
                  },
                }]
              }
            
            });
 
          }

          function collectionAmountGraph(dates, values, currencySymbol) {
            Highcharts.setOptions({
              lang: {
                thousandsSep: ','
              }
            });
            Highcharts.chart('collection-amount-chart', {
              title: {
                text: 'Total Collection Amount'
              },
              yAxis: {
                title: {
                  text: 'Collection Amount'
                },
                allowDecimals: false,
              },
              xAxis: {
                categories: dates,
                labels: {
                  enabled: true,
                }
              },
              tooltip: {
                valueDecimals: 2, 
              }, 
              legend: {
                enabled: false
              },
              plotOptions: {
                series: {
                  label: {
                    connectorAllowed: true
                  },
                  marker: {
                    enabled: true,
                    radius: 3
                  }
                },
                line: {
                  color: '#0b7676'
                },
              },
              series: [{
                name: currencySymbol,
                data: values
              }],
              responsive: {
                rules: [{
                  condition: {
                    maxWidth: 1366
                  },
                }]
              }

            });
 
          }

          function topTenParties(parties, values, currencySymbol) {
            Highcharts.setOptions({
              lang: {
                thousandsSep: ','
              }
            });
            Highcharts.chart('top-ten-parties', {
              chart: {
                type: 'column'
              },
              title: {
                text: 'Top Parties'
              },
              xAxis: {
                categories: parties,
              },
              yAxis: {
                title: {
                  text: 'Order Value'
                },
                allowDecimals: false,
              },
              legend: {
                enabled: false
              },
              plotOptions: {
                column: {
                  pointWidth: 20,
                  color: '#0b7676'
                }
              },
              series: [{
                name: currencySymbol,
                data: values
              }],
              responsive: {
                rules: [{
                  condition: {
                    maxWidth: 1366
                  },
                }]
              }
            });
          }

          function topTenPartiesCollection(parties, values, currencySymbol) {
            Highcharts.chart('top-ten-parties-collection', {
              chart: {
                type: 'column'
              },
              title: {
                text: 'Top Parties'
              },
              xAxis: {
                categories: parties,
              },
              yAxis: {
                title: {
                  text: 'Collection Amount'
                },
                allowDecimals: false,
              },
              legend: {
                enabled: false
              },
              plotOptions: {
                column: {
                  pointWidth: 20,
                  color: '#0b7676'
                }
              },
              series: [{
                name: currencySymbol,
                data: values
              }],
              responsive: {
                rules: [{
                  condition: {
                    maxWidth: 1366
                  },
                }]
              }
            });
          }
          
        });    

    </script>

@endsection