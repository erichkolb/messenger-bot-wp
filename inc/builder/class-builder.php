<?php

namespace GigaAI;

if ( ! defined('ABSPATH')) {
    exit;
}

use GigaAI\Storage\Eloquent\Node;

class Builder
{
    /**
     * Nodes in current page
     *
     * @var array
     */
    private $nodes = [];
    
    /**
     * Total pages
     *
     * @var integer
     */
    private $pages = 1;
    
    /**
     * Current page
     *
     * @var integer
     */
    private $paged = 1;
    
    /**
     * Current search keyword
     *
     * @var string
     */
    private $search = '';
    
    /**
     * Current tag filter
     *
     * @var string
     */
    private $tag = '';
    
    /**
     * Total records
     *
     * @var integer
     */
    private $total = 0;
    
    /**
     * Builder constructor.
     *
     * Register actions and assets
     */
    public function __construct()
    {
        add_action('wp_ajax_remove_node', [$this, 'ajax_remove_node']);
        
        add_action('wp_ajax_update_node', [$this, 'ajax_update_node']);
        
        add_action('admin_init', [$this, 'admin_init']);
        
        add_action('admin_menu', [$this, 'settings_page']);
        
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue'], 99);
    }
    
    /**
     * Enqueue styles and script in admin.
     *
     * @return void
     */
    public function admin_enqueue()
    {
        $payloads = giga_get_payload_buttons();
        $waits = giga_get_intended_actions();
        
        $login_url = giga_setting('account_linking_url');
        if (empty($login_url) || is_null($login_url)) {
            $login_url = wp_login_url();
        }
        
        wp_localize_script('giga-messenger-bots', 'nodes', $this->nodes);
        wp_localize_script('giga-messenger-bots', 'payloads', $payloads);
        wp_localize_script('giga-messenger-bots', 'waits', $waits);
        wp_localize_script('giga-messenger-bots', 'loginUrl', $login_url);
        wp_localize_script('giga-messenger-bots', 'channels', giga_get_channels());
    }
    
    /**
     * Register Settings Page with menu under Giga AI
     */
    public function settings_page()
    {
        add_submenu_page('giga',
            __('Bot Builder', 'giga-messenger-bots'),
            __('Bot Builder', 'giga-messenger-bots'),
            'manage_options',
            'bot-builder',
            [$this, 'render']
        );
    }
    
    public function admin_init()
    {
        if ( ! isset($_GET['page']) || $_GET['page'] !== 'bot-builder') {
            return;
        }
        
        $bot = giga_bot_instance();
        
        do_action('giga_pre_seed', $bot);
        
        $this->load_nodes();
    }
    
    /**
     * Load Nodes from DB
     */
    public function load_nodes()
    {
        $this->paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? intval($_GET['paged']) : 1;
        
        $per_page = 20;
        $offset = ($this->paged - 1) * $per_page;
        
        $this->search = isset($_GET['s']) ? trim($_GET['s']) : '';
        $this->tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
        
        $this->total = Node::notFluentIntended()
            ->search($this->search)->ofTag($this->tag)->count();
        
        $this->nodes = Node::notFluentIntended()
            ->search($this->search)->ofTag($this->tag)
            ->orderBy('created_at', 'DESC')->skip($offset)->take($per_page)
            ->get()->toArray();
        
        $this->pages = ceil($this->total / $per_page);
    }
    
    /**
     * AJAX action to remove a node
     */
    public function ajax_remove_node()
    {
        // Retrieve data from stream
        $request = json_decode(file_get_contents('php://input'));
        
        $node_id = intval($request->node_id);
        
        if (Node::find($node_id)->delete()) {
            wp_send_json_success([
                'success' => 1,
            ]);
        }
        
        wp_send_json_error([
            'error' => 1,
        ]);
    }
    
    /**
     * AJAX action to update a node. Please note that both Insert and Update is handled here
     *
     * @return Json
     */
    public function ajax_update_node()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        
        if (empty($request['node']) || empty($request['node']['answers'])) {
            die(0);
        }
        
        $node = $request['node'];
        
        // Sanitize data and update
        if ( ! empty($node['type'])) {
            $node['type'] = sanitize_text_field($node['type']);
        }
        
        if ( ! empty($node['pattern'])) {
            $node['pattern'] = sanitize_text_field($node['pattern']);
        }
        
        // New Node
        if ( ! isset($node['id']) || ! is_numeric($node['id'])) {
            
            $exists = Node::where(['type' => $node['type'], 'pattern' => $node['pattern']])->first();
            
            if ($exists) {
                $this->update_node($exists['id'], $node);
            } else {
                $node = Node::create($node);
                
                $node = $node->toArray();
            }
        } else {
            $node_id = intval($node['id']);
            
            $this->update_node($node_id, $node);
        }
        
        // Update payload buttons on the cache
        giga_update_payload_cache($node['answers']);
        
        wp_send_json_success($node);
    }
    
    /**
     * Update a node
     *
     * @param  Int   $id   Node ID
     * @param  Array $data Node Data
     *
     * @return Int Affected rows
     */
    private function update_node($id, $data)
    {
        $affected_rows = Node::find($id)->update($data);
        
        return $affected_rows;
    }
    
    /**
     * Render Settings Page Content
     *
     * @return void
     */
    public function render()
    {
        ?>
        <div class="wrap giga" id="bot-builder" ng-app="Bot" ng-controller="BotController" ng-init="init();">
            <h2><?php _e('Bot Builder', 'giga-messenger-bots'); ?></h2>

            <div id="message" class="updated notice is-dismissible" ng-show="showMessage">
                <p><?php _e('Nodes', 'giga-messenger-bots'); ?> <strong>
                        <?php _e('Updated', 'giga-messenger-bots'); ?></strong>
                </p>
                <button type="button" class="notice-dismiss"><span
                            class="screen-reader-text"><?php _e('Dismiss this notice.', 'giga-messenger-bots'); ?></span>
                </button>
            </div>

            <table id="bot-builder-form">
                <tr>
                    <td id="builder-gui">
                        <h3 ng-hide="node.id"><?php _e('Add New Node', 'giga-messenger-bots'); ?></h3>
                        <h3 ng-show="node.id"><?php _e('Edit Node', 'giga-messenger-bots'); ?></h3>
                        
                        <?php require_once GIGA_INC_DIR . 'builder/tpl/node-builder.php'; ?>
                    </td>

                    <td class="conversation" id="conversation">
                        <?php require_once GIGA_INC_DIR . 'builder/tpl/data-table.php'; ?>

                        <h3><?php _e('Live Preview', 'giga-messenger-bots'); ?></h3>
                        <dl ng-include src="'/data-table.html'"></dl>

                        <hr>

                        <h3><?php _e('Conversation', 'giga-messenger-bots'); ?></h3>

                        <form method="get" action="admin.php">
                            <input type="hidden" name="page" value="bot-builder">

                            <div class="form-group">
                                <input id="node-search" type="text" name="s" value="<?php echo $this->search ?>"
                                       placeholder="<?php _e('Search...', 'giga-messenger-bots'); ?>"
                                       class="form-control">

                                <select id="node-tag-filter" name="tag">
                                    <option value=""><?php _e('All Tags', 'giga-messenger-bots'); ?></option>
                                    <?php $tags = giga_get_tags();
                                    foreach ($tags as $tag):
                                        ?>
                                        <option value="<?php echo $tag ?>" <?php selected($tag, $this->tag) ?>><?php echo $tag ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit"
                                        class="button"><?php _e('Search', 'giga-messenger-bots'); ?></button>
                            </div>
                        </form>

                        <dl ng-repeat="node in nodes" ng-init="normalizeAnswers(node);" ng-include
                            src="'/data-table.html'"></dl>

                        <div class="pull-right pagination">
                            <?php
                            echo paginate_links([
                                'base'    => add_query_arg('paged', '%#%'),
                                'format'  => '?paged=%#%',
                                'current' => max(1, $this->paged),
                                'total'   => $this->pages,
                            ]);
                            ?>
                        </div><!--.pagination-->
                    </td>
                </tr>

                <input type="hidden" id="remove-node-nonce" ng-model="remove_node_nonce"
                       ng-init="remove_node_nonce='<?php echo wp_create_nonce('remove_node_nonce'); ?>'">
            </table>
            
            <?php require_once GIGA_INC_DIR . 'builder/tpl/buttons.php'; ?>

        </div><!--.wrap-->
        
        <?php
    }
}

new Builder;