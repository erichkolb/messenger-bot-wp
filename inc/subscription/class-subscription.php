<?php

namespace GigaAI;

use GigaAI\Storage\Eloquent\Message;
use GigaAI\Storage\Eloquent\Lead;

class Notification
{
    /**
     * Message in current page
     *
     * @var array
     */
    private $messages = [];
    
    /**
     * Current Message
     *
     * @var object
     */
    private $message = [];
    
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
     * Current tag filter
     *
     * @var string
     */
    private $channel = '';
    
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
        add_action('wp_ajax_remove_message', [$this, 'ajax_remove_message']);
        
        add_action('wp_ajax_update_message', [$this, 'ajax_update_message']);
        
        add_action('wp_ajax_search_leads', [$this, 'ajax_search_leads']);
        
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
        if (isset($_GET['page']) && $_GET['page'] === 'notifications') {
            wp_localize_script('giga-messenger-bots', 'messages', $this->messages);
            wp_localize_script('giga-messenger-bots', 'answers', $this->message->content);
        }
    }
    
    /**
     * Register Settings Page with menu under Giga AI
     */
    public function settings_page()
    {
        add_submenu_page('giga',
            __('Notifications', 'giga-messenger-bots'),
            __('Notifications', 'giga-messenger-bots'),
            'manage_options',
            'notifications',
            [$this, 'render']
        );
    }
    
    public function admin_init()
    {
        if ( ! isset($_REQUEST['page']) || $_REQUEST['page'] !== 'notifications') {
            return;
        }
        
        // Process posted data.
        if (isset($_POST['page']) && $_POST['page'] == 'notifications') {
            $this->create_or_update_message();
        }
        
        if (isset($_GET['page']) && $_GET['page'] == 'notifications') {
            
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
                $this->delete_message();
            }
            
            $bot = giga_bot_instance();
            
            do_action('giga_pre_seed', $bot);
            
            $this->load_messages();
        }
    }
    
    public function create_or_update_message()
    {
        // Validation
        if (empty($_POST['to_channel'])) {
            Session::flash([
                'status'  => 'error',
                'message' => __('Please select a subscription channel', 'giga-messenger-bots'),
            ]);
            
            giga_refresh();
        }
        
        // For the datetime-local, it has format yyyy-mm-ddThh:ii:ss, we'll remove T from it.
        $_POST['start_at'] = date('Y-m-d H:i:s', strtotime($_POST['start_at']));
        $_POST['end_at'] = date('Y-m-d H:i:s', strtotime($_POST['end_at']));
        $_POST['to_channel'] = implode(',', $_POST['to_channel']);
        $_POST['content'] = json_decode(stripslashes($_POST['content']), true);
        
        if (isset($_POST['content'][0]['text']) && $_POST['content'][0]['text'] == '') {
            Session::flash([
                'status'  => 'error',
                'message' => __('Please enter subscription messages', 'giga-messenger-bots'),
            ]);
            
            giga_refresh();
        }
        
        if (strtotime($_POST['start_at']) == 0) {
            $_POST['start_at'] = date('Y-m-d H:i:s');
        }
        
        if ( ! empty($_POST['id'])) {
            $message = Message::findOrFail($_POST['id']);
            
            // Also update cronjob
            Routine::update($message);
            
            $message->update($_POST);
        } else {
            $message = Message::create($_POST);
            
            // Create cron job after message created.
            Routine::create($message);
        }
        
        if (isset($_POST['send_message'])) {
            do_action_ref_array('giga_notification_routines', [
                'id' => $message->id,
            ]);
        }
        
        Session::flash([
            'status'  => 'success',
            'message' => __('Your notification was saved successfully!', 'giga-messenger-bots'),
        ]);
        
        // Redirect back
        giga_refresh([
            'id' => $message->id,
        ]);
    }
    
    public function delete_message()
    {
        $nonce = isset($_GET['_giga_nonce']) ? $_GET['_giga_nonce'] : '';
        
        if ( ! wp_verify_nonce($nonce, 'delete_message')) {
            die(__('Hacked, huh?', 'giga-messenger-bots'));
        }
        
        $id = intval($_GET['id']);
        
        $message = Message::find($id);
        Routine::delete($message);
        
        if ($message->delete()) {
            Session::flash([
                'status'  => 'success',
                'message' => __('Your notification was deleted successfully', 'giga-messenger-bots'),
            ]);
            
            giga_refresh(null, [
                'id',
            ]);
        }
        
        Session::flash([
            'status'  => 'error',
            'message' => __('Error during deleting your notification', 'giga-messenger-bots'),
        ]);
        
        giga_refresh();
    }
    
    /**
     * Load Nodes from DB
     */
    public function load_messages()
    {
        $this->paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? intval($_GET['paged']) : 1;
        
        $per_page = 20;
        $offset = ($this->paged - 1) * $per_page;
        
        $this->channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';
        
        $this->total = Message::count();
        
        $this->messages = Message::orderBy('created_at', 'DESC')->skip($offset)->take($per_page)
            ->get();
        
        // Default content for message
        $this->message = giga_default_message();
        
        if ( ! empty($_GET['id'])) {
            $this->message = Message::find($_GET['id']);
        }
        
        $this->pages = ceil($this->total / $per_page);
    }
    
    /**
     * AJAX search leads
     * Return array of leads.
     */
    public function ajax_search_leads()
    {
        if (empty($_GET['name'])) {
            wp_send_json_error('Not Found');
        }
        
        $name = trim($_GET['name']);
        $name = "%{$name}%";
        
        $leads = Lead::where('first_name', 'LIKE', $name)->orWhere('last_name', 'LIKE', $name)->take(20)->get()->toArray();
        
        if ( ! empty($leads)) {
            wp_send_json_success($leads);
        }
        
        wp_send_json_error('Not Found');
    }
    
    /**
     * Render Settings Page Content
     *
     * @return void
     */
    public function render()
    {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                $('#start-at').mask('9999/99/99 99:99:99', { placeholder:"yyyy/mm/dd hh:ii:ss" });
                $('#end-at').mask('9999/99/99 99:99:99', { placeholder:"yyyy/mm/dd hh:ii:ss" });
            });
        </script>
        
        <div class="wrap" id="notification-builder" ng-app="Bot" ng-controller="BotController"
             ng-init="initMessageBuilder();">

            <h2>
                <?php
                if ( ! isset($_GET['id'])) {
                    _e('New Notification', 'giga-messenger-bots');
                } else {
                    _e('Update Notification', 'giga-messenger-bots');
                }
                ?>

                <a href="<?php echo admin_url('admin.php?page=notifications'); ?>" class="page-title-action">
                    <?php _e('Add New', 'giga-messenger-bots'); ?>
                </a>
            </h2>
            
            <?php giga_load_component('admin-message'); ?>

            <table id="bot-builder-form">
                <tr>
                    <td id="builder-gui">
                        <form action="options.php" method="post">
                            
                            <?php require_once GIGA_INC_DIR . 'subscription/tpl/message-builder.php'; ?>

                            <input type="hidden" name="content" value="{{node.answers}}">
                            <input type="hidden" name="page" value="notifications">
                            <input type="hidden" name="id" value="<?php echo $this->message->id; ?>">
                        </form>
                    </td><!--#builder-gui-->

                    <td class="conversation" id="conversation">
                        <?php require_once GIGA_INC_DIR . 'builder/tpl/data-table.php'; ?>

                        <h3><?php _e('Live Preview', 'giga-messenger-bots'); ?></h3>
                        <dl ng-include src="'/data-table.html'"></dl>
                    </td><!--.conversation-->

                    <td id="message-list">
                        <h3><?php _e('Notifications', 'giga-messenger-bots'); ?></h3>

                        <table class="wp-list-table widefat fixed striped tags">
                            <thead>
                            <tr>
                                <th scope="col" id="to-channel"
                                    class="manage-column column-to-channel"><?php _e('To Channels', 'giga-messenger-bots'); ?></th>
                                <th scope="col" id="name"
                                    class="manage-column column-name"><?php _e('Description', 'giga-messenger-bots'); ?></th>
                                <th scope="col" id="leads-count"
                                    class="manage-column column-subscribers"><?php _e('Subscribers', 'giga-messenger-bots'); ?></th>
                                <th scope="col" id="leads-count"
                                    class="manage-column column-sent-count"><?php _e('Sent Count', 'giga-messenger-bots'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($this->messages)) : ?>
                                <tr>
                                    <td class="getting-started" colspan="4">
                                        <p><?php _e('No Notification Found.', 'giga-messenger-bots'); ?></p>
                                    </td>
                                </tr>
                            <?php else :
                                foreach ($this->messages as $message) : ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo add_query_arg('id', $message->id); ?>"><?php echo $message->to_channel; ?></a>
                                        </td>
                                        <td>
                                            <a href="<?php echo add_query_arg('id', $message->id); ?>"><?php echo $message->description; ?></a>
                                        </td>
                                        <td><?php echo $message->leadsCount(); ?></td>
                                        <td><?php echo $message->sent_count; ?></td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>


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
            </table>
            <?php require_once GIGA_INC_DIR . 'builder/tpl/buttons.php'; ?>
        </div><!--.wrap-->
        
        <?php
    }
}

new Notification;