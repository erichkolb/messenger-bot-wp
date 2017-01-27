<?php

namespace GigaAI;

class Command
{
    public static function run($command, $params)
    {
        @call_user_func_array(['\\GigaAI\\Command', $command], $params);
    }
    
    protected static function setChannel($channels, $type = 'add')
    {
        // Remove all non selected channels
        $channels = array_filter($channels);
        $channels = array_unique(array_keys($channels));
    
        $bot = giga_bot_instance();
        $lead_id = $bot->getLeadId();
    
        if ( ! empty($lead_id)) {
            $bot->subscription->setSubscriptionChannel($lead_id, $channels, $type);
        }
    }
    
    public static function addChannel($channels)
    {
       self::setChannel($channels);
    }
    
    public static function removeChannel($channels)
    {
        self::setChannel($channels, 'remove');
    }
}