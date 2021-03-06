<?php

/**
 * Theme My Login Action Class
 *
 * @package Theme_My_Login
 * @subpackage Actions
 */

/**
 * Class used to implement the action object.
 *
 * @since 7.0
 */
class Theme_My_Login_Action {

	/**
	 * The action name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The action title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The action slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The action handler.
	 *
	 * @var callable
	 */
	protected $handler;

	/**
	 * Whether this action is a network action or not.
	 *
	 * @var bool
	 */
	public $network = false;

	/**
	 * Whether a link to the action should be shown on forms or not.
	 *
	 * @var bool
	 */
	public $show_on_forms = true;

	/**
	 * Whether this action should be selectable in the widget or not.
	 *
	 * @var bool
	 */
	public $show_in_widget = true;

	/**
	 * Whether this action should be shown for use in nav menus or not.
	 *
	 * @var bool
	 */
	public $show_in_nav_menus = true;

	/**
	 * Whether to show the nav menu item or not when this action is assigned to a nav menu.
	 *
	 * @var bool
	 */
	public $show_nav_menu_item;

	/**
	 * Construct the instance.
	 *
	 * @since 7.0
	 *
	 * @param string $name The action name.
	 * @param array  $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type string      $title              The action title.
	 *     @type string      $slug               The action slug.
	 *     @type callable    $handler            The action callback to fire when accessed.
	 *     @type bool|string $show_on_forms      Whether a link to the action should be shown on forms or not.
	 *     @type bool        $show_in_widget     Whether this action should be selectable in the widget or not.
	 *     @type bool        $show_in_nav_menus  Whether this action should be available for use in nav menus or not.
	 *     @type bool        $show_nav_menu_item Whether to show an assigned nav menu item or not.
	 * }
	 */
	public function __construct( $name, $args = array() ) {

		$this->set_name( $name );

		$args = wp_parse_args( $args, array(
			'title'             => '',
			'slug'              => '',
			'handler'           => '',
			'network'           => false,
			'show_on_forms'     => true,
			'show_in_widget'    => true,
			'show_in_nav_menus' => true,
		) );

		if ( ! isset( $args['show_nav_menu_item'] ) ) {
			$args['show_nav_menu_item'] = $args['show_in_nav_menus'];
		}

		$this->set_title( $args['title'] );
		$this->set_slug( $args['slug'] );
		$this->set_handler( $args['handler'] );

		$this->network            = (bool) $args['network'];
		$this->show_on_forms      = $args['show_on_forms'];
		$this->show_in_widget     = (bool) $args['show_in_widget'];
		$this->show_in_nav_menus  = (bool) $args['show_in_nav_menus'];
		$this->show_nav_menu_item = (bool) $args['show_nav_menu_item'];
	}

	/**
	 * Get the action name.
	 *
	 * @since 7.0
	 *
	 * @return string The action name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the action name.
	 *
	 * @since 7.0
	 *
	 * @param string $name The action name.
	 */
	protected function set_name( $name ) {
		$this->name = sanitize_key( $name );
	}

	/**
	 * Get the action title.
	 *
	 * @since 7.0
	 *
	 * @return string The action title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set the action title.
	 *
	 * @since 7.0
	 *
	 * @param string $title The action title.
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * Get the action slug.
	 *
	 * @since 7.0
	 *
	 * @return string The action slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Set the action slug.
	 *
	 * @since 7.0
	 *
	 * @param string $slug The action slug.
	 */
	public function set_slug( $slug ) {
		if ( empty( $slug ) ) {
			$slug = $this->get_name();
		}
		$this->slug = $slug;
	}

	/**
	 * Get the action URL.
	 *
	 * @since 7.0
	 *
	 * @param string $scheme  The URL scheme.
	 * @param bool   $network Whether to retrieve the URL for the current network or current blog.
	 * @return string The action URL.
	 */
	public function get_url( $scheme = 'login', $network = null ) {
		if ( null === $network ) {
			$network = $this->network;
		}

		$function = $network ? 'network_home_url' : 'home_url';

		if ( tml_use_permalinks() ) {
			$path = user_trailingslashit( $this->get_slug() );
			$url  = $function( $path, $scheme );
		} else {
			$url = $function( '', $scheme );
			$url = user_trailingslashit( $url );
			$url = add_query_arg( 'action', $this->name, $url );
		}
		return $url;
	}

	/**
	 * Set the action handler.
	 *
	 * @since 7.0
	 *
	 * @param callable $handler The action handler.
	 */
	public function set_handler( $handler ) {
		if ( is_callable( $handler ) ) {
			$this->handler = $handler;
		}
	}

	/**
	 * Handle the action.
	 *
	 * @since 7.0
	 */
	public function handle() {

		if ( ! is_callable( $this->handler ) ) {
			return;
		}

		call_user_func( $this->handler );
	}
}
