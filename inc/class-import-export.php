<?php

namespace GigaAI;

class Import_Export
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'settings_page']);
    }
    
    /**
     * Register Settings Page with menu under Giga AI
     */
    public function settings_page()
    {
        add_submenu_page('giga',
            __('Import/Export', 'giga-messenger-bots'),
            __('Import/Export', 'giga-messenger-bots'),
            'manage_options',
            'import-export',
            [$this, 'render']
        );
    }
}