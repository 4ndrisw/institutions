<?php defined('BASEPATH') or exit('No direct script access allowed');

class Myinstitution extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('institutions_model');
        $this->load->model('clients_model');
    }

    /* Get all institutions in case user go on index page */
    public function list($id = '')
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('institutions', 'admin/tables/table'));
        }
        $contact_id = get_contact_user_id();
        $user_id = get_user_id_by_contact_id($contact_id);
        $client = $this->clients_model->get($user_id);
        $data['institutions'] = $this->institutions_model->get_client_institutions($client);
        $data['institutionid']            = $id;
        $data['title']                 = _l('institutions_tracking');

        $data['bodyclass'] = 'institutions';
        $this->data($data);
        $this->view('themes/'. active_clients_theme() .'/views/institutions/institutions');
        $this->layout();
    }

    public function show($id, $hash)
    {
        check_institution_restrictions($id, $hash);
        $institution = $this->institutions_model->get($id);

        if (!is_client_logged_in()) {
            load_client_language($institution->clientid);
        }

        $identity_confirmation_enabled = get_option('institution_accept_identity_confirmation');

        if ($this->input->post('institution_action')) {
            $action = $this->input->post('institution_action');

            // Only decline and accept allowed
            if ($action == 4 || $action == 3) {
                $success = $this->institutions_model->mark_action_state($action, $id, true);

                $redURL   = $this->uri->uri_string();
                $accepted = false;

                if (is_array($success)) {
                    if ($action == 4) {
                        $accepted = true;
                        set_alert('success', _l('clients_institution_accepted_not_invoiced'));
                    } else {
                        set_alert('success', _l('clients_institution_declined'));
                    }
                } else {
                    set_alert('warning', _l('clients_institution_failed_action'));
                }
                if ($action == 4 && $accepted = true) {
                    process_digital_signature_image($this->input->post('signature', false), SCHEDULE_ATTACHMENTS_FOLDER . $id);

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'institutions', get_acceptance_info_array());
                }
            }
            redirect($redURL);
        }
        // Handle Institution PDF generator

        $institution_number = format_institution_number($institution->id);
        /*
        if ($this->input->post('institutionpdf')) {
            try {
                $pdf = institution_pdf($institution);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            //$institution_number = format_institution_number($institution->id);
            $companyname     = get_option('company_name');
            if ($companyname != '') {
                $institution_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }

            $filename = hooks()->apply_filters('customers_area_download_institution_filename', mb_strtoupper(slug_it($institution_number), 'UTF-8') . '.pdf', $institution);

            $pdf->Output($filename, 'D');
            die();
        }
        */

        $data['title'] = $institution_number;
        $this->disableNavigation();
        $this->disableSubMenu();

        $data['institution_number']              = $institution_number;
        $data['hash']                          = $hash;
        $data['can_be_accepted']               = false;
        $data['institution']                     = hooks()->apply_filters('institution_html_pdf_data', $institution);
        $data['bodyclass']                     = 'viewinstitution';
        $data['client_company']                = $this->clients_model->get($institution->clientid)->company;
        $setSize = get_option('institution_qrcode_size');

        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }
        $data['institution_members']  = $this->institutions_model->get_institution_members($institution->id,true);

        $qrcode_data  = '';
        $qrcode_data .= _l('institution_number') . ' : ' . $institution_number ."\r\n";
        $qrcode_data .= _l('institution_date') . ' : ' . $institution->date ."\r\n";
        $qrcode_data .= _l('institution_datesend') . ' : ' . $institution->datesend ."\r\n";
        //$qrcode_data .= _l('institution_assigned_string') . ' : ' . get_staff_full_name($institution->assigned) ."\r\n";
        //$qrcode_data .= _l('institution_url') . ' : ' . site_url('institutions/show/'. $institution->id .'/'.$institution->hash) ."\r\n";


        $institution_path = get_upload_path_by_type('institutions') . $institution->id . '/';
        _maybe_create_upload_path('uploads/institutions');
        _maybe_create_upload_path('uploads/institutions/'.$institution_path);

        $params['data'] = $qrcode_data;
        $params['writer'] = 'png';
        $params['setSize'] = isset($setSize) ? $setSize : 160;
        $params['encoding'] = 'UTF-8';
        $params['setMargin'] = 0;
        $params['setForegroundColor'] = ['r'=>0,'g'=>0,'b'=>0];
        $params['setBackgroundColor'] = ['r'=>255,'g'=>255,'b'=>255];

        $params['crateLogo'] = true;
        $params['logo'] = './uploads/company/favicon.png';
        $params['setResizeToWidth'] = 60;

        $params['crateLabel'] = false;
        $params['label'] = $institution_number;
        $params['setTextColor'] = ['r'=>255,'g'=>0,'b'=>0];
        $params['ErrorCorrectionLevel'] = 'hight';

        $params['saveToFile'] = FCPATH.'uploads/institutions/'.$institution_path .'assigned-'.$institution_number.'.'.$params['writer'];

        $this->load->library('endroid_qrcode');
        $this->endroid_qrcode->generate($params);

        $this->data($data);
        $this->app_scripts->theme('sticky-js', 'assets/plugins/sticky/sticky.js');
        $this->view('themes/'. active_clients_theme() .'/views/institutions/institutionhtml');
        add_views_tracking('institution', $id);
        hooks()->do_action('institution_html_viewed', $id);
        no_index_customers_area();
        $this->layout();
    }


    public function office($id, $hash)
    {
        check_institution_restrictions($id, $hash);
        $institution = $this->institutions_model->get($id);

        if (!is_client_logged_in()) {
            load_client_language($institution->clientid);
        }

        $identity_confirmation_enabled = get_option('institution_accept_identity_confirmation');

        if ($this->input->post('institution_action')) {
            $action = $this->input->post('institution_action');

            // Only decline and accept allowed
            if ($action == 4 || $action == 3) {
                $success = $this->institutions_model->mark_action_state($action, $id, true);

                $redURL   = $this->uri->uri_string();
                $accepted = false;

                if (is_array($success)) {
                    if ($action == 4) {
                        $accepted = true;
                        set_alert('success', _l('clients_institution_accepted_not_invoiced'));
                    } else {
                        set_alert('success', _l('clients_institution_declined'));
                    }
                } else {
                    set_alert('warning', _l('clients_institution_failed_action'));
                }
                if ($action == 4 && $accepted = true) {
                    process_digital_signature_image($this->input->post('signature', false), SCHEDULE_ATTACHMENTS_FOLDER . $id);

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'institutions', get_acceptance_info_array());
                }
            }
            redirect($redURL);
        }
        // Handle Institution PDF generator

        $institution_number = format_institution_number($institution->id);
        /*
        if ($this->input->post('institutionpdf')) {
            try {
                $pdf = institution_pdf($institution);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            //$institution_number = format_institution_number($institution->id);
            $companyname     = get_option('company_name');
            if ($companyname != '') {
                $institution_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }

            $filename = hooks()->apply_filters('customers_area_download_institution_filename', mb_strtoupper(slug_it($institution_number), 'UTF-8') . '.pdf', $institution);

            $pdf->Output($filename, 'D');
            die();
        }
        */

        $data['title'] = $institution_number;
        $this->disableNavigation();
        $this->disableSubMenu();

        $data['institution_number']              = $institution_number;
        $data['hash']                          = $hash;
        $data['can_be_accepted']               = false;
        $data['institution']                     = hooks()->apply_filters('institution_html_pdf_data', $institution);
        $data['bodyclass']                     = 'viewinstitution';
        $data['client_company']                = $this->clients_model->get($institution->clientid)->company;
        $setSize = get_option('institution_qrcode_size');

        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }
        $data['institution_members']  = $this->institutions_model->get_institution_members($institution->id,true);

        $qrcode_data  = '';
        $qrcode_data .= _l('institution_number') . ' : ' . $institution_number ."\r\n";
        $qrcode_data .= _l('institution_date') . ' : ' . $institution->date ."\r\n";
        $qrcode_data .= _l('institution_datesend') . ' : ' . $institution->datesend ."\r\n";
        //$qrcode_data .= _l('institution_assigned_string') . ' : ' . get_staff_full_name($institution->assigned) ."\r\n";
        //$qrcode_data .= _l('institution_url') . ' : ' . site_url('institutions/show/'. $institution->id .'/'.$institution->hash) ."\r\n";


        $institution_path = get_upload_path_by_type('institutions') . $institution->id . '/';
        _maybe_create_upload_path('uploads/institutions');
        _maybe_create_upload_path('uploads/institutions/'.$institution_path);

        $params['data'] = $qrcode_data;
        $params['writer'] = 'png';
        $params['setSize'] = isset($setSize) ? $setSize : 160;
        $params['encoding'] = 'UTF-8';
        $params['setMargin'] = 0;
        $params['setForegroundColor'] = ['r'=>0,'g'=>0,'b'=>0];
        $params['setBackgroundColor'] = ['r'=>255,'g'=>255,'b'=>255];

        $params['crateLogo'] = true;
        $params['logo'] = './uploads/company/favicon.png';
        $params['setResizeToWidth'] = 60;

        $params['crateLabel'] = false;
        $params['label'] = $institution_number;
        $params['setTextColor'] = ['r'=>255,'g'=>0,'b'=>0];
        $params['ErrorCorrectionLevel'] = 'hight';

        $params['saveToFile'] = FCPATH.'uploads/institutions/'.$institution_path .'assigned-'.$institution_number.'.'.$params['writer'];

        $this->load->library('endroid_qrcode');
        $this->endroid_qrcode->generate($params);

        $this->data($data);
        $this->app_scripts->theme('sticky-js', 'assets/plugins/sticky/sticky.js');
        $this->view('themes/'. active_clients_theme() .'/views/institutions/institution_office_html');
        add_views_tracking('institution', $id);
        hooks()->do_action('institution_html_viewed', $id);
        no_index_customers_area();
        $this->layout();
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
            redirect(admin_url('institutions'));
        }
        $institution        = $this->institutions_model->get($id);
        $institution_number = format_institution_number($institution->id);
        
        $institution->assigned_path = FCPATH . get_institution_upload_path('institution').$institution->id.'/assigned-'.$institution_number.'.png';
        $institution->acceptance_path = FCPATH . get_institution_upload_path('institution').$institution->id .'/'.$institution->signature;
        
        $institution->client_company = $this->clients_model->get($institution->clientid)->company;
        $institution->acceptance_date_string = _dt($institution->acceptance_date);


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

    /* Generates institution PDF and senting to email  */
    public function office_pdf($id)
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
            redirect(admin_url('institutions'));
        }
        $institution        = $this->institutions_model->get($id);
        $institution_number = format_institution_number($institution->id);
        
        $institution->assigned_path = FCPATH . get_institution_upload_path('institution').$institution->id.'/assigned-'.$institution_number.'.png';
        $institution->acceptance_path = FCPATH . get_institution_upload_path('institution').$institution->id .'/'.$institution->signature;
        
        $institution->client_company = $this->clients_model->get($institution->clientid)->company;
        $institution->acceptance_date_string = _dt($institution->acceptance_date);


        try {
            $pdf = institution_office_pdf($institution);
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
                            'file_name' => str_replace("SCH", "SCH-UPT", mb_strtoupper(slug_it($institution_number)) . '.pdf'),
                            'institution'  => $institution,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }
}
