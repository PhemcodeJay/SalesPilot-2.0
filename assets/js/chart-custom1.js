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


})(jQuery);


