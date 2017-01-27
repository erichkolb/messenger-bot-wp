<?php

// Prevent loading this file directly
if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Default Plugin Settings
 *
 * @return array
 */
function giga_default_settings()
{
    $persistent_menu = giga_get_default_persistent_menu();
    
    $login_url = wp_login_url();
    
    $defaults = [
        // Allows user config page access token
        'page_id'                    => '',
        'page_access_token'          => '',
        'app_id'                     => '',
        'get_started_button_payload' => 'GIGA_GET_STARTED_PAYLOAD',
        'greeting_text'              => __('Hi everyone, welcome to my page', 'giga-messenger-bots'),
        'persistent_menu'            => $persistent_menu,
        'connection_status'          => '',
        'whitelisted_domains'        => '',
        'account_linking_url'        => $login_url,
        'auto_stop'                  => false,
    ];
    
    return apply_filters('giga_default_settings', $defaults);
}

/**
 * Get plugin setting
 *
 * @param  Mixed $field Field name, if empty, return whole settings array
 *
 * @return Mixed
 */
function giga_setting($field = null)
{
    $settings = get_option('giga_messenger_bots');
    
    $defaults = giga_default_settings();
    
    if (empty($settings) || ! is_array($settings)) {
        $settings = $defaults;
    }
    
    if (is_null($field)) {
        return $settings;
    }
    
    if (isset($settings[$field])) {
        return $settings[$field];
    }
    
    if (isset($defaults[$field])) {
        return $defaults[$field];
    }
    
    return null;
}

/**
 * Get all tags to display in builder
 *
 * @return array
 */
function giga_get_tags()
{
    $tag_rows = \GigaAI\Storage\Eloquent\Node::where('tags', '!=', '')
        ->get(['tags'])->toArray();
    $tags = [];
    
    foreach ($tag_rows as $row) {
        $tags = array_merge($tags, explode(',', $row['tags']));
    }
    
    $tags = array_unique($tags);
    
    sort($tags);
    
    return $tags;
}

/**
 * Get all intended actions to display in Builder
 *
 * @return array
 */
function giga_get_intended_actions()
{
    
    $intended_rows = \GigaAI\Storage\Eloquent\Node::where('wait', '!=', '')
        ->get(['wait'])->toArray();
    $actions = [];
    
    foreach ($intended_rows as $row) {
        if ( ! is_numeric($row['wait'])) {
            $actions[] = $row['wait'];
        }
    }
    
    $actions = array_unique($actions);
    
    sort($actions);
    
    return $actions;
}

/**
 * Get payloads cached in wp_options table
 *
 * @return mixed|void
 */
function giga_get_payload_buttons()
{
    $buttons = get_option('giga_payload_buttons', []);
    
    return $buttons;
}

/**
 * Get all payload from answers
 *
 * @param $answers
 *
 * @return array
 */
function giga_get_payloads_from_answers($answers)
{
    $payloads = [];
    
    foreach ((array)$answers as $answer) {
        if (is_array($answer)) {
            
            if (array_key_exists('payload', $answer)) {
                $payloads[$answer['payload']] = $answer['payload'];
            }
            
            $payloads = array_merge($payloads, giga_get_payloads_from_answers($answer));
        }
    }
    
    $payloads = array_unique(array_values($payloads));
    
    sort($payloads);
    
    return $payloads;
}

/**
 * Update payload cache to display in Builder
 *
 * @param array $answers The answers type which contains `payload`
 *
 * @return array
 */
function giga_update_payload_cache($answers)
{
    $exists = giga_get_payload_buttons();
    
    $new = giga_get_payloads_from_answers($answers);
    
    $update = array_unique(array_merge($exists, $new));
    
    update_option('giga_payload_buttons', $update);
    
    return $update;
}

/**
 * Default persistent menu
 *
 * @return array
 */
function giga_get_default_persistent_menu()
{
    return [
        [
            'type'    => 'postback',
            'title'   => __('Help', 'giga-messenger-bots'),
            'payload' => 'HELP',
        ],
        [
            'type'    => 'postback',
            'title'   => __('Subscribe', 'giga-messenger-bots'),
            'payload' => 'SUBSCRIBE',
        ],
        [
            'type'  => 'web_url',
            'title' => __('Giga AI', 'giga-messenger-bots'),
            'url'   => 'https://giga.ai',
        ],
    ];
}

function giga_bot_instance()
{
    static $bot = null;
    
    if (is_null($bot)) {
        $bot = new GigaAI\MessengerBot;
    }
    
    return $bot;
}

function giga_load_component($component)
{
    $component = GIGA_INC_DIR . 'components/' . $component . '.php';
    
    if (file_exists($component)) {
        require_once $component;
    }
}

function giga_is_bot_builder()
{
    return isset($_GET['page']) && $_GET['page'] === 'bot-builder';
}

function giga_get_channels()
{
    $bot = giga_bot_instance();
    
    $channels = $bot->subscription->getAllChannels();
    
    return is_array($channels) ? $channels : ['1'];
}

/**
 * Load default subscription message
 *
 * @return \GigaAI\Storage\Eloquent\Message
 */
function giga_default_message()
{
    return new GigaAI\Storage\Eloquent\Message([
        'id'          => '',
        'description' => '',
        'content'     => [
            [
                'text' => '',
            ],
        ],
        'routines'    => '',
        'send_limit'  => 0
    ]);
}

/**
 * Refresh current page with status
 *
 * @param string $status
 * @param array $add_query_arg Query args to be added to current page by add_query_arg() function
 * @param array $remove_query_arg Query args to be removed to current page by remove_query_arg() function
 */
function giga_refresh($add_query_arg = null, $remove_query_arg = null)
{
    if ( ! wp_get_referer()) {
        return;
    }
    
    $referer = wp_get_referer();
    
    if ( ! is_null($add_query_arg)) {
        $referer = add_query_arg($add_query_arg, $referer);
    }
    
    if ( ! is_null($remove_query_arg)) {
        $referer = remove_query_arg($remove_query_arg, $referer);
    }
    
    wp_safe_redirect($referer);
    exit;
}

/**
 * Allows user use simple syntax 'tax' => 'value' in WP_Query
 */
add_action('parse_tax_query', function ($query) {
    $tax = isset($query->query_vars['tax']) ? $query->query_vars['tax'] : null;
    
    if (is_null($tax)) {
        return;
    }
    
    $field = is_numeric($tax) ? 'term_taxonomy_id' : 'slug';
    $term = get_term_by($field, $tax);
    
    $tax_query = [
        [
            'taxonomy' => $term->taxonomy,
            'field'    => $field,
            'terms'    => $tax,
        ],
    ];
    
    $query->tax_query = array_merge((array)$query->tax_query, $tax_query);
    $query->tax_query = new WP_Tax_Query($tax_query);
});