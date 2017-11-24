
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
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container ">
            <div class="lead-top clearfix">
                <?php if(isset($total_assigned_leads) && !empty($total_assigned_leads)){?>
                <div class="float-left">
                    <span class="total-lead">
                        Total Leads :
                    </span>
                    This Month (<span class="lead-num"><?php echo isset($total_assigned_leads_month) && !empty($total_assigned_leads_month) ? $total_assigned_leads_month : 0;?>)</span> /
                    This Year (<span class="lead-num"><?php echo isset($total_assigned_leads) && !empty($total_assigned_leads) ? $total_assigned_leads : 0;?>)</span>

                </div>
                <?php }?>
            </div>
        <table class="upload-table lead-table" id="sample_3">
            <thead>
            <tr class="top-header">
                <th></th>
                <th style="text-align:left">
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
                <th style="text-align:center">Sr. No</th>
                <th style="text-align:left">Status</th>
                <th style="text-align:center">This Month</th>
                <th style="text-align:center">This Year</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if(!empty($status)){ $i=0;
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
                    <td style="text-align:center">
                        <?php
                        echo ++$i;
                        ?>

                    </td>
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
                    <td style="text-align:center"><?php echo $Month; ?></td>
                    <td style="text-align:center"><?php echo $Year; ?></td>
                </tr>
            <?php
                }
            }
            ?>
            <?php if(isset($Unassigned_Leads['Month'])){?>
            <tr>
                <td style="text-align:center">
                    <?php
                    echo ++$i;
                    ?>

                </td>
                <td>Unassigned Leads</td>
                <td style="text-align:center"><?php echo $Unassigned_Leads['Month']; ?></td>
                <td style="text-align:center"><?php echo $Unassigned_Leads['Year']; ?></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [0];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>
