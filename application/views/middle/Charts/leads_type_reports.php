<script src="<?php echo base_url().PLUGINS;?>highcharts/js/highcharts.js"></script>
<script src="<?php echo base_url().PLUGINS;?>highcharts/js/exporting.js"></script>
<script src = "<?php echo base_url().PLUGINS;?>highcharts/js/chart.js"></script>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Interested Leads Report
        </h3>
        <?php if(in_array($this->session->userdata('admin_type'),array('ZM','GM'))){ ?>
        <div class="float-right">
            <a href="<?php echo site_url('reports/index/leads_type_reports')?>" class="btn-Download">
                Grid View
            </a>
<!--            <span> | </span>-->
<!--            <a href="--><?php //echo site_url('charts/index/leads_type_reports/funnel')?><!--" class="btn-Download">-->
<!--                Funnel View-->
<!--            </a>-->
        </div>
        <?php }?>
    </div>
</div>
<span class="bg-top"></span>
<div class="inner-content">
<div id="container" style="min-width: 310px; height: auto; margin: 0 auto" class="report-chart"></div>
<?php
$lead_type = $this->config->item('lead_type');
$data = array();
$i = 0;
foreach ($lead_type as $key => $value){
    $data[$i]['name'] = $value;
    $data[$i]['data'] = $lead_identification[$key];
    if($key == 'HOT')
       $data[$i]['color'] = 'green';
    if($key == 'WARM')
        $data[$i]['color'] = 'yellow';
    if($key == 'COLD')
        $data[$i]['color'] = 'red';
    $i++;
}
?>
</div>
<span class="bg-bottom"></span>

<script type="application/javascript">
    //console.log($.type(<?php echo json_encode($data,JSON_NUMERIC_CHECK)?>));
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
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
        series: <?php echo json_encode($data,JSON_NUMERIC_CHECK)?>
    });
</script>