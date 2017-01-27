<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$checked = (isset($this->settings['auto_stop']) && $this->settings['auto_stop'] != false ) ? 'checked' : '';

$stop_when = (isset($this->settings['auto_stop']['stop_when']) && $this->settings['auto_stop'] != false)
                ? $this->settings['auto_stop']['stop_when'] : '*';

$restart_when = (isset($this->settings['auto_stop']['restart_when']) && $this->settings['auto_stop'] != false)
    ? $this->settings['auto_stop']['restart_when'] : ':)';
?>
<tr>
    <td scope="row" colspan="2">
        <label><strong><?php _e('Auto Stop', 'giga-messenger-bots'); ?></strong>
            <input ng-model="auto_stop" ng-init="auto_stop='<?php echo $checked ?>'"
                   ng-true-value="'checked'" type="checkbox"
                   name="check_auto_stop" value="1" <?php echo $checked ?>>
        </label>

        <p class="description">
            <?php _e('Auto stop feature helps you stop the bot when Page Administrators
            chat with your leads so the conversation more naturally. In order to enable Auto Stop. You must set your Page ID in the Basics tab.', 'giga-messenger-bots'); ?>
        </p>
    </td>
</tr>

<tr ng-show="auto_stop == 'checked'">
    <th scope="row">
        <label for="stop_when"><?php _e( 'Stop the bot when Page Administrator text', 'giga-messenger-bots'); ?> </label>
    </th>
    <td>
        <input type="text" id="stop_when" name="auto_stop[stop_when]" value="<?php echo $stop_when ?>"
               class="form-control">
        <p class="description"><?php _e ('* for any character', 'giga-messenger-bot'); ?></p>
    </td>
</tr>

<tr ng-show="auto_stop == 'checked'">
    <th scope="row">
        <label for="restart_when"><?php _e('Restart the bot when Page Administrator text', 'giga-messenger-bot'); ?> *</label>
    </th>
    <td>
        <input type="text" id="restart_when" name="auto_stop[restart_when]" value="<?php echo $restart_when ?>" class="form-control">
        <p class="description"><?php _e ('Cannot be blank or *', 'giga-messenger-bot'); ?></p>
    </td>
</tr>