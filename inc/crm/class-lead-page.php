<?php

namespace GigaAI\CRM;

use GigaAI\Storage\Eloquent\Lead;

class Lead_Page
{
    public static $current_tab = 'basics';
    
    public static function render()
    {
        $lead_id = trim($_GET['lead_id']);
        
        $tabs = [
            'basics'       => __('Basic Info', 'giga-messenger-bots'),
            'advanced'     => __('Advanced', 'giga-messenger-bots'),
            'subscription' => __('Subscription', 'giga-messenger-bots'),
        ];
        
        self::$current_tab = (isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs)) ? trim($_GET['tab']) : 'basics';
        
        $lead = Lead::where('user_id', $lead_id)->first();
        
        ?>
        <div class="wrap giga" id="giga-crm">
            <?php
            giga_load_component('admin-message');
            
            try {
                ?>
                <div id="profile-picture">
                    <img src="<?php echo esc_attr($lead->profile_pic); ?>" width="120" height="120"
                         alt="<?php _e('Profile Picture', 'giga-messenger-bots'); ?>">
                </div>

                <h1><?php echo $lead->getFullName(); ?>
                    
                    <?php if ( ! empty($lead['auto_stop'])) : ?>
                        <span class="light light-error"
                              title="<?php _e('Waiting Answers from Page Administrators', 'giga-messenger-bots') ?>"></span>
                    <?php else : ?>
                        <span class="light light-active" title="<?php _e('Active', 'giga-messenger-bots') ?>"></span>
                    <?php endif; ?>
                </h1>

                <h2 class="nav-tab-wrapper wp-clearfix">
                    <?php foreach ($tabs as $url => $title) : ?>
                        <a href="<?php echo add_query_arg('tab', $url); ?>"
                           class="nav-tab <?php echo (self::$current_tab === $url) ? 'nav-tab-active' : '' ?>"><?php echo $title ?></a>
                    <?php endforeach; ?>
                </h2>

                <form id="crm-form" name="crm_form" action="options.php" method="post">
                    
                    <?php require_once GIGA_INC_DIR . 'crm/sections/' . self::$current_tab . '.php'; ?>

                    <input type="hidden" name="_page_now" value="crm">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr($lead->user_id); ?>">
                    <input type="hidden" name="current_tab" value="<?php echo self::$current_tab ?>">
                    <?php wp_nonce_field('giga_lead_nonce', 'giga_lead_nonce'); ?>

                    <button type="submit" name="submit"
                            class="button button-primary"><?php _e('Save Changes', 'giga-messenger-bots'); ?></button>
                </form>
                
                <?php
            } catch (\Exception $e) { ?>
                <h1><?php _e('Sorry, the lead you requested does not exists', 'giga-messenger-bots'); ?></h1>
                <?php
            }
            ?>
        </div><!--.wrap-->
        <?php
    }
}