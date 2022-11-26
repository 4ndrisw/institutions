<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$institution->userid); ?>
<?php echo form_hidden('_attachment_sale_type','institution'); ?>
<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_institution" aria-controls="tab_institution" role="tab" data-toggle="tab">
                     <?php echo _l('institution'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo $institution->userid; ?>,'institution'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                     <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_staffs" onclick="initDataTable('.table-staffs', admin_url + 'institutions/table_staffs/' + <?php echo $institution->userid ;?> + '/' + 'institution', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_staffs" role="tab" data-toggle="tab">
                     <?php echo _l('institution_staffs'); ?>
                     <?php
                        $total_staffs = total_rows(db_prefix().'staff',
                          array(
                           'is_not_staff'=>1,
                           //'staff'=>get_staff_user_id(),
                           'client_type'=>'institution',
                           'client_id'=>$institution->userid
                           )
                          );
                        if($total_staffs > 0){
                          echo '<span class="badge">'.$total_staffs.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                     <?php echo _l('institution_view_activity_tooltip'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $institution->userid ;?> + '/' + 'institution', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                     <?php echo _l('institution_reminders'); ?>
                     <?php
                        $total_reminders = total_rows(db_prefix().'reminders',
                          array(
                           'isnotified'=>0,
                           'staff'=>get_staff_user_id(),
                           'rel_type'=>'institution',
                           'rel_id'=>$institution->userid
                           )
                          );
                        if($total_reminders > 0){
                          echo '<span class="badge">'.$total_reminders.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_notes" onclick="get_sales_notes(<?php echo $institution->userid; ?>,'institutions'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                     <?php echo _l('institution_notes'); ?>
                     <span class="notes-total">
                        <?php if($totalNotes > 0){ ?>
                           <span class="badge"><?php echo $totalNotes; ?></span>
                        <?php } ?>
                     </span>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" title="<?php echo _l('emails_tracking'); ?>" class="tab-separator">
                     <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab">
                     <?php if(!is_mobile()){ ?>
                     <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                     <?php } else { ?>
                     <?php echo _l('emails_tracking'); ?>
                     <?php } ?>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('view_tracking'); ?>" class="tab-separator">
                     <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
                     <?php if(!is_mobile()){ ?>
                     <i class="fa fa-eye"></i>
                     <?php } else { ?>
                     <?php echo _l('view_tracking'); ?>
                     <?php } ?>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand"></i></a>
                  </li>
               </ul>
            </div>
         </div>
         <div class="row mtop10">
            <div class="col-md-3">
               <?php echo format_institution_state($institution->active,'mtop5');  ?>
            </div>
            <div class="col-md-9">
               <div class="visible-xs">
                  <div class="mtop10"></div>
               </div>
               <div class="pull-right _buttons">
                  <?php if(staff_can('edit', 'institutions') || staff_can('edit_own', 'institutions')){ ?>
                  <a href="<?php echo admin_url('institutions/institution/'.$institution->userid); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit_institution_tooltip'); ?>" data-placement="bottom"><i class="fa-solid fa-pen-to-square"></i></a>
                  <?php } ?>
                  <div class="btn-group">
                     <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-regular fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                     <ul class="dropdown-menu dropdown-menu-right">
                        <li class="hidden-xs"><a href="<?php echo admin_url('institutions/pdf/'.$institution->userid.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                        <li class="hidden-xs"><a href="<?php echo admin_url('institutions/pdf/'.$institution->userid.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                        <li><a href="<?php echo admin_url('institutions/pdf/'.$institution->userid); ?>"><?php echo _l('download'); ?></a></li>
                        <li>
                           <a href="<?php echo admin_url('institutions/pdf/'.$institution->userid.'?print=true'); ?>" target="_blank">
                           <?php echo _l('print'); ?>
                           </a>
                        </li>
                     </ul>
                  </div>
                  <?php
                     $_tooltip = _l('institution_sent_to_email_tooltip');
                     $_tooltip_already_send = '';
                     if($institution->active == 1){
                        $_tooltip_already_send = _l('institution_already_send_to_client_tooltip', time_ago($institution->dateactivated));
                     }
                     ?>

                  <div class="btn-group">
                     <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php echo _l('more'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">
                        
                        <?php hooks()->do_action('after_institution_view_as_client_link', $institution); ?>
                        
                        <li>
                           <a href="#" data-toggle="modal" data-target="#sales_attach_file"><?php echo _l('invoice_attach_file'); ?></a>
                        </li>

                        <?php if(staff_can('create', 'institutions')){ ?>
                        <li>
                           <a href="<?php echo admin_url('institutions/copy/'.$institution->userid); ?>">
                           <?php echo _l('copy_institution'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <?php if(staff_can('delete', 'institutions')){ ?>
                        <?php
                           if((get_option('delete_only_on_last_institution') == 1 && is_last_institution($institution->userid)) || (get_option('delete_only_on_last_institution') == 0)){ ?>
                        <li>
                           <a href="<?php echo admin_url('institutions/delete/'.$institution->userid); ?>" class="text-danger delete-text _delete"><?php echo _l('delete_institution_tooltip'); ?></a>
                        </li>
                        <?php
                           }
                           }
                           ?>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_institution">
               <?php if(isset($institution->scheduled_email) && $institution->scheduled_email) { ?>
                     <div class="alert alert-warning">
                        <?php echo _l('invoice_will_be_sent_at', _dt($institution->scheduled_email->scheduled_at)); ?>
                        <?php if(staff_can('edit', 'institutions') || $institution->addedfrom == get_staff_user_id()) { ?>
                           <a href="#"
                           onclick="edit_institution_scheduled_email(<?php echo $institution->scheduled_email->id; ?>); return false;">
                           <?php echo _l('edit'); ?>
                        </a>
                     <?php } ?>
                  </div>
               <?php } ?>
               <div id="institution-preview">
                  <div class="row">
                     <?php if($institution->active == 4 && !empty($institution->acceptance_firstname) && !empty($institution->acceptance_lastname) && !empty($institution->acceptance_email)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-info mbot15">
                           <?php echo _l('accepted_identity_info',array(
                              _l('institution_lowercase'),
                              '<b>'.$institution->acceptance_firstname . ' ' . $institution->acceptance_lastname . '</b> (<a href="mailto:'.$institution->acceptance_email.'">'.$institution->acceptance_email.'</a>)',
                              '<b>'. _dt($institution->acceptance_date).'</b>',
                              '<b>'.$institution->acceptance_ip.'</b>'.(is_admin() ? '&nbsp;<a href="'.admin_url('institutions/clear_acceptance_info/'.$institution->userid).'" class="_delete text-muted" data-toggle="tooltip" data-title="'._l('clear_this_information').'"><i class="fa fa-remove"></i></a>' : '')
                              )); ?>
                        </div>
                     </div>
                     <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold">
                           <a href="<?php echo admin_url('institutions/institution/'.$institution->userid); ?>">
                           <span id="institution-number">
                           <?php echo format_institution_number($institution->userid); ?>
                           </span>
                           </a>
                        </h4>
                        <address>
                           <?php echo format_institution_info($institution); ?>
                        </address>
                     </div>
                     <div class="col-sm-6 text-right">

                     </div>
                  </div>

               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id'=>$institution->userid,'data-new-rel-type'=>'institution')); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_staffs">
                <?php if (has_permission('pengguna', '', 'create')) { ?>
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="<?php echo admin_url('institutions/staff/add/'. $institution->userid); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new_staff'); ?>
                    </a>
                </div>
                <?php } ?>
               <hr />
               <?php 
               //render_datatable(array( _l( 'staff_description'), _l( 'staff_date'), _l( 'staff_staff'), _l( 'staff_is_notified')), 'staffs'); 

                        $table_data = [
                            _l('staff_dt_name'),
                            _l('staff_dt_email'),
                            _l('staff_dt_last_Login'),
                            _l('staff_dt_active'),
                        ];
                        render_datatable($table_data, 'staffs');
               ?>
               <?php //$this->load->view('admin/includes/modals/staff',array('id'=>$institution->userid,'name'=>'institution','member'=>$member,'staff_title'=>_l('institution_set_staff_title'))); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-institution-<?php echo $institution->userid; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('institution_set_reminder_title'); ?></a>
               <hr />
               <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$institution->userid,'name'=>'institution','members'=>isset($members) ? $members : [],'reminder_title'=>_l('institution_set_reminder_title'))); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_emails_tracking">
               <?php
                  $this->load->view('admin/includes/emails_tracking',array(
                     'tracked_emails'=>
                     get_tracked_emails($institution->userid, 'institution'))
                  );
                  ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_notes">
               <?php echo form_open(admin_url('institutions/add_note/'.$institution->userid),array('id'=>'sales-notes','class'=>'institution-notes-form')); ?>
               <?php echo render_textarea('description'); ?>
               <div class="text-right">
                  <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('institution_add_note'); ?></button>
               </div>
               <?php echo form_close(); ?>
               <hr />
               <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_activity">
               <div class="row">
                  <div class="col-md-12">
                     <div class="activity-feed">
                        <?php foreach($activity as $activity){
                           $_custom_data = false;
                           ?>
                        <div class="feed-item" data-sale-activity-id="<?php echo $activity['id']; ?>">
                           <div class="date">
                              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($activity['date']); ?>">
                              <?php echo time_ago($activity['date']); ?>
                              </span>
                           </div>
                           <div class="text">
                              <?php if(is_numeric($activity['staffid']) && $activity['staffid'] != 0){ ?>
                              <a href="<?php echo admin_url('profile/'.$activity["staffid"]); ?>">
                              <?php echo staff_profile_image($activity['staffid'],array('staff-profile-xs-image pull-left mright5'));
                                 ?>
                              </a>
                              <?php } ?>
                              <?php
                                 $additional_data = '';
                                 if(!empty($activity['additional_data'])){
                                  $additional_data = unserialize($activity['additional_data']);
                                  $i = 0;
                                  foreach($additional_data as $data){
                                    if(strpos($data,'<original_active>') !== false){
                                      $original_active = get_string_between($data, '<original_active>', '</original_active>');
                                      $additional_data[$i] = format_institution_state($original_active,'',false);
                                    } else if(strpos($data,'<new_active>') !== false){
                                      $new_active = get_string_between($data, '<new_active>', '</new_active>');
                                      $additional_data[$i] = format_institution_state($new_active,'',false);
                                    } else if(strpos($data,'<active>') !== false){
                                      $active = get_string_between($data, '<active>', '</active>');
                                      $additional_data[$i] = format_institution_state($active,'',false);
                                    } else if(strpos($data,'<custom_data>') !== false){
                                      $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                      unset($additional_data[$i]);
                                    }
                                    $i++;
                                  }
                                 }
                                 $_formatted_activity = _l($activity['description'],$additional_data);
                                 if($_custom_data !== false){
                                 $_formatted_activity .= '<br />';
                                 $_formatted_activity .= '<p>';
                                 $_formatted_activity .= $_custom_data;
                                 $_formatted_activity .= '</p>';
                                 }
                                 if(!empty($activity['full_name'])){
                                 $_formatted_activity = $activity['full_name'] . ' - ' . $_formatted_activity;
                                 }
                                 echo $_formatted_activity;
                                 if(is_admin()){
                                 echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity('.$activity['id'].'); return false;"><i class="fa fa-remove"></i></a>';
                                 }
                                 ?>
                           </div>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_views">
               <?php
                  $views_activity = get_views_tracking('institution',$institution->userid);
                  if(count($views_activity) === 0) {
                     echo '<h4 class="no-mbot">'._l('not_viewed_yet',_l('institution_lowercase')).'</h4>';
                  }
                  foreach($views_activity as $activity){ ?>
               <p class="text-success no-margin">
                  <?php echo _l('view_date') . ': ' . _dt($activity['date']); ?>
               </p>
               <p class="text-muted">
                  <?php echo _l('view_ip') . ': ' . $activity['view_ip']; ?>
               </p>
               <hr />
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();
   <?php if($send_later) { ?>
      institution_institution_send(<?php echo $institution->userid; ?>);
   <?php } ?>
</script>
<?php //$this->load->view('admin/institutions/institution_send_to_client'); ?>
