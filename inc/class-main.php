<?php

namespace GigaAI;

/**
 * Main class for Giga Messenger Bots
 *
 * @author Binaty <hello@binaty.org>
 */
class Main
{
    /**
     * Constructor method. Only to define hooks
     */
    public function __construct()
    {
        // Load admin scripts and styles
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue']);
        
        // Supports I18N
        add_action('plugins_loaded', [$this, 'i18n']);
    }
    
    /**
     * Enqueue styles and script in admin.
     *
     * @return void
     */
    public function admin_enqueue()
    {
        $page = isset($_GET['page']) ? trim($_GET['page']) : '';
        
        if ( ! in_array($page, ['giga', 'bot-builder', 'crm', 'notifications'])) {
            return;
        }
        
        wp_enqueue_style('giga-messenger-bots', GIGA_CSS_URL . 'giga-messenger-bots.css', [], '2.2.1');
        
        wp_register_script('angularjs', GIGA_JS_URL . 'angular.min.js', [], '1.5.5', true);
        wp_register_script('jquery-masked-input', GIGA_JS_URL . 'jquery.maskedinput.js', [], '1.4.0', true);
        wp_register_script('giga-messenger-bots', GIGA_JS_URL . 'giga-messenger-bots.js', [
            'angularjs',
            'jquery',
            'jquery-masked-input'
        ], '2.2.1', true);
        
        wp_enqueue_script('giga-messenger-bots');
    }
    
    /**
     * I18N
     *
     * @return void
     */
    public function i18n()
    {
        load_plugin_textdomain('giga-messenger-bots', false, basename(GIGA_DIR) . '/lang/');
    }
}