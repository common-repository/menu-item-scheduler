<?php
/*
Plugin Name: Menu Item - Scheduler
Plugin URL: http://reloadweb.co.uk
Description: Redirect or Hide/show specific menu item for scheduled period.
Version: 1.0.0
Author: Reload Web (Ahmed)
Author URI: http://reloadweb.co.uk
Text Domain: menu-item-scheduler
Domain Path: /languages
*/
global $rwmis_ids;
define( 'RW_MenuItem_TD', 'menu-item-scheduler' );
class rw_menuitem_scheduler {
	
	/* Initializes the plugin by setting localization, filters, and administration functions. */
	public function __construct() {
		/*--- Constructor ---*/
				
		add_action( 'init', array($this,'rwmis_textdomain') );
		
		/* add fields to menu */
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'rwmis_add_navitem_fields' ), 10, 1 );
		
		/* save custom fields to menu */
		add_action( 'wp_update_nav_menu_item', array( $this, 'rwmis_update_navitem_fields'), 10, 3 );
			
		/* script to add datetimepicker for datetime fields to menu */
		add_action( 'admin_enqueue_scripts', array($this,'rwmis_datetimepicker_enqueue_script') );
		add_action( 'admin_print_footer_scripts', array($this, 'rwmis_datetimepicker_print_script'), 30 );
		
		/* edit menu walker */
		add_filter( 'wp_edit_nav_menu_walker', array($this,'rwmis_edit_navwalker'), 10, 2 );
		
		if( !is_admin() ){
			/* Display filtered menu */
			add_action( 'wp_get_nav_menu_items', array( $this, 'rwmis_filter_navitems'), null, 3 );
		}
		

	} /* end constructor */
	
		
	/**
	 * Load the plugin's text domain
	 */
	function rwmis_textdomain() {
		load_plugin_textdomain( RW_MenuItem_TD, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
		
	/**
	 * Add datetimepicker in Plugin
	 */		
	function rwmis_datetimepicker_enqueue_script(){
		$plugin_data = get_plugin_data( __FILE__ );
		$version = $plugin_data['Version'];	
		wp_enqueue_script('jquery');	
		wp_enqueue_script( 'jquery-datetimepicker-full', plugins_url( '/assets/jquery.datetimepicker.full.min.js', __FILE__ ), array( 'jquery' ), $version, true );
		wp_enqueue_style( 'jquery-datetimepicker', plugins_url( '/assets/jquery.datetimepicker.min.css', __FILE__ ), array(), $version, 'all' );
	}
	
	/**
	 * Add datetimepicker in menu item date fields
	 */
	function rwmis_datetimepicker_print_script(){	
		?>
<style type="text/css">.xdsoft_datetimepicker{ border-color:#007cba; }</style>
		<?php 	
		 rwmis_add_item_script(); 
	}
		
	/**
	 * Add Custom fields for menu items
	*/
	function rwmis_add_navitem_fields( $menu_item ){
			
		/* Redirect Menu Item */
		$menu_item->rwmis_redirect = get_post_meta( $menu_item->ID, '_menu_item_rwmis_redirect', true );
		$menu_item->rwmis_redirect_label = get_post_meta( $menu_item->ID, '_menu_item_rwmis_redirect_label', true );
		$menu_item->rwmis_redirect_url = get_post_meta( $menu_item->ID, '_menu_item_rwmis_redirect_url', true );
		$menu_item->rwmis_redirect_start = get_post_meta( $menu_item->ID, '_menu_item_rwmis_redirect_start', true );
		$menu_item->rwmis_redirect_end = get_post_meta( $menu_item->ID, '_menu_item_rwmis_redirect_end', true );
		
		/* Hide Menu Item */
		$menu_item->rwmis_hide = get_post_meta( $menu_item->ID, '_menu_item_rwmis_hide', true );
		$menu_item->rwmis_hide_child = get_post_meta( $menu_item->ID, '_menu_item_rwmis_hide_child', true );
		$menu_item->rwmis_hide_start = get_post_meta( $menu_item->ID, '_menu_item_rwmis_hide_start', true );
		$menu_item->rwmis_hide_end = get_post_meta( $menu_item->ID, '_menu_item_rwmis_hide_end', true );
		
		return $menu_item;
		
	}
		
	/**
	 * Update Custom fields for menu items
	*/
	function rwmis_update_navitem_fields( $menu_id, $menu_item_db_id, $args ){
		
		/* Redirect Menu Item */	
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_redirect' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_redirect_label' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_redirect_url' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_redirect_start' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_redirect_end' );
		
		/* Hide Menu Item */
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_hide' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_hide_child' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_hide_start' );
		rwpro_mis_update_field( $menu_item_db_id,'rwmis_hide_end' );
		
	}
		
	/**
	 * Define new Walker edit
	*/
	function rwmis_edit_navwalker($walker,$menu_id) {
		$menuitem_ary = wp_get_nav_menu_items($menu_id);
		$ids=array();		
		foreach ( $menuitem_ary as $key => $item ) { 
			 $ids[] = $item->ID; 
		} 		
		global $rwmis_ids;
		$rwmis_ids = $ids;
		
		/* Include our custom jQuery file with WordPress Date Time Picker dependency	*/		
		return 'Walker_Nav_Menu_Edit_RWmis';
	}
		
	/**
	 * Filter Menu for front-end display
	*/
	function rwmis_filter_navitems( $items, $menu, $args ){
	
		/* Menu items set to hide */	
		$show_items = rwmis_changeitem_hide( $items );
			
		/* Menu items set to redirect */
		$redirect_items = rwmis_changeitem_redirect($show_items);
			
		$filtered_items = $redirect_items;	
		return $filtered_items;
	}
	
}
$GLOBALS['rwmis'] = new rw_menuitem_scheduler();

include_once( 'edit-walker-nav-menu.php' );
include_once( 'rwmis.php' );

if ( ! function_exists( 'mis_fs' ) ) {
    // Create a helper function for easy SDK access.
    function mis_fs() {
        global $mis_fs;

        if ( ! isset( $mis_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $mis_fs = fs_dynamic_init( array(
                'id'                  => '5648',
                'slug'                => 'menu-item-scheduler',
                'type'                => 'plugin',
                'public_key'          => 'pk_ae2bc27db6b08374158fde1f884b3',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'first-path'     => 'plugins.php',
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $mis_fs;
    }

    // Init Freemius.
    mis_fs();
    // Signal that SDK was initiated.
    do_action( 'mis_fs_loaded' );
}
