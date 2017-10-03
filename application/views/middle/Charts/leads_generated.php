<script src="<?php echo base_url().PLUGINS;?>highcharts/js/highcharts.js"></script>
<script src="<?php echo base_url().PLUGINS;?>highcharts/js/exporting.js"></script>
<script src = "<?php echo base_url().PLUGINS;?>highcharts/js/chart.js"></script>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Leads Generated
        </h3>
        <div class="float-right">
            <a href="<?php echo site_url('reports/index/leads_generated')?>" class="btn-Download">
                Grid View
            </a>
        </div>
    </div>
</div>
<div id="container" style="min-width: 310px; height: auto; margin: 0 auto"></div>
<?php
$lead_status = $this->config->item('lead_status');
$data = array();
$i = 0;
foreach ($lead_status as $key => $value){
    $data[$i]['name'] = $value;
    $data[$i]['data'] = $status[$key];
    $i++;
}
?>
<script type="application/javascript">
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Leads Generated'
        },
        xAxis: {
            categories: <?php echo json_encode($zone_name)?>
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Generated Leads'
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
        series: <?php echo json_encode($data,JSON_NUMERIC_CHECK)?>
    });
</script>