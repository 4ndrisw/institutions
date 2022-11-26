<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Institutions
Description: Default module for defining institutions
Version: 1.0.1
Requires at least: 2.3.*
*/

define('INSTITUTIONS_MODULE_NAME', 'institutions');
define('INSTITUTION_ATTACHMENTS_FOLDER', 'uploads/institutions/');

hooks()->add_filter('before_institution_updated', '_format_data_institution_feature');
hooks()->add_filter('before_institution_added', '_format_data_institution_feature');

hooks()->add_action('after_cron_run', 'institutions_notification');
hooks()->add_action('admin_init', 'institutions_module_init_menu_items');
hooks()->add_action('admin_init', 'institutions_permissions');
hooks()->add_action('admin_init', 'institutions_settings_tab');
hooks()->add_action('clients_init', 'institutions_clients_area_menu_items');
hooks()->add_filter('get_contact_permissions', 'institutions_contact_permission',10,1);

hooks()->add_action('staff_member_deleted', 'institutions_staff_member_deleted');

hooks()->add_filter('migration_tables_to_replace_old_links', 'institutions_migration_tables_to_replace_old_links');
hooks()->add_filter('global_search_result_query', 'institutions_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'institutions_global_search_result_output', 10, 2);
hooks()->add_filter('get_dashboard_widgets', 'institutions_add_dashboard_widget');
hooks()->add_filter('module_institutions_action_links', 'module_institutions_action_links');


function institutions_add_dashboard_widget($widgets)
{
    /*
    $widgets[] = [
        'path'      => 'institutions/widgets/institution_this_week',
        'container' => 'left-8',
    ];
    $widgets[] = [
        'path'      => 'institutions/widgets/program_not_scheduled',
        'container' => 'left-8',
    ];
    */

    return $widgets;
}


function institutions_staff_member_deleted($data)
{
    $CI = &get_instance();
    $CI->db->where('staff_id', $data['id']);
    $CI->db->update(db_prefix() . 'institutions', [
            'staff_id' => $data['transfer_data_to'],
        ]);
}

function institutions_global_search_result_output($output, $data)
{
    if ($data['type'] == 'institutions') {
        $output = '<a href="' . admin_url('institutions/institution/' . $data['result']['id']) . '">' . format_institution_number($data['result']['id']) . '</a>';
    }

    return $output;
}

function institutions_global_search_result_query($result, $q, $limit)
{
    $CI = &get_instance();
    if (has_permission('institutions', '', 'view')) {

        // institutions
        $CI->db->select()
           ->from(db_prefix() . 'institutions')
           ->like(db_prefix() . 'institutions.formatted_number', $q)->limit($limit);
        
        $result[] = [
                'result'         => $CI->db->get()->result_array(),
                'type'           => 'institutions',
                'search_heading' => _l('institutions'),
            ];
        
        if(isset($result[0]['result'][0]['id'])){
            return $result;
        }

        // institutions
        $CI->db->select()->from(db_prefix() . 'institutions')->like(db_prefix() . 'clients.company', $q)->or_like(db_prefix() . 'institutions.formatted_number', $q)->limit($limit);
        $CI->db->join(db_prefix() . 'clients',db_prefix() . 'institutions.clientid='.db_prefix() .'clients.userid', 'left');
        $CI->db->order_by(db_prefix() . 'clients.company', 'ASC');

        $result[] = [
                'result'         => $CI->db->get()->result_array(),
                'type'           => 'institutions',
                'search_heading' => _l('institutions'),
            ];
    }

    return $result;
}

function institutions_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
                'table' => db_prefix() . 'institutions',
                'field' => 'description',
            ];

    return $tables;
}

function institutions_contact_permission($permissions){
        $item = array(
            'id'         => 7,
            'name'       => _l('institutions'),
            'short_name' => 'institutions',
        );
        $permissions[] = $item;
      return $permissions;

}

function institutions_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'edit_own'   => _l('permission_edit_own'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('institutions', $capabilities, _l('institutions'));
}


/**
* Register activation module hook
*/
register_activation_hook(INSTITUTIONS_MODULE_NAME, 'institutions_module_activation_hook');

function institutions_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register deactivation module hook
*/
register_deactivation_hook(INSTITUTIONS_MODULE_NAME, 'institutions_module_deactivation_hook');

function institutions_module_deactivation_hook()
{

     log_activity( 'Hello, world! . institutions_module_deactivation_hook ' );
}

//hooks()->add_action('deactivate_' . $module . '_module', $function);

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(INSTITUTIONS_MODULE_NAME, [INSTITUTIONS_MODULE_NAME]);

/**
 * Init institutions module menu items in setup in admin_init hook
 * @return null
 */
function institutions_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
            'name'       => _l('institution'),
            'url'        => 'institutions',
            'permission' => 'institutions',
            'position'   => 57,
            ]);

    if (has_permission('institutions', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('institutions', [
                'slug'     => 'institutions-tracking',
                'name'     => _l('institutions'),
                'icon'     => 'fa-solid fa-building-columns',
                'href'     => admin_url('institutions'),
                'position' => 12,
        ]);
    }
}

function module_institutions_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=institutions') . '">' . _l('settings') . '</a>';

    return $actions;
}

function institutions_clients_area_menu_items()
{
    // Show menu item only if client is logged in
    if (is_client_logged_in() && has_contact_permission('institutions')) {
        add_theme_menu_item('institutions', [
                    'name'     => _l('institutions'),
                    'href'     => site_url('institutions/list'),
                    'position' => 15,
                    'icon'     => 'fa-solid fa-building-columns',
        ]);
    }
}

/**
 * [perfex_dark_theme_settings_tab net menu item in setup->settings]
 * @return void
 */
function institutions_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('institutions', [
        'name'     => _l('settings_group_institutions'),
        //'view'     => module_views_path(INSTITUTIONS_MODULE_NAME, 'admin/settings/includes/institutions'),
        'view'     => 'institutions/institutions_settings',
        'position' => 51,
        'icon'     => 'fa-solid fa-building-columns',
    ]);
}

$CI = &get_instance();
$CI->load->helper(INSTITUTIONS_MODULE_NAME . '/institutions');
if(($CI->uri->segment(1)=='admin' && $CI->uri->segment(2)=='institutions') || $CI->uri->segment(1)=='institutions'){
    $CI->app_css->add(INSTITUTIONS_MODULE_NAME.'-css', base_url('modules/'.INSTITUTIONS_MODULE_NAME.'/assets/css/'.INSTITUTIONS_MODULE_NAME.'.css'));
    $CI->app_scripts->add(INSTITUTIONS_MODULE_NAME.'-js', base_url('modules/'.INSTITUTIONS_MODULE_NAME.'/assets/js/'.INSTITUTIONS_MODULE_NAME.'.js'));
}

if(($CI->uri->segment(1)=='admin' && $CI->uri->segment(2)=='staff') && $CI->uri->segment(3)=='edit_provile'){
    $CI->app_css->add(INSTITUTIONS_MODULE_NAME.'-css', base_url('modules/'.INSTITUTIONS_MODULE_NAME.'/assets/css/'.INSTITUTIONS_MODULE_NAME.'.css'));
}

