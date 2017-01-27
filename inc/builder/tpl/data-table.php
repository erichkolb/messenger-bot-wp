<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script type="text/ng-template" id="/data-table.html">
	<dt class="human">
	<div class="getting-started" ng-show="node.answers.length == 1 && node.answers[0].text == '' && node.pattern == ''">
		<?php _e( 'Play With The Left Section To Get Started', 'giga-messenger-bots' ); ?>
	</div>

	<div ng-hide="node.answers.length == 1 && node.answers[0].text == '' && node.pattern == ''">
        
        <div class="answer-text" ng-show="node.type === 'text'">
            <p>
                <span class="dashicons dashicons-admin-comments"></span> {{node.pattern}}
            </p>
        </div>

		<p ng-if="node.type === 'attachment'">
			<span class="dashicons dashicons-location" ng-show="node.pattern == 'location'"></span>
			<span class="dashicons dashicons-format-image" ng-show="node.pattern == 'image'"></span>
			<span class="dashicons dashicons-format-audio" ng-show="node.pattern == 'audio'"></span>
			<span class="dashicons dashicons-format-video" ng-show="node.pattern == 'video'"></span>
			<span class="dashicons dashicons-media-archive" ng-show="node.pattern == 'file'"></span> {{node.pattern}}
		</p>

		<p ng-if="node.type === 'intended'"><span class="emoji-inside">⚡️</span> {{node.pattern}}</p>

		<div class="buttons" ng-if="node.type == 'payload'">
			<button type="button" class="messenger-button"><span class="emoji-inside">👆</span> {{node.pattern}}
			</button>
		</div>

		<code ng-if="node.type =='default'"><?php _e( 'default', 'giga-messenger-bots' ); ?></code>

		<div class="button-group">
			<button title="<?php _e( 'Edit Node...', 'giga-messenger-bots' ); ?>" type="button"
			        class="button button-small" ng-click="editNode(node)">
				<span class="dashicons dashicons-edit"></span>
			</button>

			<button title="<?php _e( 'Remove Node', 'giga-messenger-bots' ); ?>" type="button"
			        class="button button-error button-small" ng-click="removeNode(node, $index)">&times;</button>
		</div>
	</div>
	</dt>
	<dd class="bot">
		<div class="answer-item" ng-repeat="answer in node.answers">

			<div ng-if="answer.text" class="answer-text">
				<p><span class="dashicons dashicons-admin-comments"></span> {{answer.text}}</p>
			</div>

			<div ng-if="answer.attachment && answer.attachment.type == 'image'">
				<a href="{{answer.attachment.payload.url}}">
					<img ng-src="{{answer.attachment.payload.url}}"
					     alt="<?php _e( 'Image Preview', 'giga-messenger-bots' ); ?>" width="250">
				</a>
			</div>

			<div ng-if="answer.attachment && answer.attachment.type == 'video'">
				<span class="dashicons dashicons-video-alt3"></span>

				{{answer.attachment.payload.url}}
			</div>

			<div ng-if="answer.attachment && answer.attachment.type == 'audio'">
				<span class="dashicons dashicons-format-audio"></span>

				{{answer.attachment.payload.url}}
			</div>

			<div ng-if="answer.attachment && answer.attachment.type == 'file'">
				<span class="dashicons dashicons-media-default"></span>

				{{answer.attachment.payload.url}}
			</div>

			<div class="lp-buttons"
				ng-if="answer.attachment && answer.attachment.type == 'template' && answer.attachment.payload.template_type == 'button'">
				{{answer.attachment.payload.text}} <br>

                <div class="buttons"><button class="messenger-button" type="button" ng-repeat="button in answer.attachment.payload.buttons"><span ng-show="button.title">{{button.title}}</span><span ng-show="button.type=='account_link'">Log In</span><span ng-show="button.type=='account_unlink'">Log Out</span></button></div>
            </div>

			<div ng-if="answer.type == 'callback'">
				<span class="dashicons dashicons-admin-network"></span> <?php _e( 'Closure', 'giga-messenger-bots' ); ?>
			</div>

			<div ng-if="answer.type == 'shortcode'">
				{{answer.content}}
			</div>
            
            <div ng-if="answer.type == 'command'" class="right">
                <span class="emoji-inside">☣</span> <?php _e('Update Lead', 'giga-messenger-bots'); ?>
            </div>

			<div class="lp-quick-replies" ng-if="answer.quick_replies">
				<ul>
					<li ng-repeat="quick_reply in answer.quick_replies" class="quick_reply">
						<span ng-if="quick_reply.content_type == 'text'">{{quick_reply.title}}</span>
						<span ng-if="quick_reply.content_type == 'location'"><i
								class="dashicons dashicons-location"></i> <?php _e( 'Send Location', 'giga-messenger-bots' ); ?></span>
					</li>
				</ul>
			</div>

			<div class="generic-carousel"
			     ng-if="answer.attachment && answer.attachment.type == 'template' && answer.attachment.payload.template_type == 'generic'">
				<div class="generic-bubble-item" ng-repeat="bubble in answer.attachment.payload.elements">
					<img ng-src="{{bubble.image_url}}" ng-if="bubble.image_url" alt="Image Bubble" width="180">
					<h4>{{bubble.title}}</h4>

					<p ng-show="bubble.subtitle">{{bubble.subtitle}}</p>
                    
					<div class="buttons"><button class="messenger-button" type="button" ng-repeat="button in bubble.buttons"><span ng-show="button.title">{{button.title}}</span><span ng-show="button.type=='account_link'">Log In</span><span ng-show="button.type=='account_unlink'">Log Out</span></button></div>
				</div>
			</div><!--.generic-carousel-->

            <div class="list-group"
                 ng-if="answer.attachment && answer.attachment.type == 'template' && answer.attachment.payload.template_type == 'list'">
                
                <div class="list-group-element {{answer.attachment.payload.top_element_style}}"
                     ng-repeat="element in answer.attachment.payload.elements">
                    
                    <div ng-show="$index == 0 && answer.attachment.payload.top_element_style != 'compact'"
                         class="element-background" style="background-image: url('{{element.image_url}}')"></div>

                    <div ng-show="$index != 0 || answer.attachment.payload.top_element_style == 'compact'"
                         class="element-thumbnail right">
                        <img class="right" ng-src="{{element.image_url}}" ng-show="element.image_url" alt="Element Image">
                    </div>
                    
                    <div class="element-description">
                        <h4>{{element.title}}</h4>
    
                        <div ng-show="element.subtitle">{{element.subtitle}}</div>
    
                        <div class="buttons" ng-show="element.buttons"><button class="messenger-button" type="button">{{element.buttons[0].title}}</button></div>
                    </div>
                </div><!--.list-group-element-->
                
                <div class="fake-button" ng-show="answer.attachment.payload.buttons">{{answer.attachment.payload.buttons[0].title}}</div>
            </div><!--.list-group-->

		</div><!--.answer-item-->
	</dd>
</script>