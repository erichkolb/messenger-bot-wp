<?php

namespace GigaAI;

/**
 * Session Management Class
 *
 * @package GigaAI
 * @since 2.2
 */
class Session
{
    /**
     * Check and start the session if session isn't already started
     */
    public function __construct()
    {
        add_action('admin_init', function () {
            if (session_id() == '') {
                session_start();
            }
        });
    }
    
    /**
     * Get the session value by key
     *
     * @param mixed  $key
     * @param string $default
     *
     * @return string
     */
    public static function get($key, $default = '')
    {
        if (array_key_exists($key, $_SESSION)) {
            
            $value = $_SESSION[$key];
    
            if ( ! empty($_SESSION['flashes']) && array_key_exists($key, $_SESSION['flashes'])) {
                unset($_SESSION[$key]);
            }
            
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Check if the session has given key
     *
     * @param $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Set the session value
     *
     * @param        $key
     * @param string $value
     */
    public static function set($key, $value = '')
    {
        if ( ! is_array($key)) {
            $_SESSION[$key] = $value;
        } else {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
        }
    }
    
    /**
     * Remove session key
     *
     * @param $key
     */
    public static function forget($key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Session flash. Generate one time session value.
     *
     * @param        $key
     * @param string $value
     */
    public static function flash($key, $value = '')
    {
        if ( ! array_key_exists('flashes', $_SESSION)) {
            $_SESSION['flashes'] = [];
        }
        
        if ( ! is_array($key)) {
            $_SESSION['flashes'][$key] = $key;
            $_SESSION[$key] = $value;
        } else {
            foreach ($key as $k => $v) {
                $_SESSION['flashes'][$k] = $k;
                $_SESSION[$k] = $v;
            }
        }
    }
}

new Session;