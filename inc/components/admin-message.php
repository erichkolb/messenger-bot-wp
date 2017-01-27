<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$status     = GigaAI\Session::get('status');
$message    = GigaAI\Session::get('message');

if ( ! empty($status) && ! empty($message)) :
?>
<div id="message" class="notice notice-<?php echo esc_attr($status); ?> is-dismissible">
    <p><?php echo esc_html($message); ?></p>
    <button type="button" class="notice-dismiss"><span
            class="screen-reader-text"><?php _e('Dismiss this notice.', 'giga-messenger-bots'); ?></span>
    </button>
</div>
<?php endif; ?>