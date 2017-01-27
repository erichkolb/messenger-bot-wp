<div class="form-group">
    <h3>Subscription Channels</h3>
    <label>This lead is currently subscribing</label>
    <?php
    $channels = giga_get_channels();
    $lead_channels = explode(',', $lead->subscribe);
    
    foreach ($channels as $channel):
        $checked = in_array($channel, $lead_channels) ? 'checked' : '';
    ?>
        <div class="subscription-channel">
            <label>
                <input type="checkbox" name="channels[]" value="<?php echo $channel ?>" <?php echo $checked ?>> <?php echo $channel ?>
            </label>
        </div>
    <?php endforeach; ?>
    
    <p class="description">Create new channel and subscribe</p>
    <div class="form-group">
        <input type="text" name="channels[]" class="regular-text" placeholder="Channel name">
    </div>
</div>