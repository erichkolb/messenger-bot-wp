<?php
/**
 * Settings Page for Giga Messenger Bots
 *
 * @author Gary <gary@binaty.org>
 */

namespace GigaAI;

use GigaAI\Http\Request;
use GigaAI\Http\ThreadSettings;

class Settings
{
    private $tabs = [];
    
    private $current_tab = 'basics';
    
    private $settings = [];
    
    /**
     * Constructor only to define hooks
     *
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        
        add_action('admin_init', [$this, 'admin_init']);
        
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue'], 99);
        
        $this->tabs = [
            'basics'              => __('Basics', 'giga-messenger-bots'),
            'thread-settings'     => __('Thread Settings', 'giga-messenger-bots'),
            'domain-whitelisting' => __('Domain Whitelisting', 'giga-messenger-bots'),
            'account-linking'     => __('Account Linking', 'giga-messenger-bots'),
            'auto-stop'           => __('Auto Stop', 'giga-messenger-bots'),
        ];
        
        $this->current_tab = (isset($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs)) ? trim($_GET['tab']) : 'basics';
        
        $this->settings = giga_setting();
    }
    
    /**
     * Create admin menu under Settings
     *
     * @return void
     */
    public function admin_menu()
    {
        add_menu_page(
            __('Giga AI', 'giga-messenger-bots'),
            __('Giga AI', 'giga-messenger-bots'),
            'manage_options',
            'giga',
            [$this, 'settings'],
            GIGA_URL . 'assets/img/giga-ai.png'
        );
        
        add_submenu_page('giga',
            __('Settings', 'giga-messenger-bots'),
            __('Settings', 'giga-messenger-bots'),
            'manage_options', 'giga',
            [$this, 'settings']
        );
    }
    
    /**
     * Enqueue styles and script in admin.
     *
     * @return void
     */
    public function admin_enqueue()
    {
        if (is_array($this->settings['persistent_menu'])) {
            wp_localize_script('giga-messenger-bots', 'persistent_menu', $this->settings['persistent_menu']);
        }
    }
    
    /**
     * All plugin settings saved in this method
     */
    public function admin_init()
    {
        register_setting('giga_messenger_bots', 'giga_messenger_bot_settings');
        
        if (isset($_POST['_page_now']) && $_POST['_page_now'] == 'giga') {
            
            if ( ! check_admin_referer('giga_settings_nonce', 'giga_settings_nonce')) {
                return;
            }
            
            $this->save_plugin_settings();
            
            Session::flash([
                'status'  => 'success',
                'message' => __('Settings saved!', 'giga-messenger-bots')
            ]);
            
            giga_refresh();
        }
    }
    
    /**
     * Save plugin settings
     *
     * @return void
     */
    private function save_plugin_settings()
    {
        $old_setting = $this->settings;
        
        $new_setting = [];
        
        $defaults = giga_default_settings();
        
        // Sanitize data before update
        foreach ($defaults as $key => $value) {
            
            $new_setting[$key] = $value;
            
            if (isset($old_setting[$key])) {
                $new_setting[$key] = $old_setting[$key];
            }
            
            if (isset($_POST[$key])) {
                
                if (is_string($_POST[$key])) {
                    $new_setting[$key] = sanitize_text_field($_POST[$key]);
                }
                
                // We don't use sanitize_text_field for these special fields. Should do our logic.
                if ($key === 'whitelisted_domains' || $key === 'auto_stop') {
                    $new_setting[$key] = $_POST[$key];
                }
            }
        }
        
        if ( ! isset($_POST['check_auto_stop'])) {
            $new_setting['auto_stop'] = false;
        } else {
            if ( ! isset($new_setting['auto_stop'])) {
                $new_setting['auto_stop'] = [];
            }
            
            if ( ! isset($new_setting['auto_stop']['stop_when']) || empty($new_setting['auto_stop']['stop_when'])) {
                $new_setting['auto_stop']['stop_when'] = '*';
            }
            
            if ( ! isset($new_setting['auto_stop']['restart_when']) || empty($new_setting['auto_stop']['restart_when'])) {
                $new_setting['auto_stop']['restart_when'] = ':)';
            }
        }
        
        // Persistent Menu rendered via AngularJS as object so we have to decode it
        if ( ! empty($new_setting['persistent_menu']) && ! is_array($new_setting['persistent_menu'])) {
            $new_setting['persistent_menu'] = json_decode(stripslashes($new_setting['persistent_menu']), true);
        }
        
        // If Page Access Token changed. Send subscribe request to Facebook. Also set the status.
        if ( ! empty($new_setting['page_access_token'])) {
            // Modify token because it only change when page loaded.
            Request::$token = $new_setting['page_access_token'];
            
            // Send subscribe request to Facebook each time Settings changed
            $status = Request::sendSubscribeRequest();
            
            if (isset($status->success) && $status->success == 1) {
                $new_setting['connection_status'] = 'success';
            } else {
                $new_setting['connection_status'] = 'error';
            }
        }
        
        // Allows users filter the value before the update
        $new_setting = apply_filters('giga_settings_before_update', $new_setting);
        
        update_option('giga_messenger_bots', $new_setting);
        
        // Convert whitelisted domains to array before passing to GigaAI
        
        if ( ! empty($new_setting['whitelisted_domains']) && ! is_array($new_setting['whitelisted_domains'])) {
            // Keep the old text value to check later
            $new_setting_whitelisted_domains = $new_setting['whitelisted_domains'];
            $new_setting['whitelisted_domains'] = preg_split('/\r\n|[\r\n]/', $new_setting['whitelisted_domains']);
        }
        
        if (empty($new_setting['whitelisted_domains']) || ! is_array($new_setting['whitelisted_domains'])) {
            $new_setting['whitelisted_domains'] = [];
        }
        
        array_unshift($new_setting['whitelisted_domains'], get_site_url());
        
        // Set bot to the new settings
        \GigaAI\Core\Config::set($new_setting);
        
        // Switch back to text instead of array. This helps check with old value to update or not later.
        $new_setting['whitelisted_domains'] = $new_setting_whitelisted_domains;
        
        $requests = [
            'get_started_button_payload' => 'updateGetStartedButton',
            'greeting_text'              => 'updateGreetingText',
            'persistent_menu'            => 'updatePersistentMenu',
            'whitelisted_domains'        => 'domainWhitelisting',
            'account_linking_url'        => 'updateAccountLinkingUrl',
        ];
        
        foreach ($requests as $id => $method) {
             call_user_func(['\\GigaAI\\Http\\ThreadSettings', $method]);
        }
        
        // Update Payloads Array
        giga_update_payload_cache($new_setting['persistent_menu']);
        
        do_action('giga_settings_saved');
    }
    
    /**
     * Safe print the setting output
     */
    private function sanitized_field_value($field)
    {
        $field_value = isset($this->settings[$field]) ? $this->settings[$field] : '';
        
        echo esc_attr($field_value);
    }
    
    public function settings()
    {
        ?>
        <div class="wrap giga" id="bot-settings" ng-app="Bot">
            <h2><?php _e('Settings', 'giga-messenger-bots'); ?></h2>

            <h2 class="nav-tab-wrapper wp-clearfix">
                <?php foreach ($this->tabs as $url => $title) : ?>
                    <a href="<?php echo add_query_arg('tab', $url); ?>"
                       class="nav-tab <?php echo ($this->current_tab === $url) ? 'nav-tab-active' : '' ?>">
                        <?php echo $title ?>
                    </a>
                <?php endforeach; ?>
            </h2>
            
            <?php giga_load_component('admin-message'); ?>

            <form action="options.php" method="post" id="poststuff" ng-controller="PersistentMenuController"
                  ng-init="init();">
                <?php settings_fields('giga_messenger_bot'); ?>

                <div class="settings-tab-content">
                    <table class="form-table">
                        <tbody>
                        <?php require_once GIGA_INC_DIR . 'settings/sections/' . $this->current_tab . '.php'; ?>
                        </tbody>
                    </table>

                    <input type="hidden" name="_page_now" value="giga">
                    <?php
                    wp_nonce_field('giga_settings_nonce', 'giga_settings_nonce');
                    submit_button();
                    ?>
                </div>
            </form>
        </div>
        <?php
    }
}

new Settings;