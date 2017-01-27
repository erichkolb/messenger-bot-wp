<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>
<tr>
    <th scope="row">
        <label for="page_access_token"><?php _e('Page Access Token', 'giga-messenger-bots'); ?> *</label>
    </th>
    <td>
        <input id="page_access_token" name="page_access_token"
               value="<?php $this->sanitized_field_value('page_access_token'); ?>"
               type="text" class="form-control"
               placeholder="Enter your page access token">
    </td>
</tr>

<tr>
    <th scope="row">
        <label for="page_id"><?php _e('Page ID', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <input id="page_id" name="page_id"
               value="<?php $this->sanitized_field_value('page_id'); ?>"
               type="text" class="form-control">
    </td>
</tr>

<tr>
    <th scope="row">
        <label for="app_id"><?php _e('App ID', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <input id="app_id" name="app_id"
               value="<?php $this->sanitized_field_value('app_id'); ?>"
               type="text" class="form-control">
    </td>
</tr>

<tr>
    <th scope="row">
        <label for="verify_token"><?php _e('Verify Token', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <input id="verify_token" type="text" disabled class="form-control" value="GigaAI">
    </td>
</tr>

<tr>
    <th scope="row">
        <?php _e('Webhook', 'giga-messenger-bots'); ?>
    </th>
    <td>
        <a href="<?php bloginfo('url'); ?>/wp-json/giga-ai/webhook">
            <?php bloginfo('url'); ?>/wp-json/giga-ai/webhook/
        </a>
    </td>
</tr>

<tr>
    <th scope="row">
        <?php _e('Connection status', 'giga-messenger-bots'); ?>
    </th>
    <td class="connection-status">
        <?php
        // Print connection status only when it's success, error, or not connected
        $connection_status = $this->settings['connection_status'];
        if (empty($connection_status)) {
            $connection_status = 'not-connected';
        }
        
        $connection_statuses = [
            'success'       => __('Success', 'giga-messenger-bots'),
            'error'         => __('Error', 'giga-messenger-bots'),
            'not-connected' => __('Not Connected', 'giga-messenger-bots'),
        ];
        
        // Safe to print
        if (in_array($connection_status, array_keys($connection_statuses))) {
            ?>
            <div class="light light-<?php echo $connection_status ?>" title="<?php echo $connection_status; ?>"></div>
            <span class="description"><?php echo $connection_statuses[$connection_status]; ?></span>
        <?php
        }
        ?>
    </td>
</tr>