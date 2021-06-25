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



/* #small-box-scroll{

  width: 1000px;

  overflow-x: scroll;

  overflow-y: hidden;

  white-space: nowrap;

} */

/* }

.small-box{

  width: 200;

  display:inline-block;

  margin-right: 15px;

padding:0 10px;



}

.inner p{  word-break: break-all;} */



</style>



<div class="row">

  <div class="col-xs-12">

    <div class="box-body" id="mainBox">

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

    </div>

  </div>

</div>



<div class="row">

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="total-orders">0</h4>            

        <p>Total Orders</p>

      </div>

    </div>

  </div>

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="total-order-values">0</h4>

        <p>Total Order Values</p>

      </div>

    </div>

  </div>

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="total-pending-collection-amount">0</h4>

        <p>Cheque to be deposited</p>

      </div>

    </div>

  </div>

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="total-cleared-collection-amount">0</h4>

        <p>Total Collection Amount</p>

      </div>

    </div>

  </div>

</div>



<div class="row">

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="products-sold">0</h4>            

        <p>Products Sold</p>

      </div>

    </div>

  </div>

  <div class="col-xs-3">

    <div class="small-box">

      <div class="inner">

        <h4 id="last-visited-date">Date</h4>

        <p>Last Order Taken</p>

      </div>

    </div>

  </div>

</div>



{{-- <div class="col-md-12" style="padding: 15px;">

  <div id="small-box-scroll" class="clearfix ml-2"></div>

  </div>

</div> --}}



<div class="row">

  <div class="col-xs-12">

    <!-- Custom Tabs -->

    <div class="nav-tabs-custom">

      <ul class="nav nav-tabs" id="tab-list">

        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true" class="open-tab" data-tabRoute="get-no-of-orders" data-chartId="order-number-chart">Total no of orders</a></li>

        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false" class="open-tab" data-tabRoute="get-order-value" data-chartId="order-value-chart">Total order value</a></li>

        <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false" class="open-tab" data-tabRoute="get-collection-amount" data-chartId="collection-amount-chart">Total collection amount</a></li>

      </ul>

      <div class="tab-content">

        <div class="tab-pane active" id="tab_1">

          <div class="col-xs-12">

            <!-- LINE CHART -->

            <div class="box">

              <div class="box-body">

                <div id="order-number-chart" style="width:100%; height:400px;"></div>

              </div>

              <!-- /.box-body -->

            </div>

            <!-- /.box -->

          </div>

        </div>

        <!-- /.tab-pane -->

        <div class="tab-pane" id="tab_2">

          <div class="col-xs-12">

            <!-- LINE CHART -->

            <div class="box">

              <div class="box-body">

                <div id="order-value-chart" style="width:100%; height:400px;"></div>

              </div>

              <!-- /.box-body -->

            </div>

            <!-- /.box -->

          </div>

        </div>

        <!-- /.tab-pane -->

        <div class="tab-pane" id="tab_3">

          <div class="col-xs-12">

            <!-- LINE CHART -->

            <div class="box">

              <div class="box-body">

                <div id="collection-amount-chart" style="width:100%; height:400px;"></div>

              </div>

              <!-- /.box-body -->

            </div>

            <!-- /.box -->

          </div>

        </div>

        <!-- /.tab-pane -->

      </div>

      <!-- /.tab-content -->

    </div>

    <!-- nav-tabs-custom -->

  </div>

</div>



@section('analytics-scripts')



  <script src="{{ asset('assets/bower_components/momentjs/moment.js') }}"></script>

  <script src="{{ asset('assets/bower_components/highchart.js') }}"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>



  <script>



    jQuery(function($) {



      let pageUrl = window.location.href,

        id = pageUrl.substr(pageUrl.indexOf("client/") + 7);



      @if(config('settings.ncal')==0)

        let start = moment(),

          end = moment();

        $(document).on('click',".applyBtn",function (e) {
            e.preventDefault();
            date = $(".drp-selected").html();
            dateArray = date.split(" - ");
            $('#reportrange span').html(moment(dateArray[0]).format('MMM D, YYYY') + ' - ' + moment(dateArray[1]).format('MMM D, YYYY'));
            startDate = moment(dateArray[0]).format("YYYY-MM-DD");
            endDate = moment(dateArray[1]).format("YYYY-MM-DD");
            countPartiesParameters(id, startDate, endDate, type="");
            getGraphs(id, startDate, endDate, type="");
        });



        countPartiesParameters(id, startDate="", endDate="", type="");

        getGraphs(id, startDate="", endDate="", type="");



        // function to show Date Range in HTML DOM

        function dateFilter(start, end,check) {

          if(start.format('YYYY-MM-DD')>end.format('YYYY-MM-DD') || check==true){

            $('#reportrange span').html("All Time");

          }else if(end.isValid() == false) {

            $('#reportrange span').html(start.format('MMM D, YYYY'));

          }else {

            $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

          }

        }



        //DateRange Picker

        $('#reportrange').daterangepicker({

          startDate: (start) ? start : null,
          endDate: (end) ? end : null,

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

              countPartiesParameters(id, startDate="", endDate="", type="");

              getGraphs(id, startDate="", endDate="", type="");

              break;



            case "Today":

              startDate = moment().format("YYYY-MM-DD");

              endDate = moment().format("YYYY-MM-DD")

              

              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");



              break;



            case "Yesterday":

              startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");

              endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");

              

              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");

              break;



            case "Last 7 Days":

              startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');

              endDate = moment().format('YYYY-MM-DD');



              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");

              break;



            case "Last 30 Days":

              startDate = moment(moment().subtract(29, 'days')).format('YYYY-MM-DD');

              endDate = moment().format('YYYY-MM-DD');



              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");

              break;



            case "This Month":

              startDate = moment(moment().startOf('month')).format('YYYY-MM-DD');

              endDate = moment().format("YYYY-MM-DD");



              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");

              break;



            case "Last Month":

              startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');

              endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');



              countPartiesParameters(id, startDate, endDate, type="");

              getGraphs(id, startDate, endDate, type="");

              break;



            case "Custom Range":

              $(".applyBtn").click(function () {

                date = $(".drp-selected").html();

                dateArray = date.split(" - ");



                startDate = moment(dateArray[0]).format("YYYY-MM-DD");

                endDate = moment(dateArray[1]).format("YYYY-MM-DD");



                countPartiesParameters(id, startDate, endDate, type="");

                getGraphs(id, startDate, endDate, type="");

              });

              break;

          

            default:

              break;

          }

        });

      @else

        $.get("{{ route('get-client-first-date') }}", {id}, function(response) {

          let start = response.data,
          end = moment();

          $("#start-ndate").attr("value", AD2BS(moment(start).format("YYYY-MM-DD")));
          $("#end-ndate").attr("value", AD2BS(moment(end).format("YYYY-MM-DD")));

          countPartiesParameters(id, moment(start).format("YYYY-MM-DD"), moment(end).format("YYYY-MM-DD"), type="nepali");
          getGraphs(id, moment(start).format("YYYY-MM-DD"), moment(end).format("YYYY-MM-DD"), type="nepali");

        });
        
        $(".nepali-date").nepaliDatePicker();

        $("#submit-nepali-date").on('click', function() {

          startDate = BS2AD($("#start-ndate").val());

          endDate = BS2AD($("#end-ndate").val());

          countPartiesParameters(id, startDate, endDate, type="nepali");

          getGraphs(id, startDate, endDate, type="nepali");

        });

      @endif



      function daysDiff(d1, d2) {

        const diffTime = Math.abs(d2 - d1);

        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return diffDays;

      }



      function countPartiesParameters(id, startDate, endDate, type) {

        $.get("{{ route('count-parties-parameters') }}", {id, start_date: startDate, end_date: endDate}, function(response) {

          $("#total-orders").html(response.data.total_orders);

          $("#total-order-values").html(response.data.currency_symbol+ " " + response.data.total_order_value);

          $("#total-pending-collection-amount").html(response.data.currency_symbol+ " " + response.data.total_pending_collection_amount);

          $("#total-cleared-collection-amount").html(response.data.currency_symbol+ " " + response.data.total_cleared_collection_amount);

          $("#products-sold").html(response.data.products_sold);

          if (type == "nepali") {

             if(response.data.last_visited=='No orders yet'){

               $("#last-visited-date").html('No orders yet');

            }else{

            $("#last-visited-date").html(AD2BS(response.data.last_visited));

            }

          } else {

            $("#last-visited-date").html(response.data.last_visited);

          }

        });

      }



      function getGraphs(id, startDate, endDate, type) {

        let orderDates = [],

          orderValues = [],

          orderValueDates = [],

          orderValueValues = [],

          collectionDates = [],

          collectionValues = [];



        let diffInDays = daysDiff(new Date(startDate), new Date(endDate));



        $.ajax({
          url:"{{ route('get-no-of-orders-for-parties') }}",
          type:"GET",
          data:{
            id, 
            start_date:startDate, 
            end_date:endDate, type
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

        $.get("{{ route('get-order-value-for-parties') }}", {id, start_date:startDate, end_date:endDate, type}, function(response) {

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

        $.get("{{ route('get-collection-amount-for-parties') }}", {id, start_date:startDate, end_date:endDate, type}, function(response) {

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



      function orderNumberGraph(dates, values, currencySymbol) {

        Highcharts.setOptions({

          lang: {

            thousandsSep: ','

          }

        });

        Highcharts.chart('order-number-chart', {

          title: {

            text: 'Total no of orders'

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
              }

            },

            line: {

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

                maxWidth: 500

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

              text: 'Order Values'

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
              }

            },

            line: {

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

                maxWidth: 500

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
              }

            },

            line: {

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

                maxWidth: 500

              },

            }]

          }



        });



      }

      

    });



  </script>

    

@endsection