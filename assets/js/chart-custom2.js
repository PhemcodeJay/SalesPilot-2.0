$(document).ready(function() {
    // Function to render Apex Charts
    function renderApexChart(selector, options) {
        if (typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(document.querySelector(selector), options);
            chart.render();

            const body = document.querySelector('body');
            if (body.classList.contains('dark') && typeof apexChartUpdate === 'function') {
                apexChartUpdate(chart, { dark: true });
            }

            document.addEventListener('ChangeColorMode', function(e) {
                if (typeof apexChartUpdate === 'function') {
                    apexChartUpdate(chart, e.detail);
                }
            });
        } else {
            console.error("ApexCharts is not defined. Make sure the library is loaded.");
        }
    }

    // Utility function to fetch chart data and pass it to the callback for processing
    function fetchChartData(url, selector, chartOptionsCallback) {
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response); // Check the data structure in the console
                const options = chartOptionsCallback(response);
                if (options) {
                    renderApexChart(selector, options);
                } else {
                    console.error("No options available for the chart.");
                }
            },
            error: function(error) {
                console.error("Error fetching data: ", error);
                $(selector).html("<p>Failed to load data.</p>");
            }
        });
    }

    // Fetch all chart data in a single call
    fetchChartData('chart-data.php', '#apex-basic', function(response) {
        if (!response['apex-basic']) {
            console.error("Missing apex-basic data");
            return null;
        }

        const salesData = response['apex-basic'].map(item => Number(item.total_sales));
        const salesDates = response['apex-basic'].map(item => item.date);

        return {
            chart: {
                height: 350,
                type: "line",
                zoom: { enabled: false }
            },
            series: [{ name: "Total Sales", data: salesData }],
            xaxis: { categories: salesDates }
        };
    });

    // Fetch data for Sell-Through and Inventory Turnover Rates
    fetchChartData('chart-data.php', '#apex-line-area', function(response) {
        if (!response['apex-line-area']) {
            console.error("Missing apex-line-area data");
            return null;
        }

        const sellThroughData = response['apex-line-area'].map(item => Number(item.avg_sell_through_rate));
        const turnoverData = response['apex-line-area'].map(item => Number(item.avg_inventory_turnover_rate));
        const lineDates = response['apex-line-area'].map(item => item.date);

        return {
            chart: {
                height: 350,
                type: "area",
                zoom: { enabled: false }
            },
            series: [
                { name: "Sell-Through Rate", data: sellThroughData },
                { name: "Inventory Turnover Rate", data: turnoverData }
            ],
            xaxis: { categories: lineDates }
        };
    });

    // Fetch data for Revenue vs. Expenses
    fetchChartData('chart-data.php', '#apex-column', function(response) {
        if (!response['apex-column']) {
            console.error("Missing apex-column data");
            return null;
        }

        const revenueData = response['apex-column'].map(item => Number(item.revenue.replace(/,/g, '')));
        const expensesData = response['apex-column'].map(item => Number(item.total_expenses.replace(/,/g, '')));
        const profitData = response['apex-column'].map(item => Number(item.profit.replace(/,/g, '')));
        const columnDates = response['apex-column'].map(item => item.date);

        return {
            chart: {
                height: 350,
                type: "bar",
                stacked: true
            },
            series: [
                { name: "Revenue", data: revenueData },
                { name: "Expenditure", data: expensesData },
                { name: "Profit", data: profitData }
            ],
            xaxis: { categories: columnDates }
        };
    });

    // Fetch data for 3D Pie chart
    fetchChartData('chart-data.php', '#am-3dpie-chart', function(response) {
        if (!response['am-3dpie-chart'] || Object.keys(response['am-3dpie-chart']).length === 0) {
            console.log("No data available for the 3D Pie chart.");
            return null;
        }

        // Extracting pie chart data (assuming data is returned as { product_name: revenue })
        const pieData = Object.keys(response['am-3dpie-chart']).map(product => ({
            value: Number(response['am-3dpie-chart'][product]), // assuming the value is revenue
            label: product // product name
        }));

        // Prepare data for the pie chart
        const pieValues = pieData.map(item => item.value);
        const pieLabels = pieData.map(item => item.label);

        return {
            chart: {
                height: 350,
                type: "pie"
            },
            series: pieValues,
            labels: pieLabels
        };
    });

    // Function to render AmCharts (3D Pie)
    function renderAmChart(selector, data) {
        if (typeof am4core !== 'undefined') {
            am4core.ready(function() {
                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                const chart = am4core.create(selector, am4charts.PieChart3D);
                chart.hiddenState.properties.opacity = 0; // Initial fade-in
                chart.legend = new am4charts.Legend();
                chart.data = data;

                const series = chart.series.push(new am4charts.PieSeries3D());
                series.dataFields.value = "revenue";
                series.dataFields.category = "product";
                series.colors.list = [
                    am4core.color("#4788ff"),
                    am4core.color("#37e6b0"),
                    am4core.color("#ff4b4b"),
                    am4core.color("#fe721c"),
                    am4core.color("#876cfe")
                ];
            });
        } else {
            console.error("am4core is not defined. Make sure the library is loaded.");
        }
    }

    // Fetch data for Top 5 Revenue Products (AmCharts)
    fetchChartData('chart-data.php', '#am-3dpie-chart', function(response) {
        if (!response['am-3dpie-chart'] || Object.keys(response['am-3dpie-chart']).length === 0) {
            console.error("No data available for the Top 5 Revenue Products.");
            return null;
        }

        const top5RevenueProductsData = Object.keys(response['am-3dpie-chart']).map(product => ({
            product: product,
            revenue: response['am-3dpie-chart'][product]
        }));
        renderAmChart('am-3dpie-chart', top5RevenueProductsData);
    });
});
