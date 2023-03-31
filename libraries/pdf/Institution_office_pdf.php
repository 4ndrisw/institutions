<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Institution_office_pdf extends App_pdf
{
    protected $institution;

    private $institution_number;

    public function __construct($institution, $tag = '')
    {
        $this->load_language($institution->clientid);

        $institution                = hooks()->apply_filters('institution_html_pdf_data', $institution);
        $GLOBALS['institution_pdf'] = $institution;

        parent::__construct();

        $this->tag             = $tag;
        $this->institution        = $institution;
        $this->institution_number = format_institution_number($this->institution->id);

        $this->SetTitle(str_replace("SCH", "SCH-UPT", $this->institution_number));
    }

    public function prepare()
    {

        $this->set_view_vars([
            'state'          => $this->institution->state,
            'institution_number' => str_replace("SCH", "SCH-UPT", $this->institution_number),
            'institution'        => $this->institution,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'institution';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_institution_office_pdf.php';
        $actualPath = module_views_path('institutions','themes/' . active_clients_theme() . '/views/institutions/institution_office_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
