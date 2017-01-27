<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="answer-properties">

	<div class="buttons-group" ng-repeat="quick_reply in answer.quick_replies">
		<div class="row">
			<button type="button" class="button button-danger button-rounded right"
			        ng-click="removeQuickReply($index, answer);">
				<i class="dashicons dashicons-minus"></i>
			</button>
		</div>

		<div>
			<label><?php _e( 'Type', 'giga-messenger-bots' ); ?></label>
			<select ng-model="quick_reply.content_type" class="form-control">
				<option value="text"><?php _e( 'Text', 'giga-messenger-bots' ); ?></option>
				<option value="location"><?php _e( 'Location', 'giga-messenger-bots' ); ?></option>
			</select>
		</div>

		<div class="row">

			<div class="one-half" ng-if="quick_reply.content_type == 'text'">
				<label><?php _e( 'Title', 'giga-messenger-bots' ); ?> *</label>
				<input type="text" class="form-control" ng-model="quick_reply.title"
				       placeholder="<?php _e( 'Enter quick reply title', 'giga-messenger-bots' ); ?>">
			</div>

			<div class="one-half" ng-if="quick_reply.content_type == 'text'">
				<label><?php _e( 'Payload', 'giga-messenger-bots' ); ?> *</label>
				<input type="text" class="form-control" ng-model="quick_reply.payload"
				       placeholder="<?php _e( 'Enter quick reply payload', 'giga-messenger-bots' ); ?>">
			</div>

		</div>
	</div>

	<button type="button"
	        ng-show="(answer.quick_replies.length < 10 || ! answer.quick_replies.length) && answersType[$index] != 'button' && answersType[$index] != 'shortcode' && answersType[$index] != 'command' && answerIndex == node.answers.length-1"
	        ng-click="addQuickReply(answer)" class="button right">
		+ <?php _e( 'Quick Replies', 'giga-messenger-bots' ); ?></button>

	<div class="clear"></div>
</div>