$(document).ready(function() {
    // Function to render Apex Charts
    function renderApexChart(selector, options) {
        if (typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(document.querySelector(selector), options);
            chart.render();
  
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) {
                if (typeof apexChartUpdate === 'function') {
                    apexChartUpdate(chart, { dark: true });
                }
            }
  
            document.addEventListener('ChangeColorMode', function(e) {
                if (typeof apexChartUpdate === 'function') {
                    apexChartUpdate(chart, e.detail);
                }
            });
        } else {
            console.error("ApexCharts is not defined.");
        }
    }
  
    // Function to fetch data and render charts
    function fetchChartData(url, selector, chartOptionsCallback) {
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const options = chartOptionsCallback(response);
                renderApexChart(selector, options);
            },
            error: function(error) {
                console.error("Error fetching data: ", error);
                $(selector).html("<p>Failed to load data.</p>"); // Display error message
            }
        });
    }
  
    // Chart 1: Basic Sales Trends
    if ($("#apex-basic").length) {
        fetchChartData('chart-data.php', '#apex-basic', function(response) {
            if (!response.apexBasicChart) {
                console.error("Missing apexBasicChart data");
                return;
            }
            const salesData = response.apexBasicChart.map(item => item.total_sales);
            const salesDates = response.apexBasicChart.map(item => item.date);
            return {
                chart: {
                    height: 350,
                    type: "line",
                    zoom: { enabled: false }
                },
                colors: ["#4788ff"],
                series: [{
                    name: "Sales Quantity",
                    data: salesData
                }],
                dataLabels: { enabled: false },
                stroke: { curve: "straight" },
                title: { text: "Sales Trends by Date", align: "left" },
                grid: {
                    row: {
                        colors: ["#f3f3f3", "transparent"],
                        opacity: 0.5
                    }
                },
                xaxis: { categories: salesDates }
            };
        });
    }
  
    // Chart 2: Sell-Through and Inventory Turnover Rates
    if ($("#apex-line-area").length) {
        fetchChartData('chart-data.php', '#apex-line-area', function(response) {
            if (!response.apexLineAreaChart) {
                console.error("Missing apexLineAreaChart data");
                return;
            }
            const sellThroughData = response.apexLineAreaChart.map(item => item.avg_sell_through_rate);
            const inventoryTurnoverData = response.apexLineAreaChart.map(item => item.avg_inventory_turnover_rate);
            const dates = response.apexLineAreaChart.map(item => item.date);
            return {
                chart: {
                    height: 350,
                    type: "area"
                },
                dataLabels: { enabled: false },
                stroke: { curve: "smooth" },
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
        });
    }
  
    // Chart 3: Revenue, Total Expenses, and Net Profit
    if ($("#apex-column").length) {
        fetchChartData('chart-data.php', '#apex-column', function(response) {
            if (!response.apex3ColumnChart) {
                console.error("Missing apex3ColumnChart data");
                return;
            }
            const revenueData = response.apex3ColumnChart.map(item => item.revenue);
            const totalExpensesData = response.apex3ColumnChart.map(item => item.total_expenses);
            const profitData = response.apex3ColumnChart.map(item => item.profit);
            const dates = response.apex3ColumnChart.map(item => item.date);
            return {
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
                dataLabels: { enabled: false },
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
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return "$ " + value + " thousands";
                        }
                    }
                }
            };
        });
    }
  
    // Chart 4: Top 5 Revenue Products (AmCharts)
    if ($('#am-3dpie-chart').length) {
        if (typeof am4core !== 'undefined') {
            am4core.ready(function() {
                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end
  
                fetchChartData('chart-data.php', '#am-3dpie-chart', function(response) {
                    if (!response.top5RevenueProducts) {
                        console.error("Missing top5RevenueProducts data");
                        return;
                    }
  
                    var chart = am4core.create("am-3dpie-chart", am4charts.PieChart3D);
                    chart.hiddenState.properties.opacity = 0; // This creates an initial fade-in
  
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
                        if (typeof amChartUpdate === 'function') {
                            amChartUpdate(chart, { dark: true });
                        }
                    }
  
                    document.addEventListener('ChangeColorMode', function(e) {
                        if (typeof amChartUpdate === 'function') {
                            amChartUpdate(chart, e.detail);
                        }
                    });
                });
            });
        } else {
            console.error("am4core is not defined.");
        }
    }
  });
  