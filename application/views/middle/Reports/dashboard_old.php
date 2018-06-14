<?php
$lead_type = $this->config->item('lead_type');
$lead_status = $this->config->item('lead_status');
$lead_source = $this->config->item('lead_source');
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Dashboard
        </h3>

    </div>
</div>
<?php
//Form
$attributes = array(
    'role' => 'form',
    'id' => 'search_form',
    'class' => 'form',
    'autocomplete' => 'off'
);
echo form_open(site_url().'reports/index/dashboard', $attributes);
$data = array(
    'view'   => isset($view) ? $view : '',
    'zone_id'  => isset($zone_id) ? encode_id($zone_id) : '',
    'branch_id' => isset($branch_id) ? encode_id($branch_id) : ''
);

echo form_hidden($data);
?>
<div class="lead-form">
    <span class="bg-top"></span>
    <div class="inner-content">

        <div class="container">
            <div class="form">
                <p id="note"><span style="color:red;">*</span> These fields are required</p>
                <div class="lead-form-left" id="l-width">
                    <div class="form-control date-c">
                        <!-- <p>sdadasd</p> -->
                        <label>Start Date:<span style="color:red;">*</span></label>
                        <?php
                        if(isset($start_date)){
                            $start_date = date('d-m-Y',strtotime($start_date));
                        }else{
                            $start_date = '';
                        }
                        $data = array(
                            'type'  => 'text',
                            'name'  => 'start_date',
                            'id'    => 'start_date',
                            'class' => 'datepicker_recurring_start',
                            'value' => $start_date

                        );
                        echo form_input($data);
                        ?>
                    </div>

                </div>
                <div class="lead-form-right" id="r-width">
                    <div class="form-control endDate">
                        <label>End Date:<span style="color:red;">*</span></label>
                        <?php
                        if(isset($end_date)){
                            $end_date = date('d-m-Y',strtotime($end_date));
                        }else{
                            $end_date = '';
                        }
                        $data = array(
                            'type'  => 'text',
                            'name'  => 'end_date',
                            'id'    => 'end_date',
                            'class' => 'datepicker_recurring_start',
                            'value' => $end_date

                        );
                        echo form_input($data);
                        ?>
                    </div>

                    <div class="form-control form-submit clearfix">
                        <button type="submit" name="Submit" value="Submit" id="su" class="full-btn float-right">
                            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
                            <span class="btn-txt">Submit</span>
                            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
                        </button>    			    </div>
                </div>
            </div>
        </div>
        <img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" style="display:none;">

        <script type="text/javascript">
            $('.loader').show();
        </script>
        <!-- BEGIN LEADS -->
        <?php echo form_close();?>
        <div class="result result-dash" style="display:none;">
            <div class="page-content">
                <div class="container">
                    <table border="1">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Emp Adoption</th>
                                <th>Emp Adoption</th>
                                <th>Emp Usage</th>
                                <th>Branch Adoption</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="odd-dash">
                                <td></td>
                                <td>Unique employee logins (since inception)</td>
                                <td>Unique employee logins (Today)</td>
                                <td>Unique employees generating leads</td>
                                <td>Branches generating leads (at least 1)</td>
                            </tr>
                            <tr class="even-dash">
                                <td>Actual</td>
                                <td><?php echo $unique_login_count;?></td>
                                <td><?php echo $today_unique_login_count;?></td>
                                <td><?php echo $unique_leadcreator_employee_count;?></td>
                                <td><?php echo $unique_leadcreator_branch_count;?></td>
                            </tr>
                            <tr class="odd-dash">
                                <td>Base</td>
                                <td><?php echo $total_employee_count;?></td>
                                <td><?php echo $total_employee_count;?></td>
                                <td><?php echo $total_employee_count;?></td>
                                <td><?php echo $total_branch_count;?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>%</td>
                                <td><?php echo round(($unique_login_count/$total_employee_count)*100,2).'%';?></td>
                                <td><?php echo round(($today_unique_login_count/$total_employee_count)*100,2).'%';?></td>
                                <td><?php echo round(($unique_leadcreator_employee_count/$total_employee_count)*100,2).'%';?></td>
                                <td><?php echo round(($unique_leadcreator_branch_count/$total_branch_count)*100,2).'%';?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php
                    if(isset($leads) && !empty($leads)){

//                    pe($leads);
//                    pe($product_category);
                    foreach ($lead_source as $key=>$val) {
                        $total_generated=0;
                        $total_converted=0;
                        $total_estimated_business=0;
                        $total_actual_business=0;
                        ?>
                        <?php if (!empty($leads[$key])) {?>
                            <div class="page-title">
                                <div class="container clearfix">
                                    <h3 class="text-center"><?php echo $val;?></h3>
                                </div>
                            </div>
                        <table>
                        <tbody>
                        <tr class="odd-dash">
                            <td>Category</td>
                            <td># of input leads</td>
                            <td># of leads converted</td>
                            <td>% Conversion (#)</td>
                            <td>Business in Cr. (Input)</td>
                            <td>Business Converted (in Cr)</td>
                            <td> % Conversion (Amt)</td>
                        </tr>

                        <?php
                            $i=0;
                            foreach ($product_category as $row) {
                                $i++;

                                if(isset($leads[$key]['generated'][$row['id']]) && !empty($leads[$key]['generated'][$row['id']])){
                                    $total_generated += $leads[$key]['generated'][$row['id']];
                                }
                                if(isset($leads[$key]['converted'][$row['id']]) && !empty($leads[$key]['converted'][$row['id']])){
                                    $total_converted += $leads[$key]['converted'][$row['id']];
                                }
                                if(isset($leads[$key]['estimated_business'][$row['id']]) && !empty($leads[$key]['estimated_business'][$row['id']])){
                                    $total_estimated_business += convertCurrencyCr($leads[$key]['estimated_business'][$row['id']]);
                                }
                                if(isset($leads[$key]['actual_business'][$row['id']]) && !empty($leads[$key]['actual_business'][$row['id']])){
                                    $total_actual_business += convertCurrencyCr($leads[$key]['actual_business'][$row['id']]);
                                }
                                ?>
                                <tr <?php if($i%2 == 0){echo 'class="odd-dash"';}else{ echo 'class="even-dash"';};?>>
                                    <td><?php echo $row['title'];?></td>
                                    <td><?php echo (isset($leads[$key]['generated'][$row['id']]) && $leads[$key]['generated'][$row['id']])?$leads[$key]['generated'][$row['id']]:0;?></td>
                                    <td><?php echo (isset($leads[$key]['converted'][$row['id']]) && $leads[$key]['converted'][$row['id']])?$leads[$key]['converted'][$row['id']]:0;?></td>
                                    <td><?php echo (isset($leads[$key]['generated'][$row['id']]) && $leads[$key]['generated'][$row['id']] && isset($leads[$key]['converted'][$row['id']]) && $leads[$key]['converted'][$row['id']])?round(($leads[$key]['converted'][$row['id']]/$leads[$key]['generated'][$row['id']])*100,2).'%':'0.00%';?></td>
                                    <td><?php echo (isset($leads[$key]['estimated_business'][$row['id']]) && $leads[$key]['estimated_business'][$row['id']])?convertCurrencyCr($leads[$key]['estimated_business'][$row['id']]):0;?></td>
                                    <td><?php echo (isset($leads[$key]['actual_business'][$row['id']]) && $leads[$key]['actual_business'][$row['id']])?convertCurrencyCr($leads[$key]['actual_business'][$row['id']]):0;?></td>
                                    <td><?php echo (isset($leads[$key]['estimated_business'][$row['id']]) && $leads[$key]['estimated_business'][$row['id']] && isset($leads[$key]['actual_business'][$row['id']]) && $leads[$key]['actual_business'][$row['id']])?round(($leads[$key]['actual_business'][$row['id']]/$leads[$key]['estimated_business'][$row['id']])*100,2).'%':'0.00%';?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                            <tfoot>
                            <tr>
                                <td>Total</td>
                                <td><?php echo $total_generated;?></td>
                                <td><?php echo $total_converted;?></td>
                                <td><?php echo ($total_converted)?round(($total_converted/$total_generated)*100,2).'%':'0.00%';?></td>
                                <td><?php echo $total_estimated_business;?></td>
                                <td><?php echo $total_actual_business;?></td>
                                <td><?php echo ($total_actual_business)?round(($total_actual_business/$total_estimated_business)*100,2).'%':'0.00%';?></td>
                            </tr>
                            </tfoot>
                        </table>
                        <?php } ?>
                    <?php }?>
                </div>
            </div>
        </div>

    </div>

</div>
<?php
}else{?>
    <span class="no_result">No records found</span>
<?php }?>

<span class="bg-bottom"></span>
<!-- END LEADS-->
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#su').click(function(){
            $('#su').hide();
        })
    });
</script>
<script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>

