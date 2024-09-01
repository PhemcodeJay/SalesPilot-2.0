if (jQuery('#am-layeredcolumn-chart').length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'monthly' // Adjust this based on your requirement (weekly, monthly, yearly)
        },
        success: function(response) {
            am4core.ready(function() {

                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                // Create chart instance
                var chart = am4core.create("am-layeredcolumn-chart", am4charts.XYChart);
                chart.colors.list = [am4core.color("#37e6b0"), am4core.color("#4788ff")];

                // Add percent sign to all numbers
                chart.numberFormatter.numberFormat = "#.#'%'";

                // Transform the response data to the format required by the chart
                chart.data = response.apexLayeredColumnChart.map(function(item) {
                    return {
                        "category": item.category_name, // Assuming `category_name` is in your response
                        "revenue": item.revenue         // Assuming `revenue` is in your response
                    };
                });

                // Create axes
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "category";
                categoryAxis.renderer.grid.template.location = 0;
                categoryAxis.renderer.minGridDistance = 30;

                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.title.text = "Revenue by Category";
                valueAxis.title.fontWeight = 800;

                // Create series for revenue
                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueY = "revenue";
                series.dataFields.categoryX = "category";
                series.clustered = false;
                series.tooltipText = "Revenue in {categoryX}: [bold]{valueY}[/]";
                series.columns.template.width = am4core.percent(60); // Adjust width as needed

                // Cursor
                chart.cursor = new am4charts.XYCursor();
                chart.cursor.lineX.disabled = true;
                chart.cursor.lineY.disabled = true;

                // Check for dark mode and update chart
                const body = document.querySelector('body');
                if (body.classList.contains('dark')) {
                    amChartUpdate(chart, { dark: true });
                }

                document.addEventListener('ChangeColorMode', function(e) {
                    amChartUpdate(chart, e.detail);
                });

            }); // end am4core.ready()
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}



if (jQuery('#am-columnlinr-chart').length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'monthly' // Adjust this based on your requirement (weekly, monthly, yearly)
        },
        success: function(response) {
            am4core.ready(function() {

                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                // Create chart instance
                var chart = am4core.create("am-columnlinr-chart", am4charts.XYChart);
                chart.colors.list = [am4core.color("#4788ff")];

                // Export
                chart.exporting.menu = new am4core.ExportMenu();

                // Transform the response data to the format required by the chart
                chart.data = response.apexColumnLineChart.map(function(item) {
                    return {
                        "date": item.date,         // Assuming `date` is in your response
                        "revenue": item.revenue,   // Assuming `revenue` is in your response
                        "profit": item.profit      // Assuming `profit` is in your response
                    };
                });

                /* Create axes */
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "date";
                categoryAxis.renderer.minGridDistance = 30;

                /* Create value axis */
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

                /* Create series for revenue */
                var columnSeries = chart.series.push(new am4charts.ColumnSeries());
                columnSeries.name = "Revenue";
                columnSeries.dataFields.valueY = "revenue";
                columnSeries.dataFields.categoryX = "date";

                columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} on {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/]";
                columnSeries.tooltip.label.textAlign = "middle";

                /* Create series for profit */
                var lineSeries = chart.series.push(new am4charts.LineSeries());
                lineSeries.name = "Profit";
                lineSeries.dataFields.valueY = "profit";
                lineSeries.dataFields.categoryX = "date";

                lineSeries.stroke = am4core.color("#4788ff");
                lineSeries.strokeWidth = 3;
                lineSeries.tooltip.label.textAlign = "middle";

                var bullet = lineSeries.bullets.push(new am4charts.Bullet());
                bullet.fill = am4core.color("#fdd400");
                bullet.tooltipText = "[#fff font-size: 15px]{name} on {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/]";
                var circle = bullet.createChild(am4core.Circle);
                circle.radius = 4;
                circle.fill = am4core.color("#fff");
                circle.strokeWidth = 3;

                // Check for dark mode and update chart
                const body = document.querySelector('body');
                if (body.classList.contains('dark')) {
                    amChartUpdate(chart, { dark: true });
                }

                document.addEventListener('ChangeColorMode', function(e) {
                    amChartUpdate(chart, e.detail);
                });

            }); // end am4core.ready()
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}


if (jQuery("#layout1-chart-3").length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'monthly' // Adjust this based on your requirement (weekly, monthly, yearly)
        },
        success: function(response) {
            // Transform the response data to the format required by the chart
            const data = response.apexLineChart.map(function(item) {
                return {
                    x: item.date,     // Assuming `date` is in your response and you want to use it as x-axis categories
                    y: item.profit    // Assuming `profit` is in your response
                };
            });

            // Chart options
            const options = {
                series: [{
                    name: "Profit",
                    data: data
                }],
                colors: ['#FF7E41'],
                chart: {
                    height: 150,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 12,
                        left: 1,
                        blur: 2,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: '',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'], // Takes an array which will be repeated on columns
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: response.apexLineChart.map(item => item.date) // Use dates from the response as x-axis categories
                }
            };

            // Create and render the chart
            const chart = new ApexCharts(document.querySelector("#layout1-chart-3"), options);
            chart.render();

            // Check for dark mode and update chart
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) {
                apexChartUpdate(chart, { dark: true });
            }

            document.addEventListener('ChangeColorMode', function(e) {
                apexChartUpdate(chart, e.detail);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}



if (jQuery("#layout1-chart-4").length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'monthly' // Adjust this based on your requirement (weekly, monthly, yearly)
        },
        success: function(response) {
            // Transform the response data to the format required by the chart
            const data = response.apexLineChart.map(function(item) {
                return {
                    x: item.date,     // Assuming `date` is in your response and you want to use it as x-axis categories
                    y: item.expenses  // Assuming `expenses` is in your response
                };
            });

            // Chart options
            const options = {
                series: [{
                    name: "Expenses",
                    data: data
                }],
                colors: ['#32BDEA'],
                chart: {
                    height: 150,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 12,
                        left: 1,
                        blur: 2,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: '',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'], // Takes an array which will be repeated on columns
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: response.apexLineChart.map(item => item.date) // Use dates from the response as x-axis categories
                }
            };

            // Create and render the chart
            const chart = new ApexCharts(document.querySelector("#layout1-chart-4"), options);
            chart.render();

            // Check for dark mode and update chart
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) {
                apexChartUpdate(chart, { dark: true });
            }

            document.addEventListener('ChangeColorMode', function(e) {
                apexChartUpdate(chart, e.detail);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}


if (jQuery("#layout1-chart-5").length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'monthly' // Adjust this based on your requirement (weekly, monthly, yearly)
        },
        success: function(response) {
            // Transform the response data to the format required by the chart
            const data = {
                profit: response.apexBarChart.map(item => item.profit),
                expenses: response.apexBarChart.map(item => item.expenses),
                categories: response.apexBarChart.map(item => item.date) // Assuming `date` is in your response
            };

            // Chart options
            const options = {
                series: [{
                    name: 'Profit',
                    data: data.profit
                }, {
                    name: 'Expenses',
                    data: data.expenses
                }],
                chart: {
                    type: 'bar',
                    height: 300
                },
                colors: ['#32BDEA', '#FF7E41'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '30%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 3,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.categories,
                    labels: {
                        minWidth: 0,
                        maxWidth: 0
                    }
                },
                yaxis: {
                    show: true,
                    labels: {
                        minWidth: 20,
                        maxWidth: 20
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$ " + val + " thousands";
                        }
                    }
                }
            };

            // Create and render the chart
            const chart = new ApexCharts(document.querySelector("#layout1-chart-5"), options);
            chart.render();

            // Check for dark mode and update chart
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) {
                apexChartUpdate(chart, { dark: true });
            }

            document.addEventListener('ChangeColorMode', function(e) {
                apexChartUpdate(chart, e.detail);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}





