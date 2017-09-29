<style>
    #chartdiv {
        width: 100%;
        height: 500px;
    }
    .amcharts-chart-div a {display:none !important;}
</style>
<script src="<?php echo base_url().PLUGINS;?>amcharts/js/amcharts.js"></script>
<script src="<?php echo base_url().PLUGINS;?>amcharts/js/funnel.js"></script>
<script src="<?php echo base_url().PLUGINS;?>amcharts/js/export.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url().PLUGINS;?>amcharts/css/export.css" type="text/css" media="all" />
<!--<script src="https://www.amcharts.com/lib/3/themes/none.js"></script>-->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Leads Identification
        </h3>
        <div class="float-right">
            <a href="<?php echo site_url('reports/index/leads_type_reports')?>" class="btn-Download">
                Grid View
            </a>
        </div>
    </div>
</div>
<div id="chartdiv"></div>
<?php
$lead_type = $this->config->item('lead_type');
$data = array();
$i = 0;
foreach ($lead_type as $key => $value){
    $data[$i]['title'] = $value;
    $data[$i]['value'] = array_sum($lead_identification[$key]);
    $i++;
}
?>
<script type="application/javascript">
    console.log(<?php echo json_encode((array)$data,JSON_NUMERIC_CHECK)?>);
    var chart = AmCharts.makeChart( "chartdiv", {
        "type": "funnel",
        "theme": "none",
        "dataProvider": <?php echo json_encode($data,JSON_NUMERIC_CHECK)?>,
        "titleField": "title",
        "marginRight": 200,
        "marginLeft": 15,
        "labelPosition": "right",
        "funnelAlpha": 0.9,
        "valueField": "value",
        "startX": 0,
        "neckWidth": "20%",
        "startAlpha": 0,
        "outlineThickness": 1,
        "neckHeight": "30%",
        "balloonText": "[[title]]:<b>[[value]]</b>",
        "export": {
            "enabled": true
        }
    } );
</script>