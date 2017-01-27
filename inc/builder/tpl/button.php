<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'button'" class="answer-properties"
     ng-init="answer.attachment.type = 'template'; answer.attachment.payload.template_type = 'button'">
	<label><?php _e( 'Call to action title', 'giga-messenger-bots' ); ?></label>

	<input type="text" class="form-control"
	       ng-model="answer.attachment.payload.text"
	       placeholder="<?php _e( 'Enter call-to-action title', 'giga-messenger-bots' ); ?>">

	<div class="buttons-group" ng-include src="'/buttons.html'"
	     ng-repeat="button in answer.attachment.payload.buttons"></div>

	<button type="button"
	        ng-show="answer.attachment.payload.buttons.length < 3 || !answer.attachment.payload.buttons.length"
	        ng-click="addButton(answer)" class="button">
		+ <?php _e( 'Add Button', 'giga-messenger-bots' ); ?></button>

	<div class="clear"></div>
</div>