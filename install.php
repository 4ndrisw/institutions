<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once('install/institutions.php');
require_once('install/institution_activity.php');
require_once('install/institution_items.php');
require_once('install/institution_members.php');



$CI->db->query("
INSERT INTO `tblemailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
('institution', 'institution-send-to-client', 'english', 'Send institution to Customer', 'institution # {institution_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached institution <strong># {institution_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>institution state:</strong> {institution_state}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-already-send', 'english', 'institution Already Sent to Customer', 'institution # {institution_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your institution request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-declined-to-staff', 'english', 'institution Declined (Sent to Staff)', 'Customer Declined institution', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined institution with number <strong># {institution_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-accepted-to-staff', 'english', 'institution Accepted (Sent to Staff)', 'Customer Accepted institution', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted institution with number <strong># {institution_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-thank-you-to-customer', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank for you accepting institution', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank for for accepting the institution.</span><br /> <br /><span style=\"font-size: 12pt;\">We look forward to doing business with you.</span><br /> <br /><span style=\"font-size: 12pt;\">We will contact you as soon as possible.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-expiry-reminder', 'english', 'institution Expiration Reminder', 'institution Expiration Reminder', '<p><span style=\"font-size: 12pt;\">Hello {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">The institution with <strong># {institution_number}</strong> will expire on <strong>{institution_expirydate}</strong></span><br /><br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span></p>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-send-to-client', 'english', 'Send institution to Customer', 'institution # {institution_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached institution <strong># {institution_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>institution state:</strong> {institution_state}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-already-send', 'english', 'institution Already Sent to Customer', 'institution # {institution_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your institution request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-declined-to-staff', 'english', 'institution Declined (Sent to Staff)', 'Customer Declined institution', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined institution with number <strong># {institution_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-accepted-to-staff', 'english', 'institution Accepted (Sent to Staff)', 'Customer Accepted institution', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted institution with number <strong># {institution_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'staff-added-as-program-member', 'english', 'Staff Added as Project Member', 'New program assigned to you', '<p>Hi <br /><br />New institution has been assigned to you.<br /><br />You can view the institution on the following link <a href=\"{institution_link}\">institution__number</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
('institution', 'institution-accepted-to-staff', 'english', 'institution Accepted (Sent to Staff)', 'Customer Accepted institution', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted institution with number <strong># {institution_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the institution on the following link: <a href=\"{institution_link}\">{institution_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0);
");
/*
 *
 */

// Add options for institutions
add_option('delete_only_on_last_institution', 1);
add_option('institution_prefix', 'SCH-');
add_option('next_institution_number', 1);
add_option('default_institution_assigned', 9);
add_option('institution_number_decrement_on_delete', 0);
add_option('institution_number_format', 4);
add_option('institution_year', date('Y'));
add_option('exclude_institution_from_client_area_with_draft_state', 1);
add_option('predefined_clientnote_institution', '- Staf diatas untuk melakukan riksa uji pada peralatan tersebut.
- Staf diatas untuk membuat dokumentasi riksa uji sesuai kebutuhan.');
add_option('predefined_terms_institution', '- Pelaksanaan riksa uji harus mengikuti prosedur yang ditetapkan perusahaan pemilik alat.
- Dilarang membuat dokumentasi tanpa seizin perusahaan pemilik alat.
- Dokumen ini diterbitkan dari sistem CRM, tidak memerlukan tanda tangan dari PT. Cipta Mas Jaya');
add_option('institution_due_after', 1);
add_option('allow_staff_view_institutions_assigned', 1);
add_option('show_assigned_on_institutions', 1);
add_option('require_client_logged_in_to_view_institution', 0);

add_option('show_program_on_institution', 1);
add_option('institutions_pipeline_limit', 1);
add_option('default_institutions_pipeline_sort', 1);
add_option('institution_accept_identity_confirmation', 1);
add_option('institution_qrcode_size', '160');
add_option('institution_send_telegram_message', 0);


/*

DROP TABLE `tblinstitutions`;
DROP TABLE `tblinstitution_activity`, `tblinstitution_items`, `tblinstitution_members`;
delete FROM `tbloptions` WHERE `name` LIKE '%institution%';
DELETE FROM `tblemailtemplates` WHERE `type` LIKE 'institution';



*/