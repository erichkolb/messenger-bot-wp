<?php if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>

<script type="text/ng-template" id="/buttons.html">

    <div class="row">
        <button type="button" class="button button-danger button-rounded right"
                ng-click="removeButton($index, answer.attachment.payload, bubble);">
            <i class="dashicons dashicons-minus"></i>
        </button>
    </div>

    <div class="row">
        <div class="one-half">
            <label><?php _e('Type', 'giga-messenger-bots'); ?></label>
            <select ng-model="button.type" class="form-control" ng-change="onButtonTypeChange(button)">
                <option value="web_url"><?php _e('Web URL', 'giga-messenger-bots'); ?></option>
                <option value="postback"><?php _e('Postback', 'giga-messenger-bots'); ?></option>
                <option value="element_share"><?php _e('Share', 'giga-messenger-bots'); ?></option>
                <option value="phone_number"><?php _e('Call Button', 'giga-messenger-bots'); ?></option>
                <option value="account_link"><?php _e('Account Link', 'giga-messenger-bots'); ?></option>
                <option value="account_unlink"><?php _e('Account Unlink', 'giga-messenger-bots'); ?></option>
            </select>
        </div>

        <div class="one-half"
             ng-if="button.type != 'account_link' && button.type != 'account_unlink' && button.type != 'element_share'">
            <label><?php _e('Title', 'giga-messenger-bots'); ?> *</label>
            <input type="text" class="form-control" ng-model="button.title"
                   placeholder="<?php _e('Enter button title', 'giga-messenger-bots'); ?>">
        </div>
    </div><!--.row-->

    <div class="row">
        <label ng-if="button.type == 'web_url' || button.type == 'account_link'"><?php _e('URL', 'giga-messenger-bots'); ?>
            *
            <input type="url" ng-model="button.url" class="form-control"
                   placeholder="<?php _e('Enter button URL', 'giga-messenger-bots'); ?>">
        </label>
    </div>

    <div class="row">
        <div class="form-group">
            <label ng-if="button.type == 'web_url'">
                <input type="checkbox" ng-model="button.messenger_extensions">
                <?php _e('Messenger Extension', 'giga-messenger-bots'); ?>
            </label>
        </div>

        <div class="form-group">
            <label ng-if="button.type == 'web_url' && button.messenger_extensions">
                <?php _e('Webview Height Ratio', 'giga-messenger-bots'); ?>
                <select class="form-control" ng-model="button.webview_height_ratio">
                    <option value="full"><?php _e('Full (Default)', 'giga-messenger-bots'); ?></option>
                    <option value="compact"><?php _e('Compact', 'giga-messenger-bots'); ?></option>
                    <option value="tail"><?php _e('Tail', 'giga-messenger-bots'); ?></option>
                </select>
            </label>
            
            <label ng-if="button.type == 'web_url' && button.messenger_extensions">
                <br>
                <?php _e('Fallback URL', 'giga-messenger-bots'); ?>
                <input type="url" ng-model="button.fallback_url" class="form-control">
            </label>
        </div>
    </div>

    <div class="row">
        <label ng-if="button.type == 'postback'"><?php _e('Payload', 'giga-messenger-bots'); ?> *
            <input type="text" ng-model="button.payload" class="form-control"
                   placeholder="<?php _e('Enter button payload name', 'giga-messenger-bots'); ?>">
        </label>

        <label ng-if="button.type == 'phone_number'"><?php _e('Payload', 'giga-messenger-bots'); ?> *
            <input type="text" ng-model="button.payload" class="form-control"
                   placeholder="<?php _e('Your own phone number which let leads tap to call', 'giga-messenger-bots'); ?>">
        </label>
    </div>
</script>