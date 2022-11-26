<?php

use app\services\institutions\InstitutionsPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Institutions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('institutions_model');
        $this->load->model('clients_model');
        $this->load->model('staff_model');
    }

    /* Get all institutions in case user go on index page */
    public function index($id = '')
    {
        $this->list_institutions($id);
    }

    /* List all institutions datatables */
    public function list_institutions($id = '')
    {
        if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && get_option('allow_staff_view_institutions_assigned') == '0') {
            access_denied('institutions');
        }

        $isPipeline = $this->session->userdata('institution_pipeline') == 'true';

        $data['institution_states'] = $this->institutions_model->get_states();
        if ($isPipeline && !$this->input->get('state') && !$this->input->get('filter')) {
            $data['title']           = _l('institutions_pipeline');
            $data['bodyclass']       = 'institutions-pipeline institutions-total-manual';
            $data['switch_pipeline'] = false;

            if (is_numeric($id)) {
                $data['institutionid'] = $id;
            } else {
                $data['institutionid'] = $this->session->flashdata('institutionid');
            }

            $this->load->view('admin/institutions/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('state') || $this->input->get('filter') && $isPipeline) {
                $this->pipeline(0, true);
            }
            
            $data['institutionid']            = $id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('institutions');
            $data['bodyclass']             = 'institutions-total-manual';
            $data['institutions_years']       = $this->institutions_model->get_institutions_years();
            $data['institutions_sale_agents'] = $this->institutions_model->get_sale_agents();
            if($id){
                $this->load->view('admin/institutions/manage_small_table', $data);

            }else{
                $this->load->view('admin/institutions/manage_table', $data);

            }

        }
    }

    public function table($client_id = '')
    {
        if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && get_option('allow_staff_view_institutions_assigned') == '0') {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('institutions', 'admin/tables/table',[
            'client_id' => $client_id,
        ]));
    }

    /* Add new institution or update existing */
    public function institution($id = '')
    {
        if ($this->input->post()) {
            $institution_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($institution_data['save_and_send_later'])) {
                unset($institution_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if ($id == '') {
                if (!has_permission('institutions', '', 'create')) {
                    access_denied('institutions');
                }
                $institution_data['is_institution'] = '1';
                $next_institution_number = get_option('next_institution_number');
                $_format = get_option('institution_number_format');
                $_prefix = get_option('institution_prefix');
                
                $prefix  = isset($institution->prefix) ? $institution->prefix : $_prefix;
                $number_format  = isset($institution->number_format) ? $institution->number_format : $_format;
                $number  = isset($institution->number) ? $institution->number : $next_institution_number;

                $institution_data['prefix'] = $prefix;
                $institution_data['number_format'] = $number_format;
                $date = date('Y-m-d');
                
                //$institution_data['formatted_number'] = institution_number_format($number, $format, $prefix, $date);
                //var_dump($institution_data);
                //die();
                $id = $this->institutions_model->add($institution_data);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('institution')));

                    $redUrl = admin_url('institutions/list_institutions/' . $id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_institution_pipeline_autoload($id) ? $redUrl : admin_url('institutions/list_institutions/')
                    );
                }
            } else {
                if (has_permission('institutions', '', 'edit') || 
                   (has_permission('institutions', '', 'edit_own') && is_staff_related_to_institution($id))
                   ) {
                  
                    $success = $this->institutions_model->update($institution_data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('institution')));
                    }
                    if ($this->set_institution_pipeline_autoload($id)) {
                        redirect(admin_url('institutions/list_institutions/'));
                    } else {
                        redirect(admin_url('institutions/list_institutions/' . $id));
                    }
                }else{
                    access_denied('institutions');
                }
            }
        }
        if ($id == '') {
            $title = _l('create_new_institution');
        } else {
            $institution = $this->institutions_model->get($id);

            if (!$institution || !user_can_view_institution($id)) {
                blank_page(_l('institution_not_found'));
            }
            $data['institution'] = $institution;
            $data['edit']     = true;
            $title            = _l('edit', _l('institution_lowercase'));
        }

        $data['institution_states'] = $this->institutions_model->get_states();
        $data['title']             = $title;
        $this->load->view('admin/institutions/institution', $data);
    }
    
    public function clear_signature($id)
    {
        if (has_permission('institutions', '', 'delete')) {
            $this->institutions_model->clear_signature($id);
        }

        redirect(admin_url('institutions/list_institutions/' . $id));
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (has_permission('institutions', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'institutions', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('institution'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_institution_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'institutions', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->institutions_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Get all institution data used when user click on institution number in a datatable left side*/
    public function get_institution_data_ajax($id, $to_return = false)
    {
        if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && get_option('allow_staff_view_institutions_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No institution found');
        }

        $institution = $this->institutions_model->get($id);

        if (!$institution || !user_can_view_institution($id)) {
            echo _l('institution_not_found');
            die;
        }

        // $data = prepare_mail_preview_data($template_name, $institution->clientid);
        $data['title'] = 'Form add / Edit Staff';
        $data['activity']          = $this->institutions_model->get_institution_activity($id);
        $data['institution']          = $institution;
        $data['member']           = $this->staff_model->get('', ['active' => 1, 'client_id'=>$id]);
        $data['institution_states'] = $this->institutions_model->get_states();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'institution']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($to_return == false) {
            $this->load->view('admin/institutions/institution_preview_template', $data);
        } else {
            return $this->load->view('admin/institutions/institution_preview_template', $data, true);
        }
    }

    public function get_institutions_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->institutions_model->get_institutions_total($this->input->post());

            $this->load->model('currencies_model');

            if (!$this->input->post('customer_id')) {
                $multiple_currencies = call_user_func('is_using_multiple_currencies', db_prefix() . 'institutions');
            } else {
                $multiple_currencies = call_user_func('is_client_using_multiple_currencies', $this->input->post('customer_id'), db_prefix() . 'institutions');
            }

            if ($multiple_currencies) {
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['institutions_years'] = $this->institutions_model->get_institutions_years();

            if (
                count($data['institutions_years']) >= 1
                && !\app\services\utilities\Arr::inMultidimensional($data['institutions_years'], 'year', date('Y'))
            ) {
                array_unshift($data['institutions_years'], ['year' => date('Y')]);
            }

            $data['_currency'] = $data['totals']['currencyid'];
            unset($data['totals']['currencyid']);
            $this->load->view('admin/institutions/institutions_total_template', $data);
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_institution($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'institution', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_institution($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'institution');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_state($state, $id)
    {
        if (!has_permission('institutions', '', 'edit') || !has_permission('institutions', '', 'edit_own')) {
            access_denied('institutions');
        }
        $success = $this->institutions_model->mark_action_state($state, $id);
        if ($success) {
            set_alert('success', _l('institution_state_changed_success'));
        } else {
            set_alert('danger', _l('institution_state_changed_fail'));
        }
        if ($this->set_institution_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('institutions/list_institutions/' . $id));
        }
    }

    public function send_expiry_reminder($id)
    {
        $canView = user_can_view_institution($id);
        if (!$canView) {
            access_denied('Institutions');
        } else {
            if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && $canView == false) {
                access_denied('Institutions');
            }
        }

        $success = $this->institutions_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_institution_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('institutions/list_institutions/' . $id));
        }
    }

    /* Send institution to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_institution($id);
        if (!$canView) {
            access_denied('institutions');
        } else {
            if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && $canView == false) {
                access_denied('institutions');
            }
        }

        try {
            $success = $this->institutions_model->send_institution_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('institution_sent_to_client_success'));
        } else {
            set_alert('danger', _l('institution_sent_to_client_fail'));
        }
        if ($this->set_institution_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('institutions/list_institutions/' . $id));
        }
    }

    /* Convert institution to invoice */
    public function convert_to_invoice($id)
    {
        if (!has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        if (!$id) {
            die('No institution found');
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }
        $invoiceid = $this->institutions_model->convert_to_invoice($id, false, $draft_invoice);
        if ($invoiceid) {
            set_alert('success', _l('institution_convert_to_invoice_successfully'));
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        } else {
            if ($this->session->has_userdata('institution_pipeline') && $this->session->userdata('institution_pipeline') == 'true') {
                $this->session->set_flashdata('institutionid', $id);
            }
            if ($this->set_institution_pipeline_autoload($id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('institutions/list_institutions/' . $id));
            }
        }
    }

    public function copy($id)
    {
        if (!has_permission('institutions', '', 'create')) {
            access_denied('institutions');
        }
        if (!$id) {
            die('No institution found');
        }
        $new_id = $this->institutions_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('institution_copied_successfully'));
            if ($this->set_institution_pipeline_autoload($new_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('institutions/institution/' . $new_id));
            }
        }
        set_alert('danger', _l('institution_copied_fail'));
        if ($this->set_institution_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('institutions/institution/' . $id));
        }
    }

    /* Delete institution */
    public function delete($id)
    {
        if (!has_permission('institutions', '', 'delete')) {
            access_denied('institutions');
        }
        if (!$id) {
            redirect(admin_url('institutions/list_institutions'));
        }
        $success = $this->institutions_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_institution_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('institution')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('institution_lowercase')));
        }
        redirect(admin_url('institutions/list_institutions'));
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'institutions', get_acceptance_info_array(true));
        }

        redirect(admin_url('institutions/list_institutions/' . $id));
    }

    /* Generates institution PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_institution($id);
        if (!$canView) {
            access_denied('Institutions');
        } else {
            if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && $canView == false) {
                access_denied('Institutions');
            }
        }
        if (!$id) {
            redirect(admin_url('institutions/list_institutions'));
        }
        $institution        = $this->institutions_model->get($id);
        $institution_number = format_institution_number($institution->id);

        try {
            $pdf = institution_pdf($institution);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('institution_file_name_admin_area', [
                            'file_name' => mb_strtoupper(slug_it($institution_number)) . '.pdf',
                            'institution'  => $institution,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }

    // Pipeline
    public function get_pipeline()
    {
        if (has_permission('institutions', '', 'view') || has_permission('institutions', '', 'view_own') || get_option('allow_staff_view_institutions_assigned') == '1') {
            $data['institution_states'] = $this->institutions_model->get_states();
            $this->load->view('admin/institutions/pipeline/pipeline', $data);
        }
    }

    public function pipeline_open($id)
    {
        $canView = user_can_view_institution($id);
        if (!$canView) {
            access_denied('Institutions');
        } else {
            if (!has_permission('institutions', '', 'view') && !has_permission('institutions', '', 'view_own') && $canView == false) {
                access_denied('Institutions');
            }
        }

        $data['userid']       = $id;
        $data['institution'] = $this->get_institution_data_ajax($id, true);
        $this->load->view('admin/institutions/pipeline/institution', $data);
    }

    public function update_pipeline()
    {
        if (has_permission('institutions', '', 'edit') || has_permission('institutions', '', 'edit_own')) {
            $this->institutions_model->update_pipeline($this->input->post());
        }
    }

    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'institution_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('institutions/list_institutions'));
        }
    }

    public function pipeline_load_more()
    {
        $state = $this->input->get('state');
        $page   = $this->input->get('page');

        $institutions = (new InstitutionsPipeline($state))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($institutions as $institution) {
            $this->load->view('admin/institutions/pipeline/_kanban_card', [
                'institution' => $institution,
                'state'   => $state,
            ]);
        }
    }

    public function set_institution_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('institution_pipeline')
                && $this->session->userdata('institution_pipeline') == 'true') {
            $this->session->set_flashdata('institutionid', $id);

            return true;
        }

        return false;
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('institution_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('institution_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
/*
    public function get_staff($userid='')
    {
        $this->app->get_table_data(module_views_path('institutions', 'admin/tables/staff'));
    }
*/
    public function table_staffs($client_id,$institution = true)
    {
        if (
            !has_permission('institutions', '', 'view')
            && !has_permission('institutions', '', 'view_own')
            && get_option('allow_staff_view_institutions_assigned') == 0
        ) {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('institutions', 'admin/tables/staff'), array('client_id'=>$client_id));
    }


}
