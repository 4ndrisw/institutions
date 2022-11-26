<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Institution_send_to_customer extends App_mail_template
{
    protected $for = 'customer';

    protected $institution;

    protected $contact;

    public $slug = 'institution-send-to-client';

    public $rel_type = 'institution';

    public function __construct($institution, $contact, $cc = '')
    {
        parent::__construct();

        $this->institution = $institution;
        $this->contact = $contact;
        $this->cc      = $cc;
    }

    public function build()
    {
        if ($this->ci->input->post('email_attachments')) {
            $_other_attachments = $this->ci->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->ci->institutions_model->get_attachments($this->institution->id, $attachment);
                $this->add_attachment([
                                'attachment' => get_upload_path_by_type('institution') . $this->institution->id . '/' . $_attachment->file_name,
                                'filename'   => $_attachment->file_name,
                                'type'       => $_attachment->filetype,
                                'read'       => true,
                            ]);
            }
        }

        $this->to($this->contact->email)
        ->set_rel_id($this->institution->id)
        ->set_merge_fields('client_merge_fields', $this->institution->clientid, $this->contact->id)
        ->set_merge_fields('institution_merge_fields', $this->institution->id);
    }
}
