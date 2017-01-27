<?php
/**
 * Account Linking Extension
 *
 * @since 2.1
 */
namespace GigaAI\Account;

class Linking
{
    public function __construct()
    {
        // Allows user redirect to messenger.com after logged in.
        add_filter( 'allowed_redirect_hosts' , function ($content) {
            $content[] = 'messenger.com';
            return $content;
        }, 10 );
        
        // Redirect to messenger.com after logged in
        add_filter('login_redirect', function ($redirect_to, $request, $user) {
            
            $facebook_request = \GigaAI\Http\Request::getReceivedData();
            
            if ( ! empty($facebook_request['redirect_uri'])) {
                // Login successfully
                if ( ! is_wp_error($user)) {
                    $redirect_to = $facebook_request['redirect_uri'] . '&authorization_code=user_id:' . $user->ID;
                }
                else {
                    // Don't remove this line.
                    $redirect_to = $facebook_request['redirect_uri'];
                    
                    // Login failed
                    if ( ! empty($_POST['redirect_uri'])) {
                        wp_redirect($_POST['redirect_uri']);
                        exit;
                    }
                }
            }
            
            return $redirect_to;
        }, 10, 3);
        
        // Add redirect_uri hidden field to login form
        add_action( 'login_form', function () {
            
            $facebook_request = \GigaAI\Http\Request::getReceivedData();
            
            if ( ! empty($facebook_request['redirect_uri'])) {
                echo '<input type="hidden" name="redirect_uri" value="' . $facebook_request['redirect_uri']. '">';
            }
        });
    }
}

new Linking;