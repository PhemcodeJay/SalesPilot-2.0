if (jQuery("#apex-basic").length) {
    $.ajax({
      url: 'path/to/your/php/script.php', // Replace with the actual path to your PHP script
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        const salesData = response.apexBasicChart.map(item => item.total_sales);
        const salesDates = response.apexBasicChart.map(item => item.date);
  
        const options = {
          chart: {
            height: 350,
            type: "line",
            zoom: {
              enabled: false
            }
          },
          colors: ["#4788ff"],
          series: [{
            name: "Sales Quantity",
            data: salesData
          }],
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: "straight"
          },
          title: {
            text: "Sales Trends by Date",
            align: "left"
          },
          grid: {
            row: {
              colors: ["#f3f3f3", "transparent"],
              opacity: 0.5
            }
          },
          xaxis: {
            categories: salesDates
          }
        };
  
        if (typeof ApexCharts !== typeof undefined) {
          const chart = new ApexCharts(document.querySelector("#apex-basic"), options);
          chart.render();
  
          const body = document.querySelector('body');
          if (body.classList.contains('dark')) {
            apexChartUpdate(chart, {
              dark: true
            });
          }
  
          document.addEventListener('ChangeColorMode', function(e) {
            apexChartUpdate(chart, e.detail);
          });
        }
      },
      error: function(error) {
        console.error("Error fetching sales data: ", error);
      }
    });
  }

  


  // chart-custom.js

if (jQuery("#apex-line-area").length) {
    $.ajax({
      url: 'path/to/your/php/script.php', // Replace with the actual path to your PHP script
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        const sellThroughData = response.apexLineAreaChart.map(item => item.avg_sell_through_rate);
        const inventoryTurnoverData = response.apexLineAreaChart.map(item => item.avg_inventory_turnover_rate);
        const dates = response.apexLineAreaChart.map(item => item.date);
  
        const options = {
          chart: {
            height: 350,
            type: "area"
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: "smooth"
          },
          colors: ["#4788ff", "#ff4b4b"],
          series: [{
            name: "Sell-Through Rate",
            data: sellThroughData
          }, {
            name: "Inventory Turnover Rate",
            data: inventoryTurnoverData
          }],
          xaxis: {
            type: "datetime",
            categories: dates
          },
          tooltip: {
            x: {
              format: "dd/MM/yy"
            }
          }
        };
  
        const chart = new ApexCharts(document.querySelector("#apex-line-area"), options);
        chart.render();
  
        const body = document.querySelector('body');
        if (body.classList.contains('dark')) {
          apexChartUpdate(chart, {
            dark: true
          });
        }
  
        document.addEventListener('ChangeColorMode', function(e) {
          apexChartUpdate(chart, e.detail);
        });
      },
      error: function(error) {
        console.error("Error fetching sell-through rate and inventory turnover data: ", error);
      }
    });
  }

  

  // chart-custom.js

if (jQuery("#apex-column").length) {
    $.ajax({
      url: 'path/to/your/php/script.php', // Replace with the actual path to your PHP script
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        const revenueData = response.apex3ColumnChart.map(item => item.revenue);
        const totalExpensesData = response.apex3ColumnChart.map(item => item.total_expenses);
        const profitData = response.apex3ColumnChart.map(item => item.profit);
        const dates = response.apex3ColumnChart.map(item => item.date);
  
        const options = {
          chart: {
            height: 350,
            type: "bar"
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: "55%",
              endingShape: "rounded"
            }
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            show: true,
            width: 2,
            colors: ["transparent"]
          },
          colors: ["#4788ff", "#37e6b0", "#ff4b4b"],
          series: [{
            name: "Revenue",
            data: revenueData
          }, {
            name: "Total Expenses",
            data: totalExpensesData
          }, {
            name: "Net Profit",
            data: profitData
          }],
          xaxis: {
            categories: dates
          },
          yaxis: {
            title: {
              text: "$ (thousands)"
            }
          },
          fill: {
            opacity: 1
          },
          tooltip: {
            y: {
              formatter: function(value) {
                return "$ " + value + " thousands";
              }
            }
          }
        };
  
        const chart = new ApexCharts(document.querySelector("#apex-column"), options);
        chart.render();
  
        const body = document.querySelector('body');
        if (body.classList.contains('dark')) {
          apexChartUpdate(chart, {
            dark: true
          });
        }
  
        document.addEventListener('ChangeColorMode', function(e) {
          apexChartUpdate(chart, e.detail);
        });
      },
      error: function(error) {
        console.error("Error fetching revenue, total expenses, and profit data: ", error);
      }
    });
  }

  


  if(jQuery('#am-3dpie-chart').length){
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        $.ajax({
            url: 'path/to/your/php/script.php', // Replace with the actual path to your PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var chart = am4core.create("am-3dpie-chart", am4charts.PieChart3D);
                chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                chart.legend = new am4charts.Legend();

                chart.data = response.top5RevenueProducts.map(function(item) {
                    return {
                        product: item.product_name,
                        revenue: item.revenue
                    };
                });

                var series = chart.series.push(new am4charts.PieSeries3D());
                series.colors.list = [
                    am4core.color("#4788ff"),
                    am4core.color("#37e6b0"),
                    am4core.color("#ff4b4b"),
                    am4core.color("#fe721c"),
                    am4core.color("#876cfe")
                ];
                series.dataFields.value = "revenue";
                series.dataFields.category = "product";

                const body = document.querySelector('body');
                if (body.classList.contains('dark')) {
                    amChartUpdate(chart, {
                        dark: true
                    });
                }

                document.addEventListener('ChangeColorMode', function(e) {
                    amChartUpdate(chart, e.detail);
                });
            },
            error: function(error) {
                console.error("Error fetching top 5 revenue by product data: ", error);
            }
        });
    }); // end am4core.ready()
}
