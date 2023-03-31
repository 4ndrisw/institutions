<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-institutions" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th><?php echo _l('institution_number'); ?> #</th>
            <th><?php echo _l('institution_list_program'); ?></th>
            <th><?php echo _l('institution_list_date'); ?></th>
            <th><?php echo _l('institution_list_state'); ?></th>

        </tr>
    </thead>
    <tbody>
        <?php foreach($institutions as $institution){ ?>
            <tr>
                <td><?php echo '<a href="' . site_url("institutions/show/" . $institution["id"] . '/' . $institution["hash"]) . '">' . format_institution_number($institution["id"]) . '</a>'; ?></td>
                <td><?php echo $institution['name']; ?></td>
                <td><?php echo _d($institution['date']); ?></td>
                <td><?php echo format_institution_state($institution['state']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
