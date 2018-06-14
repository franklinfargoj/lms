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
                        </button>

                        <?php if(isset($leads) && !empty($leads)){ ?>
                            <button type="button" onclick="tablesToExcel(['emp_adopt', 'branch_generated', 'other_agent', 'key_metrics'], ['Adaption and Usage', 'Branch Generated', 'Other Agent', 'Key Metrics'], 'Masters.xls', 'Excel')" class="full-btn float-right">
                                <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
                                <span class="btn-txt">Download</span>
                                <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
                            </button>
                        <?php }else{ ?>
                            <button type="button" onclick="tablesToExcel(['emp_adopt'], ['Adaption and Usage'], 'Masters.xls', 'Excel')" class="full-btn float-right">
                                <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
                                <span class="btn-txt">Download</span>
                                <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
                            </button>
                        <?php } ?>



<!--                        <button type="button" onclick="exportTableToExcel('emp_adopt', 'members-data')" id="Download" name="Download" value="Download" class="full-btn float-right">-->
<!--                            <img src="--><?php //echo base_url().ASSETS;?><!--images/left-nav.png" alt="left-nav" class="left-btn-img">-->
<!--                            <span class="btn-txt">Download</span>-->
<!--                            <input type="hidden" value="0" id="download_flag" name="download_flag">-->
<!--                            <img src="--><?php //echo base_url().ASSETS;?><!--images/right-nav.png" alt="left-nav" class="right-btn-img">-->
<!--                        </button>-->


                    </div>
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
                    <table id="emp_adopt" border="1">
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
                            <td>Unique employee logins (As on today)</td>
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
<!--                        <tfoot>-->
                        <tr style="background-color: #5E6469;">
                            <td style="color:#F9F5D0">%</td>
                            <td style="color:#F9F5D0"><?php echo round(($unique_login_count/$total_employee_count)*100,2).'%';?></td>
                            <td style="color:#F9F5D0"><?php echo round(($today_unique_login_count/$total_employee_count)*100,2).'%';?></td>
                            <td style="color:#F9F5D0"><?php echo round(($unique_leadcreator_employee_count/$total_employee_count)*100,2).'%';?></td>
                            <td style="color:#F9F5D0"><?php echo round(($unique_leadcreator_branch_count/$total_branch_count)*100,2).'%';?></td>
                        </tr>
<!--                        </tfoot>-->
                    </table>

                    <div class="page-title">
                        <div class="container clearfix">
                            <h3 class="text-center">Metrics</h3>
                        </div>
                    </div>


                    <?php
                    if(isset($leads) && !empty($leads)){

                    $total_estimated_business_in_cr=0;
                    $total_actual_business_in_cr = 0;
                    $total_conversion_in_cr = '0.00%';
                    //                    pe($product_category);
                    foreach ($lead_source as $key=>$val) {
                        $total_generated=0;
                        $total_converted=0;
                        $total_estimated_business=0;
                        $total_actual_business=0;

//
                        ?>
                        <?php if (!empty($leads[$key])) {?>
                            <div class="page-title">
                                <div class="container clearfix">
                                    <h3 class="text-center"><?php echo $val;?></h3>
                                </div>
                            </div>

                            <?php
                                if($val == "Other Agent"){
                                    $id = 'other_agent';
                                }
                                elseif($val == "Branch Generated"){
                                    $id = 'branch_generated';
                                }
                                else{
                                    $id = '';
                                }

                            ?>

                            <table id="<?php echo $id; ?>">
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
                                    $sumGenCurrentSaving = 0;
                                    $sumGenTermRecurring = 0;
                                    $sumConvCurrentSaving = 0;
                                    $sumConvTermRecurring = 0;

                                    $casaConversion = 0;
                                    $termConversion = 0;

                                    $casaBusinessConversion = 0;
                                    $termBusinessConversion = 0;

                                    $sumEstimatedCurrentSaving = 0;
                                    $sumEstimatedTermRecurring = 0;

                                    $sumActualCurrentSaving = 0;
                                    $sumActualTermRecurring = 0;


                                   foreach($product as $productId => $products){
                                       if(isset($leadData[$key]['generated'][$productId])){
                                           if($productId == SAVING || $productId == CURRENT){
                                               $sumGenCurrentSaving += $leadData[$key]['generated'][$productId];
                                           }
                                           if($productId == TERM || $productId == RECURRING){
                                               $sumGenTermRecurring += $leadData[$key]['generated'][$productId];
                                           }
                                       }

                                       if(isset($leadData[$key]['converted'][$productId])){
                                           if($productId == SAVING || $productId == CURRENT){
                                               $sumConvCurrentSaving += $leadData[$key]['converted'][$productId];
                                           }
                                           if($productId == TERM || $productId == RECURRING){
                                               $sumConvTermRecurring += $leadData[$key]['converted'][$productId];
                                           }
                                       }


                                       if(isset($leadData[$key]['estimated_business'][$productId])){
                                           if($productId == SAVING || $productId == CURRENT){
                                               $sumEstimatedCurrentSaving += $leadData[$key]['estimated_business'][$productId];
                                           }
                                           if($productId == TERM || $productId == RECURRING){
                                               $sumEstimatedTermRecurring += $leadData[$key]['estimated_business'][$productId];
                                           }
                                       }

                                       if(isset($leadData[$key]['actual_business'][$productId])){
                                           if($productId == SAVING || $productId == CURRENT){
                                               $sumActualCurrentSaving += $leadData[$key]['actual_business'][$productId];
                                           }
                                           if($productId == TERM || $productId == RECURRING){
                                               $sumActualTermRecurring += $leadData[$key]['actual_business'][$productId];
                                           }
                                       }
                                   }

                                   if($sumConvCurrentSaving!= 0 && $sumGenCurrentSaving!=0) {
                                       $casaConversion = round($sumConvCurrentSaving / $sumGenCurrentSaving * 100, 2) . '%';
                                   }else{
                                       $casaConversion = "0.00%";
                                   }

                                    if($sumConvTermRecurring!= 0 && $sumGenTermRecurring!=0) {
                                        $termConversion = round($sumConvTermRecurring / $sumGenTermRecurring * 100, 2) . '%';
                                    }else{
                                        $termConversion = "0.00%";
                                    }



                                    if($sumActualCurrentSaving!= 0 && $sumEstimatedCurrentSaving !=0) {
                                        $casaBusinessConversion = round($sumActualCurrentSaving / $sumEstimatedCurrentSaving * 100, 2) . '%';
                                    }else{
                                        $casaBusinessConversion = "0.00%";
                                    }

                                    if($sumActualTermRecurring!= 0 && $sumEstimatedTermRecurring!=0) {
                                        $termBusinessConversion = round($sumActualTermRecurring / $sumEstimatedTermRecurring * 100, 2) . '%';
                                    }else{
                                        $termBusinessConversion = "0.00%";
                                    }
                                ?>

                                <tr class="even-dash">
                                    <td>CASA</td>
                                    <td><?php echo $sumGenCurrentSaving;?></td>
                                    <td><?php echo $sumConvCurrentSaving;?></td>
                                    <td><?php echo $casaConversion;?></td>
                                    <td><?php echo convertCurrencyCr($sumEstimatedCurrentSaving);?></td>
                                    <td><?php echo convertCurrencyCr($sumActualCurrentSaving);?></td>
                                    <td><?php echo $casaBusinessConversion;?></td>
                                </tr>

                                <tr class="odd-dash">
                                    <td>Term Deposit</td>
                                    <td><?php echo $sumGenTermRecurring;?></td>
                                    <td><?php echo $sumConvTermRecurring?></td>
                                    <td><?php echo $termConversion;?></td>
                                    <td><?php echo convertCurrencyCr($sumEstimatedTermRecurring);?></td>
                                    <td><?php echo convertCurrencyCr($sumActualTermRecurring);?></td>
                                    <td><?php echo $termBusinessConversion;?></td>
                                </tr>

                                <?php
                                $i=0;
                                foreach ($product_category as $row) {
                                    $i++;


                                    if($key == 'walkin') {
                                        if (isset($leads[$key]['estimated_business'][$row['id']]) && !empty($leads[$key]['generated'][$row['id']])) {
                                            $total_estimated_business_in_cr += convertCurrencyCr($leads[$key]['estimated_business'][$row['id']]);
                                        }
                                        if(isset($leads[$key]['actual_business'][$row['id']]) && !empty($leads[$key]['actual_business'][$row['id']])){
                                            $total_actual_business_in_cr += convertCurrencyCr($leads[$key]['actual_business'][$row['id']]);
                                        }

                                        if($total_actual_business_in_cr != 0 || $total_estimated_business_in_cr != 0) {
                                            $total_conversion_in_cr = round(($total_actual_business_in_cr / $total_estimated_business_in_cr) * 100, 2) . '%';
                                        }else{
                                            $total_conversion_in_cr = '0.00%';
                                        }
                                    }



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



                                    if($row['id'] == DEPOSIT){
                                        continue;
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

                                </tfoot>
                            </table>
                        <?php } ?>
                    <?php }?>








                    <table id="key_metrics">
                        <tbody>
                        <tr class="odd-dash">
                            <td>Key metrics</td>
                            <td>Actuals(today)</td>
                            <td>Percentage</td>
                            <td>Actuals (yesterday)</td>
                            <td>Delta</td>
                        </tr>
                        <?php
                        $uniqueEmployeeLoginYesterday = $unique_login_count - $today_unique_login_count;
                        $uniqueEmployeeLoginDelta = $unique_login_count - $uniqueEmployeeLoginYesterday;
                        ?>
                        <tr class="even-dash">
                            <td>Unique employee logins(As of today)</td>
                            <td><?php echo $unique_login_count;?></td>
                            <td><?php echo round(($unique_login_count/$total_employee_count)*100,2).'%';?></td>
                            <td><?php echo $uniqueEmployeeLoginYesterday;?></td>
                            <td><?php echo $uniqueEmployeeLoginDelta;?></td>
                        </tr>

                        <?php
                        $uniqueEmployeeLoginTodayDelta = $today_unique_login_count - 0;
                        ?>
                        <tr class="odd-dash">
                            <td>Unique employee logins(Today)</td>
                            <td><?php echo $today_unique_login_count;?></td>
                            <td><?php echo round(($today_unique_login_count/$total_employee_count)*100,2).'%';?></td>
                            <td>-</td>
                            <td><?php echo $uniqueEmployeeLoginTodayDelta;?></td>
                        </tr>

                        <?php
                        $uniqueEmployeeGeneratingDelta = $unique_leadcreator_employee_count - 0;
                        ?>

                        <tr class="even-dash">
                            <td>Unique employees generating leads</td>
                            <td><?php echo $unique_leadcreator_employee_count;?></td>
                            <td><?php echo round(($unique_leadcreator_employee_count/$total_employee_count)*100,2).'%';?></td>
                            <td>TBD</td>
                            <td><?php echo $uniqueEmployeeGeneratingDelta;?></td>
                        </tr>

                        <?php
                           $uniqueBusinessInCroreDelta = $total_estimated_business_in_cr - 0;
                        ?>
                        <tr class="odd-dash">
                            <td>Business in Cr(input)</td>
                            <td><?php echo $total_estimated_business_in_cr;?></td>
                            <td>-</td>
                            <td>TBD</td>
                            <td><?php echo $uniqueBusinessInCroreDelta;?></td>
                        </tr>


                        <?php
                        $uniqueBusinessConvertedDelta = $total_actual_business_in_cr - 0;
                        ?>

                        <tr class="even-dash">
                            <td>Business Converted(in Cr)</td>
                            <td><?php echo $total_actual_business_in_cr;?></td>
                            <td><?php echo $total_conversion_in_cr;?></td>
                            <td>TBD</td>
                            <td><?php echo $uniqueBusinessConvertedDelta;?></td>
                        </tr>

                        </tbody>
                    </table>
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
        });
    });

    var tablesToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,'
            , tmplWorkbookXML = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            + '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office"><Author>Axel Richter</Author><Created>{created}</Created></DocumentProperties>'
            + '<Styles>'
            + '<Style ss:ID="Currency"><NumberFormat ss:Format="Currency"></NumberFormat></Style>'
            + '<Style ss:ID="Date"><NumberFormat ss:Format="Medium Date"></NumberFormat></Style>'
            + '</Styles>'
            + '{worksheets}</Workbook>'
            , tmplWorksheetXML = '<Worksheet ss:Name="{nameWS}"><Table>{rows}</Table></Worksheet>'
            , tmplCellXML = '<Cell{attributeStyleID}{attributeFormula}><Data ss:Type="{nameType}">{data}</Data></Cell>'
            , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
        return function(tables, wsnames, wbname, appname) {
            var ctx = "";
            var workbookXML = "";
            var worksheetsXML = "";
            var rowsXML = "";

            for (var i = 0; i < tables.length; i++) {
                if (!tables[i].nodeType) tables[i] = document.getElementById(tables[i]);
                for (var j = 0; j < tables[i].rows.length; j++) {
                    rowsXML += '<Row>'
                    for (var k = 0; k < tables[i].rows[j].cells.length; k++) {
                        var dataType = tables[i].rows[j].cells[k].getAttribute("data-type");
                        var dataStyle = tables[i].rows[j].cells[k].getAttribute("data-style");
                        var dataValue = tables[i].rows[j].cells[k].getAttribute("data-value");
                        dataValue = (dataValue)?dataValue:tables[i].rows[j].cells[k].innerHTML;
                        var dataFormula = tables[i].rows[j].cells[k].getAttribute("data-formula");
                        dataFormula = (dataFormula)?dataFormula:(appname=='Calc' && dataType=='DateTime')?dataValue:null;
                        ctx = {  attributeStyleID: (dataStyle=='Currency' || dataStyle=='Date')?' ss:StyleID="'+dataStyle+'"':''
                            , nameType: (dataType=='Number' || dataType=='DateTime' || dataType=='Boolean' || dataType=='Error')?dataType:'String'
                            , data: (dataFormula)?'':dataValue
                            , attributeFormula: (dataFormula)?' ss:Formula="'+dataFormula+'"':''
                        };
                        rowsXML += format(tmplCellXML, ctx);
                    }
                    rowsXML += '</Row>'
                }
                ctx = {rows: rowsXML, nameWS: wsnames[i] || 'Sheet' + i};
                worksheetsXML += format(tmplWorksheetXML, ctx);
                rowsXML = "";
            }

            ctx = {created: (new Date()).getTime(), worksheets: worksheetsXML};
            workbookXML = format(tmplWorkbookXML, ctx);



            var link = document.createElement("A");
            link.href = uri + base64(workbookXML);
            link.download = wbname || 'Workbook.xls';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    })();
</script>
<script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>

