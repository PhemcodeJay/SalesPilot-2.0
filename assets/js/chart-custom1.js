(function(jQuery) {

    "use strict";

// for apexchart
function apexChartUpdate(chart, detail) {
    let color = getComputedStyle(document.documentElement).getPropertyValue('--dark');
    if (detail.dark) {
      color = getComputedStyle(document.documentElement).getPropertyValue('--white');
    }
    chart.updateOptions({
      chart: {
        foreColor: color
      }
    })
  }
  
// for am chart
function amChartUpdate(chart, detail) {
  // let color = getComputedStyle(document.documentElement).getPropertyValue('--dark');
  if (detail.dark) {
    // color = getComputedStyle(document.documentElement).getPropertyValue('--white');
    chart.stroke = am4core.color(getComputedStyle(document.documentElement).getPropertyValue('--white'));
  }
  chart.validateData();
}

/*---------------------------------------------------------------------
   Apex Charts
-----------------------------------------------------------------------*/
if (jQuery("#apex-basic").length) {
  $.ajax({
    url: 'chart-data.php', // Replace with the actual path to your PHP script
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

if (jQuery("#apex-line-area").length) {
  $.ajax({
    url: 'chart-data.php', // Replace with the actual path to your PHP script
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


if (jQuery("#apex-column").length) {
  $.ajax({
    url: 'chart-data.php', // Replace with the actual path to your PHP script
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



/*---------------------------------------------------------------------
   Am Charts
-----------------------------------------------------------------------*/

   if (jQuery('#am-columnlinr-chart').length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'yearly' // Adjust this based on your requirement (weekly, yearly, yearly)
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
   
if(jQuery('#am-3dpie-chart').length){
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        $.ajax({
            url: 'chart-data.php', // Replace with the actual path to your PHP script
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

   if (jQuery('#am-layeredcolumn-chart').length) {
    // Fetch data from the PHP script
    jQuery.ajax({
        url: 'chart-dash.php', // Replace with the correct path to your PHP script
        type: 'GET',
        dataType: 'json',
        data: {
            range: 'yearly' // Adjust this based on your requirement (weekly, yearly, yearly)
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






  
/*---------------------------------------------------------------------
   Editable Table
-----------------------------------------------------------------------*/
const $tableID = $('#table');
 const $BTN = $('#export-btn');
 const $EXPORT = $('#export');

 const newTr = `
<tr class="hide">
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half">
    <span class="table-up"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-up" aria-hidden="true"></i></a></span>
    <span class="table-down"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-down" aria-hidden="true"></i></a></span>
  </td>
  <td>
    <span class="table-remove"><button type="button" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">Remove</button></span>
  </td>
</tr>`;

 $('.table-add').on('click', 'i', () => {

   const $clone = $tableID.find('tbody tr').last().clone(true).removeClass('hide table-line');

   if ($tableID.find('tbody tr').length === 0) {

     $('tbody').append(newTr);
   }

   $tableID.find('table').append($clone);
 });

 $tableID.on('click', '.table-remove', function () {

   $(this).parents('tr').detach();
 });

 $tableID.on('click', '.table-up', function () {

   const $row = $(this).parents('tr');

   if ($row.index() === 1) {
     return;
   }

   $row.prev().before($row.get(0));
 });

 $tableID.on('click', '.table-down', function () {

   const $row = $(this).parents('tr');
   $row.next().after($row.get(0));
 });

 // A few jQuery helpers for exporting only
 jQuery.fn.pop = [].pop;
 jQuery.fn.shift = [].shift;

 $BTN.on('click', () => {

   const $rows = $tableID.find('tr:not(:hidden)');
   const headers = [];
   const data = [];

   // Get the headers (add special header logic here)
   $($rows.shift()).find('th:not(:empty)').each(function () {

     headers.push($(this).text().toLowerCase());
   });

   // Turn all existing rows into a loopable array
   $rows.each(function () {
     const $td = $(this).find('td');
     const h = {};

     // Use the headers from earlier to name our hash keys
     headers.forEach((header, i) => {

       h[header] = $td.eq(i).text();
     });

     data.push(h);
   });

   // Output the result
   $EXPORT.text(JSON.stringify(data));
 });

/*---------------------------------------------------------------------
    Form Wizard - 1
-----------------------------------------------------------------------*/

$(document).ready(function(){

    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;

    setProgressBar(current);

    $(".next").click(function(){

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();

    //Add Class Active
    $("#top-tab-list li").eq($("fieldset").index(next_fs)).addClass("active");
    $("#top-tab-list li").eq($("fieldset").index(current_fs)).addClass("done");

    //show the next fieldset
    next_fs.show();
    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
    step: function(now) {
    // for making fielset appear animation
    opacity = 1 - now;

    current_fs.css({
    'display': 'none',
    'position': 'relative',

    });

    next_fs.css({'opacity': opacity});
    },
    duration: 500
    });
    setProgressBar(++current);
    });

    $(".previous").click(function(){

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    //Remove class active
    $("#top-tab-list li").eq($("fieldset").index(current_fs)).removeClass("active");
    $("#top-tab-list li").eq($("fieldset").index(previous_fs)).removeClass("done");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
    step: function(now) {
    // for making fielset appear animation
    opacity = 1 - now;

    current_fs.css({
    'display': 'none',
    'position': 'relative'
    });
    previous_fs.css({'opacity': opacity});
    },
    duration: 500
    });
    setProgressBar(--current);
    });

    function setProgressBar(curStep){
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
    .css("width",percent+"%")
    }

    $(".submit").click(function(){
    return false;
    })

});

 /*---------------------------------------------------------------------
   validate form wizard
-----------------------------------------------------------------------*/

$(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.addClass('active');
            $item.parent().addClass('active');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],textarea"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.active').trigger('click');
});
 /*---------------------------------------------------------------------
   Vertical form wizard
-----------------------------------------------------------------------*/
$(document).ready(function(){

    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;

    setProgressBar(current);

    $(".next").click(function(){

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();

    //Add Class Active
    $("#top-tabbar-vertical li").eq($("fieldset").index(next_fs)).addClass("active");

    //show the next fieldset
    next_fs.show();
    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
    step: function(now) {
    // for making fielset appear animation
    opacity = 1 - now;

    current_fs.css({
    'display': 'none',
    'position': 'relative'
    });
    next_fs.css({'opacity': opacity});
    },
    duration: 500
    });
    setProgressBar(++current);
    });

    $(".previous").click(function(){

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    //Remove class active
    $("#top-tabbar-vertical li").eq($("fieldset").index(current_fs)).removeClass("active");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
    step: function(now) {
    // for making fielset appear animation
    opacity = 1 - now;

    current_fs.css({
    'display': 'none',
    'position': 'relative'
    });
    previous_fs.css({'opacity': opacity});
    },
    duration: 500
    });
    setProgressBar(--current);
    });

    function setProgressBar(curStep){
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
    .css("width",percent+"%")
    }

    $(".submit").click(function(){
    return false;
    })

});


/*---------------------------------------------------------------------
   Profile Image Edit
-----------------------------------------------------------------------*/

$(document).ready(function() {
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }


    $(".file-upload").on('change', function(){
        readURL(this);
    });

    $(".upload-button").on('click', function() {
       $(".file-upload").click();
    });
});

// ratting
$(function() {
  if(typeof $.fn.barrating !== typeof undefined){
    $('#example').barrating({
      theme: 'fontawesome-stars'
    });
    $('#bars-number').barrating({
      theme: 'bars-1to10'
    });
    $('#movie-rating').barrating('show',{
      theme: 'bars-movie'
    });
    $('#movie-rating').barrating('set', 'Mediocre');
    $('#pill-rating').barrating({
      theme: 'bars-pill',
      showValues: true,
      showSelectedRating: false,
      onSelect: function(value, text) {
        alert('Selected rating: ' + value);
    }
    });
  } 
  if (typeof $.fn.mdbRate !== typeof undefined) {
    $('#rateMe1').mdbRate();
    $('#face-rating').mdbRate();
  }
});

// quill
if (jQuery("#editor").length) {
  var quill = new Quill('#editor', {
  theme: 'snow'
  });
}
  // With Tooltip
  if (jQuery("#quill-toolbar").length) {
  var quill = new Quill('#quill-toolbar', {
      modules: {
        toolbar: '#quill-tool'
      },
      placeholder: 'Compose an epic...',
      theme: 'snow'
  });
  // Enable all tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // Can control programmatically too
  $('.ql-italic').mouseover();
  setTimeout(function() {
      $('.ql-italic').mouseout();
  }, 2500);
}
  // file upload
  $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
  });

  /*---------------------------------------------------------------------
   Dashboard Charts
  ---------------------------------------------------------------------*/
  if (jQuery("#layout1-chart1").length) {
    options = {
      chart: {
        height: 350,
        type: "candlestick"
      },
      colors: ["#32BDEA", "#FF7E41"],
      series: [{
        data: [{
          x: new Date(15387786e5),
          y: [6629.81, 6650.5, 6623.04, 6633.33]
        }, {
          x: new Date(15387804e5),
          y: [6632.01, 6643.59, 6620, 6630.11]
        }, {
          x: new Date(15387822e5),
          y: [6630.71, 6648.95, 6623.34, 6635.65]
        }, {
          x: new Date(1538784e6),
          y: [6635.65, 6651, 6629.67, 6638.24]
        }, {
          x: new Date(15387858e5),
          y: [6638.24, 6640, 6620, 6624.47]
        }, {
          x: new Date(15387876e5),
          y: [6624.53, 6636.03, 6621.68, 6624.31]
        }, {
          x: new Date(15387894e5),
          y: [6624.61, 6632.2, 6617, 6626.02]
        }, {
          x: new Date(15387912e5),
          y: [6627, 6627.62, 6584.22, 6603.02]
        }, {
          x: new Date(1538793e6),
          y: [6605, 6608.03, 6598.95, 6604.01]
        }, {
          x: new Date(15387948e5),
          y: [6604.5, 6614.4, 6602.26, 6608.02]
        }, {
          x: new Date(15387966e5),
          y: [6608.02, 6610.68, 6601.99, 6608.91]
        }, {
          x: new Date(15387984e5),
          y: [6608.91, 6618.99, 6608.01, 6612]
        }, {
          x: new Date(15388002e5),
          y: [6612, 6615.13, 6605.09, 6612]
        }, {
          x: new Date(1538802e6),
          y: [6612, 6624.12, 6608.43, 6622.95]
        }, {
          x: new Date(15388038e5),
          y: [6623.91, 6623.91, 6615, 6615.67]
        }, {
          x: new Date(15388056e5),
          y: [6618.69, 6618.74, 6610, 6610.4]
        }, {
          x: new Date(15388074e5),
          y: [6611, 6622.78, 6610.4, 6614.9]
        }, {
          x: new Date(15388092e5),
          y: [6614.9, 6626.2, 6613.33, 6623.45]
        }, {
          x: new Date(1538811e6),
          y: [6623.48, 6627, 6618.38, 6620.35]
        }, {
          x: new Date(15388128e5),
          y: [6619.43, 6620.35, 6610.05, 6615.53]
        }, {
          x: new Date(15388146e5),
          y: [6615.53, 6617.93, 6610, 6615.19]
        }, {
          x: new Date(15388164e5),
          y: [6615.19, 6621.6, 6608.2, 6620]
        }, {
          x: new Date(15388182e5),
          y: [6619.54, 6625.17, 6614.15, 6620]
        }, {
          x: new Date(153882e7),
          y: [6620.33, 6634.15, 6617.24, 6624.61]
        }, {
          x: new Date(15388218e5),
          y: [6625.95, 6626, 6611.66, 6617.58]
        }, {
          x: new Date(15388236e5),
          y: [6619, 6625.97, 6595.27, 6598.86]
        }, {
          x: new Date(15388254e5),
          y: [6598.86, 6598.88, 6570, 6587.16]
        }, {
          x: new Date(15388272e5),
          y: [6588.86, 6600, 6580, 6593.4]
        }, {
          x: new Date(1538829e6),
          y: [6593.99, 6598.89, 6585, 6587.81]
        }, {
          x: new Date(15388308e5),
          y: [6587.81, 6592.73, 6567.14, 6578]
        }, {
          x: new Date(15388326e5),
          y: [6578.35, 6581.72, 6567.39, 6579]
        }, {
          x: new Date(15388344e5),
          y: [6579.38, 6580.92, 6566.77, 6575.96]
        }, {
          x: new Date(15388362e5),
          y: [6575.96, 6589, 6571.77, 6588.92]
        }, {
          x: new Date(1538838e6),
          y: [6588.92, 6594, 6577.55, 6589.22]
        }, {
          x: new Date(15388398e5),
          y: [6589.3, 6598.89, 6589.1, 6596.08]
        }, {
          x: new Date(15388416e5),
          y: [6597.5, 6600, 6588.39, 6596.25]
        }, {
          x: new Date(15388434e5),
          y: [6598.03, 6600, 6588.73, 6595.97]
        }, {
          x: new Date(15388452e5),
          y: [6595.97, 6602.01, 6588.17, 6602]
        }, {
          x: new Date(1538847e6),
          y: [6602, 6607, 6596.51, 6599.95]
        }, {
          x: new Date(15388488e5),
          y: [6600.63, 6601.21, 6590.39, 6591.02]
        }, {
          x: new Date(15388506e5),
          y: [6591.02, 6603.08, 6591, 6591]
        }, {
          x: new Date(15388524e5),
          y: [6591, 6601.32, 6585, 6592]
        }, {
          x: new Date(15388542e5),
          y: [6593.13, 6596.01, 6590, 6593.34]
        }, {
          x: new Date(1538856e6),
          y: [6593.34, 6604.76, 6582.63, 6593.86]
        }, {
          x: new Date(15388578e5),
          y: [6593.86, 6604.28, 6586.57, 6600.01]
        }, {
          x: new Date(15388596e5),
          y: [6601.81, 6603.21, 6592.78, 6596.25]
        }, {
          x: new Date(15388614e5),
          y: [6596.25, 6604.2, 6590, 6602.99]
        }, {
          x: new Date(15388632e5),
          y: [6602.99, 6606, 6584.99, 6587.81]
        }, {
          x: new Date(1538865e6),
          y: [6587.81, 6595, 6583.27, 6591.96]
        }, {
          x: new Date(15388668e5),
          y: [6591.97, 6596.07, 6585, 6588.39]
        }, {
          x: new Date(15388686e5),
          y: [6587.6, 6598.21, 6587.6, 6594.27]
        }, {
          x: new Date(15388704e5),
          y: [6596.44, 6601, 6590, 6596.55]
        }, {
          x: new Date(15388722e5),
          y: [6598.91, 6605, 6596.61, 6600.02]
        }, {
          x: new Date(1538874e6),
          y: [6600.55, 6605, 6589.14, 6593.01]
        }, {
          x: new Date(15388758e5),
          y: [6593.15, 6605, 6592, 6603.06]
        }]
      }],
      title: {
        text: "$45,78956",
        align: "left"
      },
      xaxis: {
        type: "datetime"
      },
      yaxis: {
        tooltip: {
          enabled: !0
        },
        labels: {
          offsetX: -2,
          offsetY: 0,
          minWidth: 30,
          maxWidth: 30,
        }
      },
      plotOptions: {
        candlestick: {
          colors: {
            upward: '#FF7E41',
            downward: '#32BDEA'
          }
        }
      }
    };
    (chart = new ApexCharts(document.querySelector("#layout1-chart1"), options)).render()
    const body = document.querySelector('body')
    if (body.classList.contains('dark')) {
      apexChartUpdate(chart, {
        dark: true
      })
    }
  
    document.addEventListener('ChangeColorMode', function (e) {
      apexChartUpdate(chart, e.detail)
    })
  }
  if(jQuery('#layout1-chart-2').length){
    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end
    
    // Create chart instance
    var chart = am4core.create("layout1-chart-2", am4charts.XYChart);
    chart.colors.list = [
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA"),
		  am4core.color("#32BDEA")
		];
    chart.scrollbarX = new am4core.Scrollbar();
    
    // Add data
    chart.data = [{
      "country": "Jan",
      "visits": 3025
    }, {
      "country": "Feb",
      "visits": 1882
    }, {
      "country": "Mar",
      "visits": 1809
    }, {
      "country": "Apr",
      "visits": 1322
    }, {
      "country": "May",
      "visits": 1122
    }, {
      "country": "Jun",
      "visits": 1114
    }, {
      "country": "Jul",
      "visits": 984
    }, {
      "country": "Aug",
      "visits": 711
    }];
    
    prepareParetoData();
    
    function prepareParetoData(){
        var total = 0;
    
        for(var i = 0; i < chart.data.length; i++){
            var value = chart.data[i].visits;
            total += value;
        }
    
        var sum = 0;
        for(var i = 0; i < chart.data.length; i++){
            var value = chart.data[i].visits;
            sum += value;   
            chart.data[i].pareto = sum / total * 100;
        }    
    }
    
    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "country";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 60;
    categoryAxis.tooltip.disabled = true;
    
    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.minWidth = 50;
    valueAxis.min = 0;
    valueAxis.cursorTooltipEnabled = false;

    // Create series
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.sequencedInterpolation = true;
    series.dataFields.valueY = "visits";
    series.dataFields.categoryX = "country";
    series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
    series.columns.template.strokeWidth = 0;
    
    series.tooltip.pointerOrientation = "vertical";
    
    series.columns.template.column.cornerRadiusTopLeft = 10;
    series.columns.template.column.cornerRadiusTopRight = 10;
    series.columns.template.column.fillOpacity = 0.8;
    
    // on hover, make corner radiuses bigger
    var hoverState = series.columns.template.column.states.create("hover");
    hoverState.properties.cornerRadiusTopLeft = 0;
    hoverState.properties.cornerRadiusTopRight = 0;
    hoverState.properties.fillOpacity = 1;
    
    series.columns.template.adapter.add("fill", function(fill, target) {
      return chart.colors.getIndex(target.dataItem.index);
    })
    
    
    var paretoValueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    paretoValueAxis.renderer.opposite = true;
    paretoValueAxis.min = 0;
    paretoValueAxis.max = 100;
    paretoValueAxis.strictMinMax = true;
    paretoValueAxis.renderer.grid.template.disabled = true;
    paretoValueAxis.numberFormatter = new am4core.NumberFormatter();
    paretoValueAxis.numberFormatter.numberFormat = "#'%'"
    paretoValueAxis.cursorTooltipEnabled = false;
    
    var paretoSeries = chart.series.push(new am4charts.LineSeries())
    paretoSeries.dataFields.valueY = "pareto";
    paretoSeries.dataFields.categoryX = "country";
    paretoSeries.yAxis = paretoValueAxis;
    paretoSeries.tooltipText = "pareto: {valueY.formatNumber('#.0')}%[/]";
    paretoSeries.bullets.push(new am4charts.CircleBullet());
    paretoSeries.strokeWidth = 2;
    paretoSeries.stroke = new am4core.InterfaceColorSet().getFor("alternativeBackground");
    paretoSeries.strokeOpacity = 0.5;
    
    // Cursor
    chart.cursor = new am4charts.XYCursor();
    chart.cursor.behavior = "panX";
    
    }); // end am4core.ready()
  }
  // layout1-chart-3 (Profit)
if (jQuery("#layout1-chart-3").length) {
  jQuery.ajax({
      url: 'chart-dash.php',
      type: 'GET',
      dataType: 'json',
      data: { range: 'yearly' },
      success: function(response) {
          const data = response['layout1-chart-3'].map(function(item) {
              return { x: item.date, y: item.profit };
          });

          const options = {
              series: [{ name: "Profit", data: data }],
              colors: ['#FF7E41'],
              chart: { height: 150, type: 'line', zoom: { enabled: false }, sparkline: { enabled: true }},
              stroke: { curve: 'smooth', width: 3 },
              xaxis: { categories: response['layout1-chart-3'].map(item => item.date) }
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

// layout1-chart-4 (Expenses)
if (jQuery("#layout1-chart-4").length) {
  jQuery.ajax({
      url: 'chart-dash.php',
      type: 'GET',
      dataType: 'json',
      data: { range: 'yearly' },
      success: function(response) {
          const data = response['layout1-chart-4'].map(function(item) {
              return { x: item.date, y: item.expenses };
          });

          const options = {
              series: [{ name: "Expenses", data: data }],
              colors: ['#32BDEA'],
              chart: { height: 150, type: 'line', zoom: { enabled: false }, sparkline: { enabled: true }},
              stroke: { curve: 'smooth', width: 3 },
              xaxis: { categories: response['layout1-chart-4'].map(item => item.date) }
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

// layout1-chart-5 (Profit and Expenses Combined)
if (jQuery("#layout1-chart-5").length) {
  jQuery.ajax({
      url: 'chart-dash.php',
      type: 'GET',
      dataType: 'json',
      data: { range: 'yearly' },
      success: function(response) {
          const data = {
              profit: response['layout1-chart-5'].map(item => item.profit),
              expenses: response['layout1-chart-5'].map(item => item.expenses),
              categories: response['layout1-chart-5'].map(item => item.date)
          };

          const options = {
              series: [{ name: 'Profit', data: data.profit }, { name: 'Expenses', data: data.expenses }],
              chart: { type: 'bar', height: 300 },
              colors: ['#37e6b0', '#ff4d6b'],
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

                                                                                                                                                                                                                                                                                                                                                                                                      

  /*---------------------------------------------------------------------
   Report Charts
  ---------------------------------------------------------------------*/
  if (jQuery("#report-chart1").length) {
    options = {
      chart: {
        height: 350,
        type: "candlestick"
      },
      series: [{
        data: [{
          x: new Date(15387786e5),
          y: [6629.81, 6650.5, 6623.04, 6633.33]
        }, {
          x: new Date(15387804e5),
          y: [6632.01, 6643.59, 6620, 6630.11]
        }, {
          x: new Date(15387822e5),
          y: [6630.71, 6648.95, 6623.34, 6635.65]
        }, {
          x: new Date(1538784e6),
          y: [6635.65, 6651, 6629.67, 6638.24]
        }, {
          x: new Date(15387858e5),
          y: [6638.24, 6640, 6620, 6624.47]
        }, {
          x: new Date(15387876e5),
          y: [6624.53, 6636.03, 6621.68, 6624.31]
        }, {
          x: new Date(15387894e5),
          y: [6624.61, 6632.2, 6617, 6626.02]
        }, {
          x: new Date(15387912e5),
          y: [6627, 6627.62, 6584.22, 6603.02]
        }, {
          x: new Date(1538793e6),
          y: [6605, 6608.03, 6598.95, 6604.01]
        }, {
          x: new Date(15387948e5),
          y: [6604.5, 6614.4, 6602.26, 6608.02]
        }, {
          x: new Date(15387966e5),
          y: [6608.02, 6610.68, 6601.99, 6608.91]
        }, {
          x: new Date(15387984e5),
          y: [6608.91, 6618.99, 6608.01, 6612]
        }, {
          x: new Date(15388002e5),
          y: [6612, 6615.13, 6605.09, 6612]
        }, {
          x: new Date(1538802e6),
          y: [6612, 6624.12, 6608.43, 6622.95]
        }, {
          x: new Date(15388038e5),
          y: [6623.91, 6623.91, 6615, 6615.67]
        }, {
          x: new Date(15388056e5),
          y: [6618.69, 6618.74, 6610, 6610.4]
        }, {
          x: new Date(15388074e5),
          y: [6611, 6622.78, 6610.4, 6614.9]
        }, {
          x: new Date(15388092e5),
          y: [6614.9, 6626.2, 6613.33, 6623.45]
        }, {
          x: new Date(1538811e6),
          y: [6623.48, 6627, 6618.38, 6620.35]
        }, {
          x: new Date(15388128e5),
          y: [6619.43, 6620.35, 6610.05, 6615.53]
        }, {
          x: new Date(15388146e5),
          y: [6615.53, 6617.93, 6610, 6615.19]
        }, {
          x: new Date(15388164e5),
          y: [6615.19, 6621.6, 6608.2, 6620]
        }, {
          x: new Date(15388182e5),
          y: [6619.54, 6625.17, 6614.15, 6620]
        }, {
          x: new Date(153882e7),
          y: [6620.33, 6634.15, 6617.24, 6624.61]
        }, {
          x: new Date(15388218e5),
          y: [6625.95, 6626, 6611.66, 6617.58]
        }, {
          x: new Date(15388236e5),
          y: [6619, 6625.97, 6595.27, 6598.86]
        }, {
          x: new Date(15388254e5),
          y: [6598.86, 6598.88, 6570, 6587.16]
        }, {
          x: new Date(15388272e5),
          y: [6588.86, 6600, 6580, 6593.4]
        }, {
          x: new Date(1538829e6),
          y: [6593.99, 6598.89, 6585, 6587.81]
        }, {
          x: new Date(15388308e5),
          y: [6587.81, 6592.73, 6567.14, 6578]
        }, {
          x: new Date(15388326e5),
          y: [6578.35, 6581.72, 6567.39, 6579]
        }, {
          x: new Date(15388344e5),
          y: [6579.38, 6580.92, 6566.77, 6575.96]
        }, {
          x: new Date(15388362e5),
          y: [6575.96, 6589, 6571.77, 6588.92]
        }, {
          x: new Date(1538838e6),
          y: [6588.92, 6594, 6577.55, 6589.22]
        }, {
          x: new Date(15388398e5),
          y: [6589.3, 6598.89, 6589.1, 6596.08]
        }, {
          x: new Date(15388416e5),
          y: [6597.5, 6600, 6588.39, 6596.25]
        }, {
          x: new Date(15388434e5),
          y: [6598.03, 6600, 6588.73, 6595.97]
        }, {
          x: new Date(15388452e5),
          y: [6595.97, 6602.01, 6588.17, 6602]
        }, {
          x: new Date(1538847e6),
          y: [6602, 6607, 6596.51, 6599.95]
        }, {
          x: new Date(15388488e5),
          y: [6600.63, 6601.21, 6590.39, 6591.02]
        }, {
          x: new Date(15388506e5),
          y: [6591.02, 6603.08, 6591, 6591]
        }, {
          x: new Date(15388524e5),
          y: [6591, 6601.32, 6585, 6592]
        }, {
          x: new Date(15388542e5),
          y: [6593.13, 6596.01, 6590, 6593.34]
        }, {
          x: new Date(1538856e6),
          y: [6593.34, 6604.76, 6582.63, 6593.86]
        }, {
          x: new Date(15388578e5),
          y: [6593.86, 6604.28, 6586.57, 6600.01]
        }, {
          x: new Date(15388596e5),
          y: [6601.81, 6603.21, 6592.78, 6596.25]
        }, {
          x: new Date(15388614e5),
          y: [6596.25, 6604.2, 6590, 6602.99]
        }, {
          x: new Date(15388632e5),
          y: [6602.99, 6606, 6584.99, 6587.81]
        }, {
          x: new Date(1538865e6),
          y: [6587.81, 6595, 6583.27, 6591.96]
        }, {
          x: new Date(15388668e5),
          y: [6591.97, 6596.07, 6585, 6588.39]
        }, {
          x: new Date(15388686e5),
          y: [6587.6, 6598.21, 6587.6, 6594.27]
        }, {
          x: new Date(15388704e5),
          y: [6596.44, 6601, 6590, 6596.55]
        }, {
          x: new Date(15388722e5),
          y: [6598.91, 6605, 6596.61, 6600.02]
        }]
      }],
      title: {
        text: "$45,78956",
        align: "left"
      },
      xaxis: {
        type: "datetime"
      },
      yaxis: {
        tooltip: {
          enabled: !0
        },
        labels: {
          offsetX: -2,
          offsetY: 0,
          minWidth: 30,
          maxWidth: 30,
        }
      },
      plotOptions: {
        candlestick: {
          colors: {
            upward: '#FF7E41',
            downward: '#32BDEA'
          }
        }
      }
    };
    (chart = new ApexCharts(document.querySelector("#report-chart1"), options)).render()
    const body = document.querySelector('body')
    if (body.classList.contains('dark')) {
      apexChartUpdate(chart, {
        dark: true
      })
    }
  
    document.addEventListener('ChangeColorMode', function (e) {
      apexChartUpdate(chart, e.detail)
    })
  }
  if (jQuery("#report-chart02").length) {
    var options = {
      series: [
      {
        name: 'Desktops',
        data: [
          {
            x: 'ABC',
            y: 10
          },
          {
            x: 'DEF',
            y: 60
          },
          {
            x: 'XYZ',
            y: 41
          }
        ]
      },
      {
        name: 'Mobile',
        data: [
          {
            x: 'ABCD',
            y: 10
          },
          {
            x: 'DEFG',
            y: 20
          },
          {
            x: 'WXYZ',
            y: 51
          },
          {
            x: 'PQR',
            y: 30
          },
          {
            x: 'MNO',
            y: 20
          },
          {
            x: 'CDE',
            y: 30
          }
        ]
      }
    ],
      legend: {
      show: false
    },
    chart: {
      height: 350,
      type: 'treemap'
    },
    title: {
      text: 'Multi-dimensional Treemap',
      align: 'center'
    }
    };

    (chart = new ApexCharts(document.querySelector("#report-chart02"), options)).render()
    const body = document.querySelector('body')
    if (body.classList.contains('dark')) {
      apexChartUpdate(chart, {
        dark: true
      })
    }
  
    document.addEventListener('ChangeColorMode', function (e) {
      apexChartUpdate(chart, e.detail)
    })
  }
  if (jQuery('#report-chart2').length) {
    am4core.ready(function() {

      // Themes begin
      am4core.useTheme(am4themes_animated);
      // Themes end
      
      // create chart
      var chart = am4core.create("report-chart2", am4charts.TreeMap);
      chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect
      chart.colors.list = [am4core.color("#32bdea"),am4core.color("#ff7e41"), am4core.color("#e83e8c")];
      
      chart.data = [{
        name: "First",
        children: [
          {
            name: "",
            value: 130,
          },
          {
            name: "",
            value: 90,
          },
          {
            name: "",
            value: 80,
          }
        ]
      },
      {
        name: "Second",
        children: [
          {
            name: "",
            value: 150
          },
          {
            name: "",
            value: 40
          },
          {
            name: "",
            value: 100
          }
        ]
      },
      {
        name: "Third",
        children: [
          {
            name: "",
            value: 250
          },
          {
            name: "",
            value: 148
          },
          {
            name: "",
            value: 126
          },
          {
            name: "",
            value: 26
          }
        ]
      }];
      
      chart.colors.step = 2;
      
      // define data fields
      chart.dataFields.value = "value";
      chart.dataFields.name = "name";
      chart.dataFields.children = "children";
      
      chart.zoomable = false;
      var bgColor = new am4core.InterfaceColorSet().getFor("background");
      
      // level 0 series template
      var level0SeriesTemplate = chart.seriesTemplates.create("0");
      var level0ColumnTemplate = level0SeriesTemplate.columns.template;
      
      level0ColumnTemplate.column.cornerRadius(10, 10, 10, 10);
      level0ColumnTemplate.fillOpacity = 0;
      level0ColumnTemplate.strokeWidth = 4;
      level0ColumnTemplate.strokeOpacity = 0;
      
      // level 1 series template
      var level1SeriesTemplate = chart.seriesTemplates.create("1");
      var level1ColumnTemplate = level1SeriesTemplate.columns.template;
      
      level1SeriesTemplate.tooltip.animationDuration = 0;
      level1SeriesTemplate.strokeOpacity = 1;
      
      level1ColumnTemplate.column.cornerRadius(10, 10, 10, 10)
      level1ColumnTemplate.fillOpacity = 1;
      level1ColumnTemplate.strokeWidth = 4;
      level1ColumnTemplate.stroke = bgColor;
      
      var bullet1 = level1SeriesTemplate.bullets.push(new am4charts.LabelBullet());
      bullet1.locationY = 0.5;
      bullet1.locationX = 0.5;
      bullet1.label.text = "{name}";
      bullet1.label.fill = am4core.color("#ffffff");
      
      chart.maxLevels = 2;
      
      }); // end am4core.ready()
  }
  if (jQuery("#report-chart3").length) {
    options = {
      series: [
      {
        name: 'Bob',
        data: [
          {
            x: 'Design',
            y: [
              new Date('2019-03-05').getTime(),
              new Date('2019-03-08').getTime()
            ]
          },
          {
            x: 'Code',
            y: [
              new Date('2019-03-02').getTime(),
              new Date('2019-03-05').getTime()
            ]
          },
          {
            x: 'Code',
            y: [
              new Date('2019-03-05').getTime(),
              new Date('2019-03-07').getTime()
            ]
          },
          {
            x: 'Test',
            y: [
              new Date('2019-03-03').getTime(),
              new Date('2019-03-09').getTime()
            ]
          },
          {
            x: 'Test',
            y: [
              new Date('2019-03-08').getTime(),
              new Date('2019-03-11').getTime()
            ]
          },
          {
            x: 'Validation',
            y: [
              new Date('2019-03-11').getTime(),
              new Date('2019-03-16').getTime()
            ]
          },
          {
            x: 'Design',
            y: [
              new Date('2019-03-01').getTime(),
              new Date('2019-03-03').getTime()
            ]
          }
        ]
      },
      {
        name: 'Joe',
        data: [
          {
            x: 'Design',
            y: [
              new Date('2019-03-02').getTime(),
              new Date('2019-03-05').getTime()
            ]
          },
          {
            x: 'Test',
            y: [
              new Date('2019-03-06').getTime(),
              new Date('2019-03-16').getTime()
            ]
          },
          {
            x: 'Code',
            y: [
              new Date('2019-03-03').getTime(),
              new Date('2019-03-07').getTime()
            ]
          },
          {
            x: 'Deployment',
            y: [
              new Date('2019-03-20').getTime(),
              new Date('2019-03-22').getTime()
            ]
          },
          {
            x: 'Design',
            y: [
              new Date('2019-03-10').getTime(),
              new Date('2019-03-16').getTime()
            ]
          }
        ]
      },
      {
        name: 'Dan',
        data: [
          {
            x: 'Code',
            y: [
              new Date('2019-03-10').getTime(),
              new Date('2019-03-17').getTime()
            ]
          },
          {
            x: 'Validation',
            y: [
              new Date('2019-03-05').getTime(),
              new Date('2019-03-09').getTime()
            ]
          },
        ]
      }
    ],
      chart: {
      height: 350,
      type: 'rangeBar'
    },
    colors: ['#32BDEA', '#e83e8c', '#FF7E41'],
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '80%'
      }
    },
    xaxis: {
      type: 'datetime'
    },
    
    stroke: {
      width: 1
    },
    fill: {
      type: 'solid',
      opacity: 1
    },
    legend: {
      position: 'top',
      horizontalAlign: 'left'
    }
    };

     (chart = new ApexCharts(document.querySelector("#report-chart3"), options)).render()
     const body = document.querySelector('body')
     if (body.classList.contains('dark')) {
       apexChartUpdate(chart, {
         dark: true
       })
     }
   
     document.addEventListener('ChangeColorMode', function (e) {
       apexChartUpdate(chart, e.detail)
     })
  }
  if (jQuery("#report-chart4").length) {   
    options = {
      series: [{
      name: "SAMPLE A",
      data: [
      [16.4, 5.4], [10.9, 7.4],[10.9, 8.2], [16.4, 1.8], [13.6, 0.3],  [27.1, 2.3],  [13.6, 3.7], [10.9, 5.2], [16.4, 6.5],  [24.5, 7.1], [10.9, 0], [8.1, 4.7],  [21.7, 1.8], [29.9, 1.5], [27.1, 0.8], [22.1, 2]]
    },{
      name: "SAMPLE B",
      data: [
      [36.4, 13.4], [1.7, 11], [1.4, 7],  [3.6, 13.7], [1.9, 15.2], [6.4, 16.5], [0.9, 10], [4.5, 17.1], [10.9, 10], [0.1, 14.7], [9, 10], [12.7, 11.8], [2.1, 10], [2.5, 10], [27.1, 10], [2.9, 11.5], [7.1, 10.8], [2.1, 12]]
    },{
      name: "SAMPLE C",
      data: [
      [21.7, 3], [23.6, 3.5], [24.6, 3], [29.9, 3], [21.7, 20], [19, 5], [22.4, 3], [24.5, 3], [32.6, 3],  [21.6, 5], [20.9, 4], [22.4, 0], [32.6, 10.3], [29.7, 20.8], [24.5, 0.8], [21.4, 0], [21.7, 6.9], [28.6, 7.7]]
    }],
      chart: {
      height: 350,
      type: 'scatter',
      zoom: {
        enabled: true,
        type: 'xy'
      }
    },
    colors: ['#32BDEA', '#e83e8c', '#FF7E41'],
    xaxis: {
      tickAmount: 10,
      labels: {
        formatter: function(val) {
          return parseFloat(val).toFixed(1)
        }
      }
    },
    yaxis: {
      tickAmount: 7,
      show: true,
      labels: {
        minWidth: 20,
        maxWidth: 20
      }
    }
    };

     (chart = new ApexCharts(document.querySelector("#report-chart4"), options)).render()
     const body = document.querySelector('body')
     if (body.classList.contains('dark')) {
       apexChartUpdate(chart, {
         dark: true
       })
     }
   
     document.addEventListener('ChangeColorMode', function (e) {
       apexChartUpdate(chart, e.detail)
     })
  }

})(jQuery);


