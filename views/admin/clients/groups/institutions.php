<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<h4 class="customer-profile-group-heading"><?php echo _l('institutions'); ?></h4>
	<?php if(has_permission('institutions','','create')){ ?>
		<a href="<?php echo admin_url('institutions/institution?customer_id='.$client->userid); ?>" class="btn btn-info mbot15<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('create_new_institution'); ?></a>
	<?php } ?>
	<?php if(has_permission('institutions','','view') || has_permission('institutions','','view_own') || get_option('allow_staff_view_institutions_assigned') == '1'){ ?>
		<a href="#" class="btn btn-info mbot15" data-toggle="modal" data-target="#client_zip_institutions"><?php echo _l('zip_institutions'); ?></a>
	<?php } ?>
	<div id="institutions_total"></div>
	<?php
	$this->load->view('admin/institutions/table_html', array('class'=>'institutions-single-client'));
	//$this->load->view('admin/clients/modals/zip_institutions');
	?>
<?php } ?>
