<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>
<tr>
    <th scope="row">
        <label for="whitelisted_domains"><?php _e('Domain Whitelisting', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
         <textarea name="whitelisted_domains" id="whitelisted_domains"
                   class="form-control"
                   rows="10"><?php $this->sanitized_field_value('whitelisted_domains'); ?></textarea>

        <p class="description"><?php _e('Domain whitelisting is required for Webview and Payment. Your website is whitelisted by default. In case you need to whitelist other domains, enter domains here, one per line. Maximum: 10 domains.', 'giga-messenger-bots'); ?></p>
    </td>
</tr>