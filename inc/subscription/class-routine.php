<?php

namespace GigaAI;

class Routine
{
    public function __construct()
    {
        add_action('giga_notification_routines', function ($id) {
            try {
                static $done = false;
                
                if ( ! $done) {
                    $bot = giga_bot_instance();
                    $bot->subscription->find($id)->send();
                    
                    $done = true;
                }
            } catch (\Exception $e) {
                // Handle reached limit or datetime limit
                if (current_user_can('administrator')) {
                    Session::flash([
                        'status'  => 'error',
                        'message' => $e->getMessage(),
                    ]);
                    
                    wp_safe_redirect(admin_url('admin.php?page=notifications&id=' . $id));
                    exit;
                }
            }
        });
        
        // Add Weekly and Monthly schedule
        add_filter('cron_schedules', function ($schedules) {
            $schedules['weekly'] = [
                'interval' => 604800,
                'display'  => __('Once Weekly', 'giga-messenger-bots'),
            ];
            
            $schedules['monthly'] = [
                'interval' => 2635200,
                'display'  => __('Once Monthly', 'giga-messenger-bots'),
            ];
            
            return $schedules;
        });
    }
    
    public static function create($message)
    {
        // Create cron job after message created.
        if ( ! wp_next_scheduled('giga_notification_routines') && ! empty($message->routines)) {
            wp_schedule_event(strtotime($message->start_at), $message->routines, 'giga_notification_routines', [
                'id' => $message->id,
            ]);
        }
    }
    
    public static function update($message)
    {
        self::delete($message);
        
        self::create($message);
    }
    
    public static function delete($message)
    {
        $args = [
            'id' => $message->id,
        ];
        
        $timestamp = wp_next_scheduled('giga_notification_routines', $args);
        wp_unschedule_event($timestamp, 'giga_notification_routines', $args);
    }
}

new Routine;