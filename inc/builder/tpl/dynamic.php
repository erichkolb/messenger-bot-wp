<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'shortcode'" class="form-group answer-properties"
     ng-init="answer.type = 'shortcode'">
	<label><?php _e( 'Content', 'giga-messenger-bots' ); ?> *</label><br>

	<textarea class="form-control" rows="8" ng-model="answer.content"></textarea>
</div>