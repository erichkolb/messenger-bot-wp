<?php if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>

<div id="node" class="user-action">
    <label><?php _e('When Receive', 'giga-messenger-bots'); ?>

        <select ng-model="node.type" ng-change="node.pattern = ''">
            <option value="text"><?php _e('Text', 'giga-messenger-bots'); ?></option>
            <option value="payload"><?php _e('Click', 'giga-messenger-bots'); ?></option>
            <option value="attachment"><?php _e('Attachment'); ?></option>
            <option value="intended"><?php _e('Intended Action', 'giga-messenger-bots'); ?></option>
            <option value="default"><?php _e('Others (Default Message)', 'giga-messenger-bots'); ?></option>
        </select>

        <div id="pattern">
            <div class="form-group" ng-if="node.type=='text'">
                <textarea class="user-action-input form-control" type="text"
                          ng-model="node.pattern" id="input-text-pattern" autofocus></textarea>
                <p class="text-small"><?php _e('The text or pattern when bot received.', 'giga-messenger-bots'); ?></p>
            </div>

            <div class="form-group" ng-if="node.type=='payload'">
                <input type="text" list="patterns" ng-model="node.pattern" class="form-control">
                <datalist id="patterns">
                    <option value="{{value}}" ng-repeat="(key, value) in payloads">
                </datalist>
                <p class="text-small"><?php _e('The button when people clicked.', 'giga-messenger-bots'); ?></p>
            </div>

            <div class="form-group" ng-if="node.type=='attachment'">
                <select class="user-action-input" ng-model="node.pattern">
                    <option value="location"><?php _e('Location', 'giga-messenger-bots'); ?></option>
                    <option value="image"><?php _e('Image', 'giga-messenger-bots'); ?></option>
                    <option value="audio"><?php _e('Audio', 'giga-messenger-bots'); ?></option>
                    <option value="video"><?php _e('Video', 'giga-messenger-bots'); ?></option>
                    <option value="file"><?php _e('File', 'giga-messenger-bots'); ?></option>
                </select>
                <p class="text-small"><?php _e('The attachment type received from people.', 'giga-messenger-bots'); ?></p>
            </div>

            <div class="form-group" ng-if="node.type == 'intended'">
                <input type="text" list="waits" ng-model="node.pattern" class="form-control">
                <datalist id="waits">
                    <option value="{{value}}" ng-repeat="value for (key, value) in waits">
                </datalist>
            </div>
        </div>
    </label>

    <br>

    <div class="bot-answers">
        <div class="bot-answer" ng-repeat="(answerIndex, answer) in node.answers">
            <button type="button" class="button button-rounded button-danger right remove-draft-answer"
                    ng-show="$index > 0" ng-click="removeDraftAnswer($index)"><i class="dashicons dashicons-minus"></i>
            </button>

            <div class="group answer-with" ng-show="answer.type != 'intended'">
                <span class="badge">{{$index + 1}}</span>
                <label>
                    <span ng-show="$index == 0"><?php _e('Bot will', 'giga-messenger-bots'); ?></span>
                    <span ng-show="$index > 0"><?php _e('Then bot will', 'giga-messenger-bots'); ?></span>
                </label>

                <select ng-model="answersType[$index]" ng-change="node.answers[$index] = {}">
                    <option value="text"><?php _e('Send Text', 'giga-messenger-bots'); ?></option>
                    <option value="image"><?php _e('Send Image', 'giga-messenger-bots'); ?></option>
                    <option value="video"><?php _e('Send Video', 'giga-messenger-bots'); ?></option>
                    <option value="audio"><?php _e('Send Audio', 'giga-messenger-bots'); ?></option>
                    <option value="file"><?php _e('Send File', 'giga-messenger-bots'); ?></option>
                    <option value="button"><?php _e('Send Buttons', 'giga-messenger-bots'); ?></option>
                    <option value="generic"><?php _e('Send Generic', 'giga-messenger-bots'); ?></option>
                    <option value="list"><?php _e('Send List', 'giga-messenger-bots'); ?></option>
                    <option value="shortcode"><?php _e('Send Dynamic Shortcode', 'giga-messenger-bots'); ?></option>
                    <option value="command"><?php _e('Update Current Lead', 'giga-messenger-bots'); ?></option>
                    <option value="callback" disabled><?php _e('Closure', 'giga-messenger-bots'); ?></option>
                </select>

                <div ng-show="answersType[$index] == 'callback'"><?php _e('Callback closure is not editable', 'giga-messenger-bots'); ?></div>
            </div>

            <div class="answer-meta">
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/text.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/intended.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/media.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/button.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/generic.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/list.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/dynamic.php'; ?>
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/command.php'; ?>

                <?php require_once GIGA_INC_DIR . 'builder/tpl/quick_replies.php'; ?>
                
            </div>
        </div><!--bot-answer-->
    </div><!--.bot-answers-->

    <hr>

    <div class="advanced">
        <div id="node-tags" ng-show="node.tags != null">
            <label><?php _e('Tagged As', 'giga-messenger-bots'); ?></label>
            <input type="text" ng-model="node.tags" class="form-control">
        </div>

        <div id="node-wait" ng-show="node.wait != null">
            <label><?php _e('Wait', 'giga-messenger-bots'); ?></label>
            <input type="text" ng-model="node.wait" class="form-control">
            <p class="description"><?php _e('Your intended action name', 'giga-messenger-bots'); ?></p>
        </div>
    </div>
    
    <button type="button" class="button" ng-show="node.answers.length < 10" ng-click="addAnswer();">
        + <?php _e('Add Bot Action', 'giga-messenger-bots'); ?>
    </button>

    <div class="button-group right">
        <button title="<?php _e('Add Tag', 'giga-messenger-bots'); ?>" type="button" class="button"
                ng-click="node.tags = ''"><?php _e('Tag', 'giga-messenger-bots'); ?></button>
        <button title="<?php _e('Add Intended Action', 'giga-messenger-bots'); ?>" type="button" class="button"
                ng-click="node.wait = ''"><?php _e('Wait', 'giga-messenger-bots'); ?></button>
    </div>
    <br><br>
    <button type="button" class="button button-primary"
            ng-click="updateNode()"><?php _e('Save Changes', 'giga-messenger-bots'); ?></button>
</div><!--#node-->