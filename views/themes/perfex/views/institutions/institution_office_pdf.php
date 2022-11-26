<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('institution_office_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . str_replace("SCH","SCH-UPT",$institution_number) . '</b>';

if (get_option('show_state_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . institution_state_color_pdf($state) . ');text-transform:uppercase;">' . format_institution_state($state, '', false) . '</span>';
}

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(8);

$organization_info = '<div style="color:#424242;">';
    $organization_info .= format_organization_info();
$organization_info .= '</div>';

// Institution to
$institution_info = '<b>' . _l('institution_office_to') . '</b>';
$institution_info .= '<div style="color:#424242;">';
$institution_info .= format_office_info($institution->office, 'institution', 'billing');
$institution_info .= '</div>';

$left_info  = $swap == '1' ? $institution_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $institution_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// Institution to
$left_info ='<p>' . _l('institution_opening') . ',</p>';
$left_info .= _l('institution_client');
$left_info .= '<div style="color:#424242;">';
$left_info .= format_customer_info($institution, 'institution', 'billing');
$left_info .= '</div>';

$right_info = '';

$pdf->ln(4);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 1) - $dimensions['lm']);

$organization_info = '<strong>'. _l('institution_members') . ': </strong><br />';

$CI = &get_instance();
$CI->load->model('institutions_model');
$institution_members = $CI->institutions_model->get_institution_members($institution->id,true);
$i=1;
foreach($institution_members as $member){
  $organization_info .=  $i.'. ' .$member['firstname'] .' '. $member['lastname']. '<br />';
  $i++;
}

$institution_info = '<br />' . _l('institution_data_date') . ': ' . _d($institution->date) . '<br />';


if ($institution->program_id != 0 && get_option('show_program_on_institution') == 1) {
    $institution_info .= _l('program') . ': ' . get_program_name_by_id($institution->program_id) . '<br />';
}


$left_info  = $swap == '1' ? $institution_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $institution_info;

$pdf->ln(4);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_institution_items_table_data($institution, 'institution', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->SetFont($font_name, '', $font_size);

$assigned_path = <<<EOF
        <img width="150" height="150" src="$institution->assigned_path">
    EOF;    
$assigned_info = '<div style="text-align:center;">';
    $assigned_info .= get_option('invoice_company_name') . '<br />';
    $assigned_info .= $assigned_path . '<br />';

if ($institution->assigned != 0 && get_option('show_assigned_on_institutions') == 1) {
    $assigned_info .= get_staff_full_name($institution->assigned);
}
$assigned_info .= '</div>';

$acceptance_path = <<<EOF
    <img src="$institution->acceptance_path">
EOF;
$client_info = '<div style="text-align:center;">';
    $client_info .= $institution->client_company .'<br />';

if ($institution->signed != 0) {
    $client_info .= _l('institution_signed_by') . ": {$institution->acceptance_firstname} {$institution->acceptance_lastname}" . '<br />';
    $client_info .= _l('institution_signed_date') . ': ' . _dt($institution->acceptance_date_string) . '<br />';
    $client_info .= _l('institution_signed_ip') . ": {$institution->acceptance_ip}" . '<br />';

    $client_info .= $acceptance_path;
    $client_info .= '<br />';
}
$client_info .= '</div>';


$left_info  = $swap == '1' ? $client_info : $assigned_info;
$right_info = $swap == '1' ? $assigned_info : $client_info;
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(2);   
$companyname = get_option('companyname');
$pdf->writeHTMLCell('', '', '', '', _l('institution_crm_info', $companyname), 0, 1, false, true, 'L', true);
