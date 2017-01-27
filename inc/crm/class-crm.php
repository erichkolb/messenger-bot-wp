<?php

namespace GigaAI\CRM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GigaAI\Storage\Storage;

class CRM
{
	/**
	 * Constructor will create the menu item
	 */
	public function __construct()
	{
		add_action( 'admin_menu', [ $this, 'settings_page' ] );

		add_action( 'admin_init', [ $this, 'update_lead' ] );
	}

	/**
	 * Menu item will allow us to load the page to display the table
	 */
	public function settings_page()
	{
		add_submenu_page( 'giga', __( 'CRM', 'giga-messenger-bots' ), __( 'CRM', 'giga-messenger-bots' ), 'manage_options', 'crm', [
			$this,
			'render'
		] );
	}

	/**
	 * Display the list table page
	 *
	 * @return Void
	 */
	public function render()
	{
		$view = isset( $_GET['view'] ) ? trim( $_GET['view'] ) : '';

		if ( $view === 'lead' ) {
			Lead_Page::render();
		} else {
			$table = new \Lead_List_Table;
			$table->prepare_items();
			?>
			<div class="wrap">
				<h2><?php _e( 'Leads', 'giga' ); ?></h2>
				<?php $table->display(); ?>
			</div>
			<?php
		}
	}

	public function update_lead()
	{
		if ( empty( $_POST['_page_now'] ) || $_POST['_page_now'] !== 'crm') {
			return;
		}

		if ( ! check_admin_referer( 'giga_lead_nonce', 'giga_lead_nonce' ) )
			return;

		$lead_id = trim( $_POST['user_id'] );
        
		// Update Lead
		$lead = \GigaAI\Storage\Eloquent\Lead::where( 'user_id', $lead_id )->first();
        
		$tab = $_POST['current_tab'];
		
		if ($tab === 'basics') {
            // Sanitize and validation
            if ( ! empty($_POST['email'])) {
                if ( ! is_email($_POST['email'])) {
                    unset ($_POST['email']);
                }
            }
            
            // The Lead class from the library has built in mass update feature and validation
            // so we only need to pass $_POST
            // variable and let it do the rest.
            $lead->update($_POST);
        }
        
        if ($tab === 'advanced') {
            // Update Lead Meta. We treat all meta as text
            $meta = [];
            foreach ($_POST as $field_name => $value) {
                if (starts_with($field_name, 'meta_')) {
                    $field_name = ltrim($field_name, 'meta_');
            
                    if ( ! empty($field_name)) {
                        $meta[$field_name] = sanitize_text_field($value);
                    }
                }
            }
    
            Storage::updateLeadMeta($lead->user_id, $meta);
        }
        
        if ($tab === 'subscription')
        {
            $channels = $_POST['channels'];
            
            // Remove all empty or special characters
            $channels = array_unique(array_filter(array_map(function ($channel) {
                return sanitize_text_field($channel);
            }, $channels)));
            
            $lead->subscribe = implode(',', $channels);
            
            $lead->save();
        }
        
        \GigaAI\Session::flash([
            'status'  => 'success',
            'message' => __('Lead info saved!', 'giga-messenger-bots')
        ]);
        
        giga_refresh();
	}
}

new CRM;