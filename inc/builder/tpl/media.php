<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div
	ng-if="answersType[$index] == 'video' || answersType[$index] == 'audio' || answersType[$index] == 'file' || answersType[$index] == 'image'"
	class="form-group response-properties">
	<label><span class="capitalize">{{answersType[$index]}}</span> <?php _e( 'URL', 'giga-messenger-bot' ); ?> *</label><br>
	<input type="text" class="form-control" ng-model="answer.attachment.payload.url" placeholder="http://"
	       ng-change="answer.attachment.type = answersType[$index]">
</div>