<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div ng-if="answersType[$index] == 'generic'" class="answer-properties"
     ng-init="answer.attachment.type = 'template'; answer.attachment.payload.template_type = 'generic'">

	<div ng-repeat="bubble in answer.attachment.payload.elements" class="generic-bubble">

		<div class="row">
			<button type="button" class="button right" ng-click="removeGenericBubble($index, answer);">&times;
			</button>
		</div>

		<div class="form-group">
			<label><?php _e( 'Title', 'giga-messenger-bots' ); ?> *</label><br>
			<input type="text" class="form-control" ng-model="bubble.title"
			       placeholder="<?php _e( 'Enter Title. Maximum: 45 characters.', 'giga-messenger-bots' ); ?>">
		</div>

		<div class="form-group">
			<label><?php _e( 'Subtitle', 'giga-messenger-bots' ); ?></label><br>
			<input type="text" class="form-control"
			       ng-model="bubble.subtitle"
			       placeholder="<?php _e( 'Enter subtitle. Maximum: 80 characters.',
				       'giga-messenger-bots' ); ?>">
		</div>

		<div class="form-group">
			<label><?php _e( 'Item URL', 'giga-messenger-bots' ); ?></label><br>
			<input type="url" class="form-control"
			       ng-model="bubble.item_url" placeholder="http://">
		</div>

		<div class="form-group">
			<label><?php _e( 'Image URL', 'giga-messenger-bots' ); ?></label><br>
			<input type="url" class="form-control"
			       ng-model="bubble.image_url" placeholder="http://">
		</div>

		<div class="buttons-group" ng-include src="'/buttons.html'"
		     ng-repeat="button in bubble.buttons"></div>

		<button type="button"
		        ng-show="bubble.buttons.length < 6 || !bubble.buttons.length"
		        ng-click="addGenericButton(bubble)" class="button">
			+ <?php _e( 'Add Button', 'giga-messenger-bots' ); ?></button>

		<div class="clear"></div>
	</div>

	<button type="button" class="button add-bubble" ng-click="addGenericBubble(answer);">
		+ <?php _e( 'Add Bubble', 'giga-messenger-bots' ); ?>
	</button>

</div>