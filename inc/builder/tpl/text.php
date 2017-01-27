<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div ng-show="answersType[$index] == 'text'" class="form-group answer-properties">
	<label><?php _e( 'Content', 'giga-messenger-bots' ); ?> *</label><br>
	<textarea class="form-control" ng-model="answer.text"
	          placeholder="<?php _e( 'Enter Message Content', 'giga-messenger-bots' ); ?>"></textarea>
</div>