<?php

if ( ! defined('ABSPATH')) {
    exit;
}

// Load the Messenger Bots library
require_once GIGA_DIR . 'vendor/autoload.php';

// Load the helpers
require_once GIGA_INC_DIR . 'helpers.php';
require_once GIGA_INC_DIR . 'Command.php';

// Set the settings for Giga
$settings = giga_setting();
GigaAI\Core\Config::set($settings);

/** Tell Giga to Parse the Shortcode */
$shortcode_parser = [
    'type'     => 'shortcode',
    'callback' => function ($content) {
        $content = do_shortcode($content);
        
        $output = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $output;
        }
        
        return $content;
    },
];

$command_parser = [
    'type' => 'command',
    'callback' => function ($content) {
        if (isset($content['command'])) {
            $command = $content['command'];
            $args = $content['args'];
            GigaAI\Command::run($command, $args);
        }
    }
];

GigaAI\Core\DynamicParser::support($shortcode_parser);
GigaAI\Core\DynamicParser::support($command_parser);