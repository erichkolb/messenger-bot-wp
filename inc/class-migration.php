<?php

namespace GigaAI;

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Setup and migrate data each time db has update
 */
class Migration
{
    // Current database version
    public static $db_version = 6;
    
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'check_migrate']);
    }
    
    public function check_migrate()
    {
        $installed_version = get_option('giga_db_version');
        
        if ($installed_version != self::$db_version) {
            self::up();
        }
    }
    
    /**
     * Create or change table structure when database has updated
     *
     * @return void
     */
    public static function up()
    {
        
        global $wpdb;
        
        $wpdb_collate = $wpdb->collate;
        
        // We'll create 5 tables
        $bot_instances = $wpdb->prefix . "bot_instances";
        $bot_leads = $wpdb->prefix . "bot_leads";
        $bot_leads_meta = $wpdb->prefix . "bot_leads_meta";
        $bot_nodes = $wpdb->prefix . "bot_nodes";
        $bot_messages = $wpdb->prefix . "bot_messages";
        
        $create_bot_instances_sql = "CREATE TABLE {$bot_instances} (
            id varchar(150) NOT NULL,
            name varchar(255) NOT NULL,
            meta text,
            status varchar(50) NOT NULL,
            UNIQUE KEY ix_instance_id (id)
        )
        COLLATE {$wpdb_collate}";
        
        $create_bot_leads_table_sql = "CREATE TABLE {$bot_leads} (
            id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            instance_id int(10) UNSIGNED DEFAULT NULL,
            creator_id int(10) UNSIGNED DEFAULT NULL,
            source varchar(50) DEFAULT 'facebook',
            user_id varchar(100) NOT NULL DEFAULT '',
            first_name varchar(255) DEFAULT NULL,
            last_name varchar(255) DEFAULT NULL,
            profile_pic varchar(500) DEFAULT NULL,
            locale varchar(50) DEFAULT NULL,
            timezone varchar(10) DEFAULT NULL,
            gender varchar(255) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            phone varchar(255) DEFAULT NULL,
            country varchar(255) DEFAULT NULL,
            location varchar(255) DEFAULT NULL,
            _wait varchar(255) DEFAULT NULL,
            _quick_save varchar(255) DEFAULT NULL,
            linked_account varchar(255) DEFAULT NULL,
            subscribe varchar(255) DEFAULT NULL,
            is_payment_enabled varchar(30) DEFAULT NULL,
            auto_stop varchar(10) DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp NULL DEFAULT NULL,
            deleted_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY source_user_id (source, user_id)
        )
        COLLATE {$wpdb_collate}";
        
        $create_bot_leads_meta_table_sql = "CREATE TABLE {$bot_leads_meta} (
            id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id varchar(30) NOT NULL DEFAULT '',
            meta_key varchar(100) DEFAULT NULL,
            meta_value longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id_meta_key (user_id, meta_key)
        )
        COLLATE {$wpdb_collate}";
        
        $create_bot_messages_table_sql = "CREATE TABLE {$bot_messages} (
          id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          instance_id int(10) UNSIGNED DEFAULT NULL,
          creator_id int(10) UNSIGNED DEFAULT NULL,
          to_lead text,
          to_channel text,
          content text NOT NULL,
          description varchar(255) DEFAULT NULL,
          wait varchar(99) DEFAULT NULL,
          status varchar(50) DEFAULT NULL,
          notification_type varchar(20) DEFAULT 'REGULAR',
          send_limit varchar(10) DEFAULT '1',
          sent_count int(11) UNSIGNED NOT NULL DEFAULT '0',
          routines varchar(255) DEFAULT NULL,
          unique_id varchar(150) DEFAULT NULL,
          created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at timestamp NULL DEFAULT NULL,
          deleted_at timestamp NULL DEFAULT NULL,
          start_at timestamp NULL DEFAULT NULL,
          end_at timestamp NULL DEFAULT NULL,
          sent_at timestamp NULL DEFAULT NULL,
          PRIMARY KEY  (id),
          UNIQUE KEY unique_id (unique_id)
        )
        COLLATE {$wpdb_collate}";
        
        $create_bot_nodes_table_sql = "CREATE TABLE {$bot_nodes} (
          `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `instance_id` int(10) UNSIGNED DEFAULT NULL,
          `creator_id` int(10) UNSIGNED DEFAULT NULL,
          `pattern` text,
          `answers` text NOT NULL,
          `wait` varchar(99) DEFAULT NULL,
          `sources` varchar(255) DEFAULT NULL,
          `type` varchar(50) DEFAULT NULL,
          `notification_type` varchar(20) DEFAULT 'REGULAR',
          `status` varchar(20) DEFAULT NULL,
          `tags` varchar(255) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL,
          `deleted_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY  (id)
        )
        COLLATE {$wpdb_collate}";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($create_bot_instances_sql);
        dbDelta($create_bot_leads_table_sql);
        dbDelta($create_bot_leads_meta_table_sql);
        dbDelta($create_bot_messages_table_sql);
        dbDelta($create_bot_nodes_table_sql);
        
        // Seed the basic data.
        $bot = giga_bot_instance();
        
        $bot->answer('hi', 'Hi [first_name]!');
        $bot->answer('payload:GIGA_GET_STARTED_PAYLOAD', 'Hi [first_name]!');
        
        update_option('giga_db_version', self::$db_version);
    }
    
    /**
     * Remove plugin settings on activate.
     *
     * @return void
     */
    public static function down()
    {
        // Do nothing for now
    }
}

new Migration;