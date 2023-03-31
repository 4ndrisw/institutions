<?php defined('BASEPATH') or exit('No direct script access allowed');
   if ($institution['state'] == $state) { ?>
<li data-institution-id="<?php echo $institution['id']; ?>" class="<?php if($institution['invoiceid'] != NULL){echo 'not-sortable';} ?>">
   <div class="panel-body">
      <div class="row">
         <div class="col-md-12">
            <h4 class="bold pipeline-heading"><a href="<?php echo admin_url('institutions/list_institutions/'.$institution['id']); ?>" onclick="institution_pipeline_open(<?php echo $institution['id']; ?>); return false;"><?php echo format_institution_number($institution['id']); ?></a>
               <?php if(has_permission('institutions','','edit')){ ?>
               <a href="<?php echo admin_url('institutions/institution/'.$institution['id']); ?>" target="_blank" class="pull-right"><small><i class="fa fa-pencil-square-o" aria-hidden="true"></i></small></a>
               <?php } ?>
            </h4>
            <span class="inline-block full-width mbot10">
            <a href="<?php echo admin_url('clients/client/'.$institution['clientid']); ?>" target="_blank">
            <?php echo $institution['company']; ?>
            </a>
            </span>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-md-8">
                  <span class="bold">
                  <?php echo _l('institution_total') . ':' . app_format_money($institution['total'], $institution['currency_name']); ?>
                  </span>
                  <br />
                  <?php echo _l('institution_data_date') . ': ' . _d($institution['date']); ?>
                  <?php if(is_date($institution['expirydate']) || !empty($institution['expirydate'])){
                     echo '<br />';
                     echo _l('institution_data_expiry_date') . ': ' . _d($institution['expirydate']);
                     } ?>
               </div>
               <div class="col-md-4 text-right">
                  <small><i class="fa fa-paperclip"></i> <?php echo _l('institution_notes'); ?>: <?php echo total_rows(db_prefix().'notes', array(
                     'rel_id' => $institution['id'],
                     'rel_type' => 'institution',
                     )); ?></small>
               </div>
               <?php $tags = get_tags_in($institution['id'],'institution');
                  if(count($tags) > 0){ ?>
               <div class="col-md-12">
                  <div class="mtop5 kanban-tags">
                     <?php echo render_tags($tags); ?>
                  </div>
               </div>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</li>
<?php } ?>
