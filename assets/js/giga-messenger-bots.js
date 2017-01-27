/**
 * This file creates drag and drop feature for Giga Messenger Bot
 *
 * @param  jQuery $ Alias of jQuery
 * @param  Angular angular Alias of Angular
 *
 * @return void
 */
;(function ($, angular) {
    'use strict';

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Create an app named `Bot`
    var app = angular.module('Bot', []);

    app.controller('BotController', function ($scope, $window, $http) {
        $scope.isLoading = false;

        // The node format of a Bot
        $scope.defaultNode = {
            type: 'text',
            pattern: '',
            answers: [
                {
                    text: ''
                }
            ]
        };

        $scope.node = {};

        // Payloads buttons
        $scope.payloads = [];

        $scope.nodes = [];

        $scope.answersType = ['text'];

        $scope.showMessage = false;

        $scope.waits = [];

        $scope.loginUrl = '';

        // For Subscription Module
        $scope.messages = [];
        $scope.search = '';
        $scope.leads = [];
        $scope.added = [];
        $scope.queued = [];

        /**
         * This method runs on page load. Load the required data.
         *
         * @return void
         */
        $scope.init = function () {

            var required = ['nodes', 'payloads', 'waits', 'channels'];

            angular.forEach(required, function (dependency) {
                if (typeof $window[dependency] != 'undefined') {
                    $scope[dependency] = $window[dependency];
                }
            });

            $scope.loginUrl = $window.loginUrl;

            $scope.node = angular.copy($scope.defaultNode);
        };

        /**
         * Init Method for Subscription Builder
         *
         * @return void
         */
        $scope.initMessageBuilder = function () {

            if (typeof $window.channels != 'undefined') {
                $scope.channels = $window.channels;
            }

            $scope.node = angular.copy($scope.defaultNode);

            if (typeof $window.answers != 'undefined') {
                $scope.node.answers = $window.answers;
                $scope.answersType = $scope.getAnswersTypeFromNode($scope.node);
            }
        };

        /**
         * Add default node content
         *
         * @return void
         */
        $scope.addAnswer = function () {
            $scope.node.answers.push({
                text: ''
            });

            $scope.answersType.push('text');
        };

        /**
         * Action runs when Add Button clicked
         *
         * @param answer Response
         */
        $scope.addButton = function (answer) {
            if (typeof answer.attachment.payload.buttons === 'undefined') {
                answer.attachment.payload.buttons = [];
            }

            answer.attachment.payload.buttons.push({
                'type': 'web_url',
                'title': '',
                'url': ''
            });
        };

        /**
         * Action runs when Add Button on Generic Template clicked
         *
         * @param bubble Bubble node
         */
        $scope.addGenericButton = function (bubble) {
            // If buttons doesn't exists. Create it.
            if (typeof bubble['buttons'] === 'undefined')
                bubble['buttons'] = [];

            bubble['buttons'].push({
                'type': 'web_url',
                'title': '',
                'url': ''
            });
        };

        $scope.addQuickReply = function (answer) {
            // If buttons doesn't exists. Create it.
            if (typeof answer['quick_replies'] === 'undefined') {
                answer['quick_replies'] = [];
            }

            answer.quick_replies.push({
                'content_type': 'text',
                'title': '',
                'payload': ''
            });
        };

        $scope.removeQuickReply = function ($index, answer) {
            answer.quick_replies.splice($index, 1);
        };

        /**
         * Action runs when Add Bubble clicked
         *
         * @param answer Response node
         */
        $scope.addGenericBubble = function (answer) {
            // If bubble doesn't exists. Create it.
            if (!angular.isArray(answer.attachment.payload.elements)) {
                answer.attachment.payload.elements = [];
            }

            answer.attachment.payload.elements.push({
                'image_url': '',
                'title': '',
                'subtitle': '',
                'buttons': []
            });
        };

        $scope.removeGenericBubble = function ($index, content) {
            content.attachment.payload.elements.splice($index, 1);
        };

        /**
         * When user clicks Edit button on each node. Scroll to top with node content
         *
         * @param node node
         */
        $scope.editNode = function (node) {
            // Callback is an object so we have to cast to array
            if (!angular.isArray(node.answers))
                node.answers = [node.answers];

            $scope.answersType = $scope.getAnswersTypeFromNode(node);

            $scope.node = node;
        };

        /**
         * Remove button from button group or bubble
         *
         * @param $index
         * @param content
         * @param bubble
         */
        $scope.removeButton = function ($index, content, bubble) {
            if (typeof content.buttons != 'undefined') {
                content.buttons.splice($index, 1);
            } else {
                bubble.buttons.splice($index, 1);
            }
        };

        /**
         * Remove Draft Response. This doesn't persist data, user have to save changes.
         *
         * @param $index
         */
        $scope.removeDraftAnswer = function ($index) {
            $scope.node.answers.splice($index, 1);
        };

        $scope.getAnswersTypeFromNode = function (node) {
            var answersType = [];

            angular.forEach(node.answers, function (answer, index) {

                if (typeof answer.type != 'undefined') {
                    answersType[index] = answer.type;
                }

                if (typeof answer.text != 'undefined') {
                    answersType[index] = 'text';
                }

                if (typeof answer.attachment != 'undefined' && typeof answer.attachment.type != 'undefined') {
                    answersType[index] = answer.attachment.type;

                    if (answer.attachment.type === 'template' && typeof answer.attachment.payload.template_type != 'undefined') {
                        answersType[index] = answer.attachment.payload.template_type;
                    }
                }
            });

            return answersType;
        };

        $scope.removeNode = function (node, $index) {
            if (!confirm('This can not be undone. Do you want to continue?'))
                return false;

            // Make a post update
            if (!$scope.isLoading) {
                $scope.isLoading = true;

                $http.post($window.ajaxurl + '?action=remove_node', {
                    action: 'remove_node',
                    node_id: node.id
                }).success(function () {
                    $scope.nodes.splice($index, 1);
                });

                $scope.isLoading = false;
            }
        };

        $scope.updateNode = function () {
            $scope.showMessage = false;

            // Make a post update
            if (!$scope.isLoading) {
                $scope.isLoading = true;

                $http.post($window.ajaxurl + '?action=update_node', {
                    action: 'update_node',
                    node: $scope.node
                }).success(function (response) {

                    if (typeof $scope.node.id == 'undefined' || !$scope.node.id) {
                        $scope.nodes.unshift(response.data);
                    }

                    $scope.node = angular.copy($scope.defaultNode);
                    $scope.answersType = $scope.getAnswersTypeFromNode($scope.node);

                    $scope.showMessage = true;
                });

                $scope.isLoading = false;
            }
        };

        $scope.onButtonTypeChange = function (button) {

            if (['web_url', 'account_link', 'account_unlink', 'element_share'].indexOf(button.type) !== -1) {
                delete button.payload;
            }

            if (['postback', 'phone_number', 'account_unlink', 'element_share'].indexOf(button.type) !== -1) {
                delete button.url;
                delete button.messenger_extensions;
                delete button.webview_height_ratio;
                delete button.fallback_url;
            }

            if (['account_link', 'account_unlink', 'element_share'].indexOf(button.type) !== -1) {
                delete button.title;
            }

            if (button.type === 'account_link') {
                button.url = $scope.loginUrl;
            }
        };

        /**
         * Handle search leads in subscription message form
         */
        $scope.$watch('search', function () {
            if (!$scope.isLoading && $scope.search.length > 0) {
                $scope.isLoading = true;

                $http.get($window.ajaxurl + '?action=search_leads', {
                    params: {
                        name: $scope.search
                    }
                }).success(function (response) {
                    $scope.queued = response.data;
                }).error(function (response) {
                    console.log(response.data);
                });

                $scope.isLoading = false;
            }
        });

        $scope.addLead = function ($index) {
            var isDuplicated = false;

            angular.forEach($scope.leads, function (lead) {
                if (lead.id === $scope.queued[$index].id) {
                    isDuplicated = true;
                    return false;
                }
            });

            if (isDuplicated)
                return false;

            $scope.leads.push($scope.queued[$index]);

            $scope.queued.splice($index, 1);
        };
    });

    app.controller('PersistentMenuController', function ($scope, $window) {
        $scope.items = [];

        $scope.item = {};

        $scope.isEdit = false;

        $scope.init = function () {
            if (typeof $window.persistent_menu !== 'undefined')
                $scope.items = $window.persistent_menu;
        };

        $scope.addItem = function () {
            if ($scope.item.type === 'web_url')
                delete $scope.item.payload;
            else
                delete $scope.item.web_url;

            $scope.items.push($scope.item);

            $scope.item = {};
        };

        $scope.editItem = function ($index) {
            $scope.isEdit = true;

            $scope.item = $scope.items[$index];
        };

        $scope.saveItem = function () {
            $scope.item = {};
            $scope.isEdit = false;
        };

        $scope.removeItem = function ($index) {
            $scope.items.splice($index, 1);
        };
    });

})(jQuery, angular);