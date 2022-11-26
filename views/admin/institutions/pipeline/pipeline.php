<?php defined('BASEPATH') or exit('No direct script access allowed');
$i = 0;
$has_permission_edit = has_permission('institutions','','edit');
foreach ($institution_states as $state) {
  $kanBan = new \app\services\institutions\InstitutionsPipeline($state);
  $kanBan->search($this->input->get('search'))
    ->sortBy($this->input->get('sort_by'),$this->input->get('sort'));
    if($this->input->get('refresh')) {
        $kanBan->refresh($this->input->get('refresh')[$state] ?? null);
    }
  $institutions = $kanBan->get();
  $total_institutions = count($institutions);
  $total_pages = $kanBan->totalPages();
 ?>
 <ul class="kan-ban-col" data-col-state-id="<?php echo $state; ?>" data-total-pages="<?php echo $total_pages; ?>" data-total="<?php echo $total_institutions; ?>">
  <li class="kan-ban-col-wrapper">
    <div class="border-right panel_s no-mbot">
      <div class="panel-heading-bg <?php echo institution_state_color_class($state); ?>-bg institution-state-pipeline-<?php echo institution_state_color_class($state); ?>">
        <div class="kan-ban-step-indicator<?php if($i == count($institution_states) -1){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
        <?php echo institution_state_by_id($state); ?> - <?php echo $kanBan->countAll() . ' ' . _l('institutions') ?>
      </div>
      <div class="kan-ban-content-wrapper">
        <div class="kan-ban-content">
          <ul class="sortable<?php if($has_permission_edit){echo ' state pipeline-state';} ?>" data-state-id="<?php echo $state; ?>">
            <?php
            foreach ($institutions as $institution) {
              $this->load->view('admin/institutions/pipeline/_kanban_card',array('institution'=>$institution,'state'=>$state));
            } ?>
            <?php if($total_institutions > 0 ){ ?>
              <li class="text-center not-sortable kanban-load-more" data-load-state="<?php echo $state; ?>">
                <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1 || $kanBan->getPage() === $total_pages){echo ' disabled';} ?>" data-page="<?php echo $kanBan->getPage(); ?>" onclick="kanban_load_more(<?php echo $state; ?>,this,'institutions/pipeline_load_more',310,360); return false;";><?php echo _l('load_more'); ?></a>
              </li>
            <?php } ?>
            <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_institutions > 0){echo ' hide';} ?>">
              <h4>
                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                <?php echo _l('no_institutions_found'); ?></h4>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
    <?php $i++; } ?>
