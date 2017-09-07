
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<?php 
    $status = $this->config->item('lead_status');
    $title = ucwords($type).' Leads';
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center"><?php echo $title;?></h3>
        <?php //echo isset($emp_id) ? '<h4 class="text-center">'. $emp_id .'</h4>' : "";?>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <table class="upload-table lead-table" id="sample_3">
            <thead>
            <tr class="top-header">
                <th></th>
                <th>
                    <?php
                        $options['']='Select Status';
                        foreach ($status as $key => $value) {
                            $options[$value] = $value;
                        }
                        echo form_dropdown('status', $options ,'',array());
                    ?>
                </th>
                <th><!-- <input type="text" name="customername" placeholder="Search MTD"> --></th>
                <th><!-- <input type="text" name="customername" placeholder="Search YTD"> --></th>
            </tr>
            <tr>
                <th>Sr No</th>
                <th>Status</th>
                <th>This Month</th>
                <th>This Year</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if(!empty($status)){
                $i = 0;
                foreach ($status as $key => $value){ 
                    $param = '';
                    if(isset($employee_id) && !empty($employee_id)){
                        $param = '/'.encode_id($employee_id);
                    }else if(isset($branch_id) && !empty($branch_id)){
                        $param = '/'.encode_id($branch_id);
                    }else if(isset($zone_id) && !empty($zone_id)){
                        $param = '/'.encode_id($zone_id);
                    }
                    if(isset($lead_source) && !empty($lead_source)){
                        $param .= '/'.$lead_source;
                    }
                ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $value; ?></td>
                    <?php
                        switch ($key) {
                            case 'NC':
                                $Month = $NC['Month'];
                                $Year = $NC['Year'];
                                break;
                            case 'FU':
                                $Month = $FU['Month'];
                                $Year = $FU['Year'];
                                break;
                            case 'DC':
                                $Month = $DC['Month'];
                                $Year = $DC['Year'];
                                break;
                            case 'AO':
                                $Month = $AO['Month'];
                                $Year = $AO['Year'];
                                break;
                            case 'Converted':
                                $Month = $Converted['Month'];
                                $Year = $Converted['Year'];
                                break;
                            case 'NI':
                                $Month = $NI['Month'];
                                $Year = $NI['Year'];
                                break;
                            case 'CBC':
                                $Month = $CBC['Month'];
                                $Year = $CBC['Year'];
                                break;
                            case 'Closed':
                                $Month = $Closed['Month'];
                                $Year = $Closed['Year'];
                                break;
                        }
                    ?>
                    <td><a href="<?php echo site_url('leads/leads_list/'.$type.'/mtd/'.$key.$param);?>" ><?php echo $Month; ?></a></td>
                    <td><a href="<?php echo site_url('leads/leads_list/'.$type.'/ytd/'.$key.$param);?>" ><?php echo $Year; ?></a></td>
                </tr>
            <?php
            $i++; 
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>
