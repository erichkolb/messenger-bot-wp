<div class="form-group">
    <label for="first_name"><?php _e( 'First Name', 'giga-messenger-bots' ); ?></label>
    <input type="text" name="first_name" id="first_name" class="form-control"
           value="<?php echo esc_attr($lead->first_name); ?>">
</div>

<div class="form-group">
    <label for="last_name"><?php _e( 'Last Name', 'giga-messenger-bots' ); ?></label>
    <input type="text" name="last_name" id="last_name" class="form-control"
           value="<?php echo esc_attr($lead->last_name); ?>">
</div>

<div class="form-group">
    <label for="email"><?php _e( 'Email', 'giga-messenger-bots' ); ?></label>
    <input type="text" name="email" id="email" class="form-control"
           value="<?php echo esc_attr($lead->email); ?>">
</div>

<div class="form-group">
    <label for="phone"><?php _e( 'Phone', 'giga-messenger-bots' ); ?></label>
    <input type="text" name="phone" id="phone" class="form-control"
           value="<?php echo esc_attr($lead->phone); ?>">
</div>

<div class="form-group">
    <label for="locale"><?php _e( 'Locale', 'giga-messenger-bots' ); ?></label>
    <input type="text" name="locale" id="locale" class="form-control"
           value="<?php echo esc_attr($lead->locale); ?>">
</div>

<div class="form-group">
    <label for="gender"><?php _e( 'Gender', 'giga-messenger-bots' ); ?></label>
    <select name="gender" id="gender" class="form-control">
        <option value=""
                disabled><?php _e( 'Please select', 'giga-messenger-bots' ); ?></option>
        <option
            value="male" <?php selected( $lead->gender, 'male' ); ?>><?php _e( 'Male', 'giga-messenger-bots' ); ?></option>
        <option
            value="female" <?php selected( $lead->gender, 'female' ); ?>><?php _e( 'Female', 'giga-messenger-bots' ); ?></option>
    </select>
</div>

<div class="form-group">
    <label for="auto_stop"><?php _e( 'Status', 'giga-messenger-bots' ); ?></label>
    <select name="auto_stop" id="auto_stop" class="form-control">
        <option value="" disabled><?php _e( 'Please select', 'giga-messenger-bots' ); ?></option>
        <option value="1" <?php selected( $lead->auto_stop, 1 ); ?>><?php _e( 'Waiting Answers from Page Administrator', 'giga-messenger-bots' ); ?></option>
        <option value="" <?php selected( $lead->auto_stop, '' ); ?>><?php _e( 'Active', 'giga-messenger-bots' ); ?></option>
    </select>
</div>