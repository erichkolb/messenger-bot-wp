<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<tr>
    <th scope="row">
        <label for="account-linking-url"><?php _e('Account Linking URL', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <input type="text" name="account_linking_url" value="<?php $this->sanitized_field_value('account_linking_url'); ?>" id="account-linking-url" class="form-control">
    
        <p class="description">
            <?php _e("By default, Giga uses WP default login URL (/wp-login.php) so you don't have to do anything here.
            In case you want to use other login URL. Enter to text box above.", 'giga-messenger-bots'); ?>
        </p>
    </td>
</tr>