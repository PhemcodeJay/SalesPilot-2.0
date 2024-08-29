if (jQuery('#am-layeredcolumn-chart').length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-data.php', // Replace with the correct path to your PHP script
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
        url: 'chart-data.php', // Replace with the correct path to your PHP script
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




