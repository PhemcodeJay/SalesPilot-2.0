if (jQuery('#am-layeredcolumn-chart').length) {
    jQuery.ajax({
        url: 'chart-dash.php',
        type: 'GET',
        dataType: 'json',
        data: { range: 'yearly' },
        success: function(response) {
            am4core.ready(function() {
                am4core.useTheme(am4themes_animated);
                var chart = am4core.create("am-layeredcolumn-chart", am4charts.XYChart);
                chart.colors.list = [am4core.color("#37e6b0"), am4core.color("#4788ff")];
                chart.numberFormatter.numberFormat = "#.#'%'";
                chart.data = response.apexLayeredColumnChart.map(function(item) {
                    return { "category": item.category_name, "revenue": item.revenue };
                });
                
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "category";
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.title.text = "Revenue by Category";

                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueY = "revenue";
                series.dataFields.categoryX = "category";
                series.tooltipText = "Revenue in {categoryX}: [bold]{valueY}[/]";
                
                chart.cursor = new am4charts.XYCursor();
                const body = document.querySelector('body');
                if (body.classList.contains('dark')) amChartUpdate(chart, { dark: true });
                document.addEventListener('ChangeColorMode', function(e) {
                    amChartUpdate(chart, e.detail);
                });
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}

if (jQuery('#am-columnlinr-chart').length) {
    jQuery.ajax({
        url: 'chart-dash.php',
        type: 'GET',
        dataType: 'json',
        data: { range: 'yearly' },
        success: function(response) {
            am4core.ready(function() {
                am4core.useTheme(am4themes_animated);
                var chart = am4core.create("am-columnlinr-chart", am4charts.XYChart);
                chart.colors.list = [am4core.color("#4788ff")];
                chart.exporting.menu = new am4core.ExportMenu();
                chart.data = response.apexColumnLineChart.map(function(item) {
                    return { "date": item.date, "revenue": item.revenue, "profit": item.profit };
                });
                
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "date";
                
                var columnSeries = chart.series.push(new am4charts.ColumnSeries());
                columnSeries.name = "Revenue";
                columnSeries.dataFields.valueY = "revenue";
                columnSeries.dataFields.categoryX = "date";
                columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} on {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/]";
                
                var lineSeries = chart.series.push(new am4charts.LineSeries());
                lineSeries.name = "Profit";
                lineSeries.dataFields.valueY = "profit";
                lineSeries.dataFields.categoryX = "date";
                
                const body = document.querySelector('body');
                if (body.classList.contains('dark')) amChartUpdate(chart, { dark: true });
                document.addEventListener('ChangeColorMode', function(e) {
                    amChartUpdate(chart, e.detail);
                });
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}

if (jQuery("#layout1-chart-3").length) {
    jQuery.ajax({
        url: 'chart-dash.php',
        type: 'GET',
        dataType: 'json',
        data: { range: 'yearly' },
        success: function(response) {
            const data = response.apexLineChart.map(function(item) {
                return { x: item.date, y: item.profit };
            });

            const options = {
                series: [{ name: "Profit", data: data }],
                colors: ['#FF7E41'],
                chart: { height: 150, type: 'line', zoom: { enabled: false }, sparkline: { enabled: true }},
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { categories: response.apexLineChart.map(item => item.date) }
            };

            const chart = new ApexCharts(document.querySelector("#layout1-chart-3"), options);
            chart.render();
            
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) apexChartUpdate(chart, { dark: true });
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
    jQuery.ajax({
        url: 'chart-dash.php',
        type: 'GET',
        dataType: 'json',
        data: { range: 'yearly' },
        success: function(response) {
            const data = response.apexLineChart.map(function(item) {
                return { x: item.date, y: item.expenses };
            });

            const options = {
                series: [{ name: "Expenses", data: data }],
                colors: ['#32BDEA'],
                chart: { height: 150, type: 'line', zoom: { enabled: false }, sparkline: { enabled: true }},
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { categories: response.apexLineChart.map(item => item.date) }
            };

            const chart = new ApexCharts(document.querySelector("#layout1-chart-4"), options);
            chart.render();
            
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) apexChartUpdate(chart, { dark: true });
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
    jQuery.ajax({
        url: 'chart-dash.php',
        type: 'GET',
        dataType: 'json',
        data: { range: 'yearly' },
        success: function(response) {
            const data = {
                profit: response.apexBarChart.map(item => item.profit),
                expenses: response.apexBarChart.map(item => item.expenses),
                categories: response.apexBarChart.map(item => item.date)
            };

            const options = {
                series: [{ name: 'Profit', data: data.profit }, { name: 'Expenses', data: data.expenses }],
                chart: { type: 'bar', height: 300 },
                colors: ['#32BDEA', '#FF7E41'],
                plotOptions: { bar: { horizontal: false, columnWidth: '30%', endingShape: 'rounded' }},
                xaxis: { categories: data.categories }
            };

            const chart = new ApexCharts(document.querySelector("#layout1-chart-5"), options);
            chart.render();
            
            const body = document.querySelector('body');
            if (body.classList.contains('dark')) apexChartUpdate(chart, { dark: true });
            document.addEventListener('ChangeColorMode', function(e) {
                apexChartUpdate(chart, e.detail);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data: " + error);
        }
    });
}
