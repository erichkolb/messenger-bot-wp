<?php if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'command'" class="form-group answer-properties"
     ng-init="answer.type = 'command'">
    <label><?php _e('Command', 'giga-messenger-bots'); ?> *<br>
        <select ng-model="answer.content.command">
            <option value="" disabled><?php _e('Please select', 'giga-messenger-bots'); ?></option>
            <option value="addChannel"><?php _e('Add Lead to Subscription Channels', 'giga-messenger-bots'); ?></option>
            <option value="removeChannel"><?php _e('Remove Lead from Subscription Channels', 'giga-messenger-bots'); ?></option>
        </select>
    </label>
    
    <br><br>
    
    <div class="subscription-channels-checkbox-group"
         ng-show="answer.content.command == 'addChannel' || answer.content.command == 'removeChannel'">
        <label><?php _e('Channels', 'giga-messenger-bots'); ?></label>
        <div ng-repeat="channel in channels">
            <label>
                <input type="checkbox" ng-model="answer.content.args.channels[channel]"> {{channel}}
            </label>
        </div>
    </div>
</div>