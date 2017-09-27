<script src="<?php echo base_url().ASSETS;?>/js/highcharts.js"></script>
<script src="<?php echo base_url().ASSETS;?>/js/exporting.js"></script>
<script src = "<?php echo base_url().ASSETS;?>/js/chart.js"></script>

<div id="container" style="min-width: 310px; height: auto; margin: 0 auto"></div>
<script type="application/javascript">
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Leads Generated Vs Converted'
        },
        xAxis: {
            categories: <?php echo json_encode($zone_name)?>
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Leads'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        series: [{
            name: 'Leads Generated',
            data: <?php echo json_encode($generated_count,JSON_NUMERIC_CHECK)?>
        }, {
            name: 'Leads Converted',
            data: <?php echo json_encode($converted_count,JSON_NUMERIC_CHECK)?>
        }]
    });
</script>