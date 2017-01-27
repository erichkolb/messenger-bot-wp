<?php

namespace GigaAI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rest
{
    public $bot;

	public function __construct()
	{
        $this->bot = giga_bot_instance();

		add_action( 'rest_api_init', [ $this, 'register' ] );
	}

	public function register()
	{
		register_rest_route( 'giga-ai', '/webhook', [
			'methods'   => 'GET, POST, DELETE',
			'callback'  => [ $this, 'webhook' ],
		] );

        register_rest_route( 'giga-ai', '/subscription', [
            'methods'   => 'GET, POST',
            'callback'  => [ $this, 'subscription' ],
        ] );
	}

    /**
     * URL located at wp-json/giga-ai/webhook
     */
	public function webhook()
	{
        do_action( 'giga_pre_run', $this->bot );

		$this->bot->run();

        exit;
	}

    /**
     * URL located wp-json/giga-ai/subscription
     */
	public function subscription()
    {
        do_action( 'giga_subscription', $this->bot );

        echo 'Done!';

        exit;
    }
}

new Rest;