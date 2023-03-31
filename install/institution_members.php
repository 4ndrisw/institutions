<?php defined('BASEPATH') or exit('No direct script access allowed');


if (!$CI->db->table_exists(db_prefix() . 'institution_members')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "institution_members` (
      `id` int(11) NOT NULL,
      `institution_id` int(11) NOT NULL DEFAULT 0,
      `staff_id` int(11) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'institution_members`
      ADD PRIMARY KEY (`id`),
      ADD KEY `staff_id` (`staff_id`),
      ADD KEY `institution_id` (`institution_id`) USING BTREE;');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'institution_members`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}