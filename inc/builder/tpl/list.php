<?php if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'list'" class="answer-properties"
     ng-init="answer.attachment.type = 'template'; answer.attachment.payload.template_type = 'list'">

    <div class="form-group top-element-style">
        <label><input type="checkbox" ng-model="answer.attachment.payload.top_element_style" ng-true-value="'compact'"
                      ng-false-value="'large'">
            <?php _e('No Highlight Top Element', 'giga-messenger-bots'); ?></label>
    </div>
    
    <div ng-repeat="element in answer.attachment.payload.elements" class="list-element">

        <div class="row">
            <button type="button" class="button right" ng-click="removeGenericBubble($index, answer);">&times;
            </button>
        </div>

        <div class="form-group">
            <label><?php _e('Title', 'giga-messenger-bots'); ?> *</label><br>
            <input type="text" class="form-control" ng-model="element.title"
                   placeholder="<?php _e('Enter Title. Maximum: 45 characters.', 'giga-messenger-bots'); ?>">
        </div>

        <div class="form-group">
            <label><?php _e('Subtitle', 'giga-messenger-bots'); ?> *</label><br>
            <input type="text" class="form-control"
                   ng-model="element.subtitle"
                   placeholder="<?php _e('Enter subtitle. Maximum: 80 characters.',
                       'giga-messenger-bots'); ?>">
        </div>

        <div class="form-group">
            <label><?php _e('Image URL', 'giga-messenger-bots'); ?> *</label><br>
            <input type="url" class="form-control"
                   ng-model="element.image_url" placeholder="http://">
            <p class="description"><?php _e('Required if you want to highlight the top element', 'giga-messenger-bots'); ?></p>
        </div>

        <div class="default-action">
            <label>
                <?php _e('Default Action', 'giga-messenger-bots'); ?>
                <input type="checkbox" ng-model="element.default_action.type" ng-true-value="'web_url'"
                       ng-false-value="''">
            </label>

            <br>
            
            <p class="description"><?php _e('Action when leads tap on the element row'); ?></p>

            <div class="form-group" ng-if="element.default_action.type=='web_url'">
                <label><?php _e('Action URL', 'giga-messenger-bots'); ?></label>
                <input type="url" ng-model="element.default_action.url" class="form-control"
                       placeholder="<?php _e('Enter button URL', 'giga-messenger-bots'); ?>">
            </div>
            
            <div class="form-group" ng-if="element.default_action.type == 'web_url'">
                <label>
                    <input type="checkbox" ng-model="element.default_action.messenger_extensions">
                    <?php _e('Messenger Extension', 'giga-messenger-bots'); ?>
                </label>
            </div>

            <label ng-if="element.default_action.type == 'web_url' && element.default_action.messenger_extensions">
                <?php _e('Webview Height Ratio', 'giga-messenger-bots'); ?>
                <select class="form-control" ng-model="element.default_action.webview_height_ratio">
                    <option value="full"><?php _e('Full (Default)', 'giga-messenger-bots'); ?></option>
                    <option value="compact"><?php _e('Compact', 'giga-messenger-bots'); ?></option>
                    <option value="tail"><?php _e('Tail', 'giga-messenger-bots'); ?></option>
                </select>
            </label>

            <label ng-if="element.default_action.type == 'web_url' && element.default_action.messenger_extensions">
                <br>
                <?php _e('Fallback URL', 'giga-messenger-bots'); ?>
                <input type="url" ng-model="element.default_action.fallback_url" class="form-control">
            </label>
        </div>

        <div class="buttons-group" ng-include src="'/buttons.html'"
             ng-repeat="button in element.buttons"></div>

        <button type="button"
                ng-show="element.buttons.length < 1 || !element.buttons.length"
                ng-click="addGenericButton(element)" class="button">
            + <?php _e('Add Button', 'giga-messenger-bots'); ?></button>

        <div class="clear"></div>
    </div>

    <button type="button" class="button add-bubble" ng-click="addGenericBubble(answer);">
        + <?php _e('Add Element', 'giga-messenger-bots'); ?>
    </button>

    <div class="buttons-group" ng-include src="'/buttons.html'"
         ng-repeat="button in answer.attachment.payload.buttons"></div>
    
    <button type="button" class="button" ng-click="addButton(answer);">
        + <?php _e('Add Button', 'giga-messenger-bots'); ?>
    </button>

</div>