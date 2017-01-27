<?php if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>

<div id="node" class="user-action">

    <section id="to">
        <h4><?php _e('To Channels', 'giga-messenger-bots'); ?></h4>

        <div class="form-group" id="to-channel">
            <ul>
                <?php
                $channels = giga_get_channels();
                foreach ($channels as $channel) :
                    $checked = in_array($channel, explode(',', $this->message->to_channel)) ? 'checked' : '';
                    ?>
                    <li>
                        <label>
                            <input type="checkbox" name="to_channel[]"
                                   value="<?php echo esc_attr($channel); ?>" <?php echo $checked ?>> <?php echo esc_html($channel) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <br>

    <section class="bot-answers">
        <h4><?php _e('With Messages', 'giga-messenger-bots'); ?></h4>
        <div class="bot-answer" ng-repeat="(answerIndex, answer) in node.answers">
            <button type="button" class="button button-rounded button-danger right remove-draft-answer"
                    ng-show="$index > 0" ng-click="removeDraftAnswer($index)"><i class="dashicons dashicons-minus"></i>
            </button>

            <div class="group answer-with" ng-show="answer.type != 'intended'">
                <span class="badge">{{$index + 1}}</span>

                <select ng-model="answersType[$index]" ng-change="node.answers[$index] = {}">
                    <option value="text"><?php _e('Text', 'giga-messenger-bots'); ?></option>
                    <option value="image"><?php _e('Image', 'giga-messenger-bots'); ?></option>
                    <option value="video"><?php _e('Video', 'giga-messenger-bots'); ?></option>
                    <option value="audio"><?php _e('Audio', 'giga-messenger-bots'); ?></option>
                    <option value="file"><?php _e('File', 'giga-messenger-bots'); ?></option>
                    <option value="button"><?php _e('Buttons', 'giga-messenger-bots'); ?></option>
                    <option value="generic"><?php _e('Generic Template', 'giga-messenger-bots'); ?></option>
                    <option value="list"><?php _e('List', 'giga-messenger-bots'); ?></option>
                    <option value="quick_replies"
                            ng-if="$index > 0"><?php _e('Quick Replies', 'giga-messenger-bots'); ?></option>
                    <option value="shortcode"><?php _e('Dynamic Shortcode', 'giga-messenger-bots'); ?></option>
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
                
                <?php require_once GIGA_INC_DIR . 'builder/tpl/quick_replies.php'; ?>

            </div>
        </div><!--bot-answer-->

        <div class="advanced">
            <?php $ng_init = ( ! empty($this->message->wait)) ? "ng-init=\"node.wait='" . esc_attr($this->message->wait) . "'\"" : ''; ?>
            <div id="node-wait" ng-show="node.wait != null" <?php echo $ng_init; ?>">
                <label><?php _e('Wait', 'giga-messenger-bots'); ?></label>
                <input type="text" name="wait" ng-model="node.wait" class="form-control">
                <p class="description"><?php _e('Your intended action name', 'giga-messenger-bots'); ?></p>
            </div>
        </div>

        <button ng-show="node.answers.length < 10" type="button" class="button" ng-click="addAnswer();">
            + <?php _e('Add Message', 'giga-messenger-bots'); ?>
        </button>

        <button title="<?php _e('Add Intended Action', 'giga-messenger-bots'); ?>" type="button" class="button"
                ng-click="node.wait = ''"><?php _e('Wait', 'giga-messenger-bots'); ?></button>

    </section><!--.bot-answers-->

    <div class="form-group">
        <textarea placeholder="<?php _e('Subscription Description', 'giga-messenger-bots'); ?>"
                  name="description" id="description" class="form-control"
                  rows="1"><?php echo esc_html($this->message->description); ?></textarea>
        <p class="description"><?php _e("Subscription description isn't required but recommended as it's easier
            to manage.", 'giga-messenger-bots'); ?></p>
    </div>

    <section id="timing">
        <h4><span class="dashicons dashicons-clock"></span> <?php _e('Timing Settings', 'schedule-settings'); ?></h4>
        <?php $schedules = wp_get_schedules(); ?>

        <div class="form-group">
            <label for="routines"><?php _e('Routines', 'giga-messenger-bots'); ?></label>
            <select id="routines" class="form-control" name="routines" ng-model="routines"
                    ng-init="routines='<?php echo $this->message->routines ?>'">
                <option value=""><?php _e('Immediately. No Schedule.'); ?></option>
                <?php foreach ($schedules as $key => $value):
                    $selected = $key === $this->message->routines ? 'selected' : '';
                    ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php echo $selected ?>><?php echo esc_html($value['display']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="send-limit"><?php _e('Sending Limit', 'giga-messenger-bots'); ?></label>
            <input type="number" id="send-limit" name="send_limit"
                   value="<?php echo intval($this->message->send_limit); ?>" class="form-control"
                   placeholder="<?php _e('0 for unlimited', 'giga-messenger-bots'); ?>">
            <p class="description"><?php _e('Each time we send mass notification to all leads can be considered as 1 time. Not total of leads times. Zero for unlimited.'); ?></p>
        </div>
        <?php
        $start_at = '';
        $end_at = '';
        
        if (strtotime($this->message->start_at)) {
            $start_at = date('Y-m-d H:i:s', strtotime($this->message->start_at));
        }
        
        if (strtotime($this->message->end_at)) {
            $end_at = date('Y-m-d H:i:s', strtotime($this->message->end_at));
        }
        ?>
        <div class="form-group" ng-show="routines != ''">
            <label for="start-at"><?php _e('Start at', 'giga-messenger-bots'); ?></label>
            <input type="text" class="form-control" name="start_at" id="start-at"
                   class="form-control" value="<?php echo $start_at ?>">
            <p class="description"><?php _e('Leaves blank to start the message routine now. You can also set the time to start in the future.', 'giga-messenger-bots'); ?></p>
        </div>

        <div class="form-group" ng-show="routines != ''">
            <label for="end-at"><?php _e('End at', 'giga-messenger-bots'); ?></label>

            <input type="text" class="form-control" name="end_at"
                   value="<?php echo $end_at ?>" id="end-at"
                   class="form-control">
            <p class="description"><?php _e('When to stop sending this message routine? Leaves blank to keep sending forever.', 'giga-messenger-bots'); ?></p>
        </div>
    </section>
    <hr>

    <button type="submit" class="button button-primary"><?php _e('Save Changes', 'giga-messenger-bots'); ?></button>
    <input type="submit" name="send_message" value="<?php _e('Save & Send Instant Message', 'giga-messenger-bots'); ?>" class="button button-success">
    
    <?php if (isset($_GET['id'])) :
        $href = add_query_arg([
            'action'      => 'delete',
            '_giga_nonce' => wp_create_nonce('delete_message'),
        ]);
        ?>
        <span class="delete-action right">
            <a class="text-danger deletion menu-delete"
               href="<?php echo $href ?>"><?php _e('Delete', 'giga-messenger-bots'); ?></a>
        </span>
    <?php endif; ?>
</div><!--#node-->