<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<tr>
    <th scope="row">
        <label for="greeting_text"><?php _e('Greeting Text', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <textarea id="greeting_text" name="greeting_text" type="text"
                  class="form-control" rows="3"><?php $this->sanitized_field_value('greeting_text') ?></textarea>
    </td>
</tr>

<tr>
    <th scope="row">
        <label for="get_started_button_payload"><?php _e('Get Started Button Payload', 'giga-messenger-bots'); ?></label>
    </th>
    <td>
        <input id="get_started_button_payload" name="get_started_button_payload"
               type="text" class="form-control"
               value="<?php $this->sanitized_field_value('get_started_button_payload') ?>">
    </td>
</tr>

<tr>
    <th scope="row" colspan="2"><?php _e('Persistent Menu', 'giga-messenger-bots'); ?></th>
</tr>

<tr>
    <td colspan="2">
        <nav id="persistent-menu">
            <em><?php _e('Preview', 'giga-messenger-bots'); ?></em>
            <ul>
                <li ng-click="editItem($index)" ng-repeat="item in items track by $index"
                    title="{{item.type === 'postback' ? 'Payload: ' + item.payload : 'Web URL: ' + item.url}} - <?php _e('Click to Edit', 'giga-messenger-bots'); ?>">
                    <button class="button button-rounded button-danger" type="button"
                            ng-click="removeItem($index);"><i
                                class="dashicons dashicons-minus"></i>
                    </button>
                    {{item.title}}
                </li>
            </ul>
        </nav>

        <div id="persistent-menu-builder">

            <div class="row">
                <div class="one-half">
                    <label><?php _e('Type', 'giga-messenger-bots'); ?></label>
                    <select ng-model="item.type" class="form-control">
                        <option
                                value="web_url"><?php _e('Web URL', 'giga-messenger-bots'); ?></option>
                        <option
                                value="postback"><?php _e('Postback', 'giga-messenger-bots'); ?></option>
                    </select>
                </div>

                <div class="one-half">
                    <label><?php _e('Title', 'giga-messenger-bots'); ?> *</label>
                    <input type="text" class="form-control" ng-model="item.title"
                           placeholder="<?php _e('Enter button title', 'giga-messenger-bots'); ?>">
                </div>
            </div><!--.row-->

            <div class="row">
                <label
                        ng-if="item.type == 'web_url'"><?php _e('URL', 'giga-messenger-bots'); ?>
                    *
                    <input type="url" ng-model="item.url" class="form-control"
                           placeholder="<?php _e('Enter button URL', 'giga-messenger-bots'); ?>">
                </label>

                <label
                        ng-if="item.type == 'postback'"><?php _e('Payload', 'giga-messenger-bots'); ?>
                    *
                    <input type="text" ng-model="item.payload" class="form-control"
                           placeholder="<?php _e('Enter button payload name', 'giga-messenger-bots'); ?>">
                </label>
            </div>
        </div>


        <button ng-show="! isEdit" type="button" class="button"
                ng-click="addItem();"><?php _e('Add Menu Item', 'giga-messenger-bots'); ?></button>
        <button ng-show="isEdit" type="button" class="button"
                ng-click="saveItem();"><?php _e('Save Menu Item', 'giga-messenger-bots'); ?></button>

        <input type="hidden" name="persistent_menu" value="{{items}}">
    </td>
</tr>