<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'intended'" class="form-group answer-properties">
	<label><?php _e( 'Wait', 'giga-messenger-bots' ); ?> *</label><br>
	<input type="text" class="form-control" ng-model="node.wait"
	       placeholder="<?php _e( 'Action name', 'giga-messenger-bots' ); ?>">
</div>