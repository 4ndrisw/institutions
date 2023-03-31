<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="institution-html-logo">
               <?php echo get_dark_company_logo(); ?>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <div class="top" data-sticky data-sticky-class="preview-sticky-header">
      <div class="container preview-sticky-container">
         <div class="row">
            <div class="col-md-12">
               <div class="col-md-3">
                  <h3 class="bold no-mtop institution-html-number no-mbot">
                     <span class="sticky-visible hide">
                     <?php echo format_institution_number($institution->id); ?>
                     </span>
                  </h3>
                  <h4 class="institution-html-state mtop7">
                     <?php echo format_institution_state($institution->state,'',true); ?>
                  </h4>
               </div>
               <div class="col-md-9">         
                  <?php
                     // Is not accepted, declined and expired
                     if ($institution->state != 4 && $institution->state != 3 && $institution->state != 5) {
                       $can_be_accepted = true;
                       if($identity_confirmation_enabled == '0'){
                         echo form_open($this->uri->uri_string(), array('class'=>'pull-right mtop7 action-button'));
                         echo form_hidden('institution_action', 4);
                         echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_institution').'</button>';
                         echo form_close();
                       } else {
                         echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_institution').'</button>';
                       }
                     } else if($institution->state == 3){
                       if (($institution->expirydate >= date('Y-m-d') || !$institution->expirydate) && $institution->state != 5) {
                         $can_be_accepted = true;
                         if($identity_confirmation_enabled == '0'){
                           echo form_open($this->uri->uri_string(),array('class'=>'pull-right mtop7 action-button'));
                           echo form_hidden('institution_action', 4);
                           echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_institution').'</button>';
                           echo form_close();
                         } else {
                           echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_institution').'</button>';
                         }
                       }
                     }
                     // Is not accepted, declined and expired
                     if ($institution->state != 4 && $institution->state != 3 && $institution->state != 5) {
                       echo form_open($this->uri->uri_string(), array('class'=>'pull-right action-button mright5 mtop7'));
                       echo form_hidden('institution_action', 3);
                       echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-default action-button accept"><i class="fa fa-remove"></i> '._l('clients_decline_institution').'</button>';
                       echo form_close();
                     }
                     ?>
                  <?php echo form_open(site_url('institutions/pdf/'.$institution->id), array('class'=>'pull-right action-button')); ?>
                  <button type="submit" name="institutionpdf" class="btn btn-default action-button download mright5 mtop7" value="institutionpdf">
                  <i class="fa fa-file-pdf-o"></i>
                  <?php echo _l('clients_invoice_html_btn_download'); ?>
                  </button>
                  <?php echo form_close(); ?>
                  <?php if((is_client_logged_in() && has_contact_permission('institutions'))  || is_staff_member()){ ?>
                  <a href="<?php echo site_url('clients/institutions/'); ?>" class="btn btn-default pull-right mright5 mtop7 action-button go-to-portal">
                  <?php echo _l('client_go_to_dashboard'); ?>
                  </a>
                  <?php } ?>
               </div>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="panel_s mtop20">
   <div class="panel-body">
      <div class="col-md-10 col-md-offset-1">
         <div class="row mtop20">
            <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
               <h4 class="bold institution-html-number"><?php echo format_institution_number($institution->id); ?></h4>
               <address class="institution-html-company-info">
                  <?php echo format_organization_info(); ?>
               </address>
            </div>
            <div class="col-sm-6 text-right transaction-html-info-col-right">
               <span class="bold institution_to"><?php echo _l('institution_to'); ?>:</span>
               <address class="institution-html-customer-billing-info">
                  <?php echo format_customer_info($institution, 'institution', 'billing'); ?>
               </address>
               <!-- shipping details -->
               <?php if($institution->include_shipping == 1 && $institution->show_shipping_on_institution == 1){ ?>
               <span class="bold institution_ship_to"><?php echo _l('ship_to'); ?>:</span>
               <address class="institution-html-customer-shipping-info">
                  <?php echo format_customer_info($institution, 'institution', 'shipping'); ?>
               </address>
               <?php } ?>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
               <div class="container-fluid">
                  <?php if(!empty($institution_members)){ ?>
                     <strong><?= _l('institution_members') ?></strong>
                     <ul class="institution_members">
                     <?php 
                        foreach($institution_members as $member){
                          echo ('<li style="list-style:auto" class="member">' . $member['firstname'] .' '. $member['lastname'] .'</li>');
                         }
                     ?>
                     </ul>
                  <?php } ?>
               </div>
            </div>
            <div class="col-md-6 text-right">
               <p class="no-mbot institution-html-date">
                  <span class="bold">
                  <?php echo _l('institution_data_date'); ?>:
                  </span>
                  <?php echo _d($institution->date); ?>
               </p>
               <?php if(!empty($institution->expirydate)){ ?>
               <p class="no-mbot institution-html-expiry-date">
                  <span class="bold"><?php echo _l('institution_data_expiry_date'); ?></span>:
                  <?php echo _d($institution->expirydate); ?>
               </p>
               <?php } ?>
               <?php if(!empty($institution->reference_no)){ ?>
               <p class="no-mbot institution-html-reference-no">
                  <span class="bold"><?php echo _l('reference_no'); ?>:</span>
                  <?php echo $institution->reference_no; ?>
               </p>
               <?php } ?>
               <?php if($institution->program_id != 0 && get_option('show_program_on_institution') == 1){ ?>
               <p class="no-mbot institution-html-program">
                  <span class="bold"><?php echo _l('program'); ?>:</span>
                  <?php echo get_program_name_by_id($institution->program_id); ?>
               </p>
               <?php } ?>
               <?php $pdf_custom_fields = get_custom_fields('institution',array('show_on_pdf'=>1,'show_on_client_portal'=>1));
                  foreach($pdf_custom_fields as $field){
                    $value = get_custom_field_value($institution->id,$field['id'],'institution');
                    if($value == ''){continue;} ?>
               <p class="no-mbot">
                  <span class="bold"><?php echo $field['name']; ?>: </span>
                  <?php echo $value; ?>
               </p>
               <?php } ?>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12">
               <div class="table-responsive">
                  <?php
                     $items = get_institution_items_table_data($institution, 'institution');
                     echo $items->table();
                  ?>
               </div>
            </div>


            <div class="row mtop25">
               <div class="col-md-12">
                  <div class="col-md-6 text-center">
                     <div class="bold"><?php echo get_option('invoice_company_name'); ?></div>
                     <div class="qrcode text-center">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_institution_upload_path('institution').$institution->id.'/assigned-'.$institution_number.'.png')); ?>" class="img-responsive center-block institution-assigned" alt="institution-<?= $institution->id ?>">
                     </div>
                     <div class="assigned">
                     <?php if($institution->assigned != 0 && get_option('show_assigned_on_institutions') == 1){ ?>
                        <?php echo get_staff_full_name($institution->assigned); ?>
                     <?php } ?>

                     </div>
                  </div>
                     <div class="col-md-6 text-center">
                       <div class="bold"><?php echo $client_company; ?></div>
                       <?php if(!empty($institution->signature)) { ?>
                           <div class="bold">
                              <p class="no-mbot"><?php echo _l('institution_signed_by') . ": {$institution->acceptance_firstname} {$institution->acceptance_lastname}"?></p>
                              <p class="no-mbot"><?php echo _l('institution_signed_date') . ': ' . _dt($institution->acceptance_date) ?></p>
                              <p class="no-mbot"><?php echo _l('institution_signed_ip') . ": {$institution->acceptance_ip}"?></p>
                           </div>
                           <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                           <?php if($institution->signed == 1 && has_permission('institutions','','delete')){ ?>
                              <a href="<?php echo admin_url('institutions/clear_signature/'.$institution->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                                 <i class="fa fa-remove"></i>
                              </a>
                           <?php } ?>
                           </p>
                           <div class="customer_signature text-center">
                              <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_institution_upload_path('institution').$institution->id.'/'.$institution->signature)); ?>" class="img-responsive center-block institution-signature" alt="institution-<?= $institution->id ?>">
                           </div>
                       <?php } ?>
                     </div>
               </div>
            </div>




            <?php if(!empty($institution->clientnote)){ ?>
            <div class="col-md-12 institution-html-note">
            <hr />
               <b><?php echo _l('institution_order'); ?></b><br /><?php echo $institution->clientnote; ?>
            </div>
            <?php } ?>
            <?php if(!empty($institution->terms)){ ?>
            <div class="col-md-12 institution-html-terms-and-conditions">
               <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><?php echo $institution->terms; ?>
            </div>
            <?php } ?>

         </div>
      </div>
   </div>
</div>
<?php
   if($identity_confirmation_enabled == '1' && $can_be_accepted){
    get_template_part('identity_confirmation_form',array('formData'=>form_hidden('institution_action',4)));
   }
   ?>
<script>
   $(function(){
     new Sticky('[data-sticky]');
   })
</script>
