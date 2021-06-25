
<canvas id="mycanvas" class="canvas"></canvas>

<script src="{{asset('assets/plugins/chartjs/chartjs.min.js')}}"></script>
<script>
  let product_names = @json($product_name);
  let product_qty_sum = {{$sum}};
  let units = @json($units); 

  let barChartData = {
    labels: product_names,
    datasets: [{
        label: 'Quantity',
        backgroundColor: "rgba(14,118,118,1)",
        data: product_qty_sum,
        barPercentage: 0.5,
        barThickness: 6,
        maxBarThickness: 8,
        minBarLength: 2,
    },]
  };

  (function() {
    let ctx = document.getElementById("mycanvas").getContext("2d");
    window.myBar = new Chart(ctx, {
      type: 'bar',
      data: barChartData,
      options: {
        tooltips: {
          mode: 'label',
          callbacks: {
            label: function(tooltipItem, data) {
              return data.datasets[tooltipItem.datasetIndex].label + ": " + tooltipItem.yLabel+" "+units[tooltipItem.index];
            },
          }
        },
        legend: {
          labels: {
          fontColor: 'red'
          }
        },
        elements: {
          rectangle: {
              borderWidth: 2,
              borderColor: 'rgb(0, 255, 0)',
              borderSkipped: 'bottom'
          }
        },
        responsive: true,
        title: {
          display: true,
          text: 'Top 10 Most Retuned Products',
          fontStyle: 'bold',
          fontSize: 20,
        },
        scales: {
          xAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: 'Product Names, Variant',
              fontStyle: "bold",
              fontSize: 20,
            },
          }],
          yAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: 'Quantity',
              fontStyle: "bold",
              fontSize: 20,
            },
            ticks: {
              beginAtZero: true,
              steps: product_qty_sum[product_qty_sum.length-1]*10,
              max: product_qty_sum[0] + 300,
            }
          }],
        },
      }
    });
  }());
</script>


