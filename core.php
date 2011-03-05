<?php 
class AC_Core {
	static $options;
	
	static function init( $options ) {
		
		self::$options = $options;
		add_action('wp_dashboard_setup', array( __CLASS__, 'ac_dashboard_setup'), 99);
		add_action('admin_head', array( __CLASS__, 'ac_admin_head_setup'));
		add_action('login_head', array( __CLASS__, 'ac_login_head_setup'));
		add_action('admin_init', array( __CLASS__, 'ac_remove_update_notices'));
		add_action('admin_menu', array( __CLASS__, 'ac_remove_plugin_update_count'));
	}
	
	function ac_remove_update_notices()	{
//		global $current_user;	
//		get_currentuserinfo();
//		if ($current_user->user_login != 'admin')
		if ( in_array( 'hide_update_notices', self::$options->general_settings ) )
			remove_action('admin_notices', 'update_nag', 3);	
	}
	
	function ac_remove_plugin_update_count() {
		if ( in_array( 'hide_plugin_count', self::$options->general_settings ) )
		{
			global $menu, $submenu;
		 
		    $menu[65][0] = 'Plugins';  
		    $submenu['index.php'][10][0] = 'Updates';  
		}
	}
	
	function ac_admin_head_setup() {
		// Favicon
		if ( !empty( self::$options->favicon ) )
			echo '<link rel="shortcut icon" href="' . get_bloginfo('home') . '/wp-content/' . self::$options->favicon . '" />';
		
		// Backend logo
		if ( !empty( self::$options->admin_logo ) )
		echo '<style type="text/css">
		#header-logo {
			display: none !important;
		}
        #site-title {
        	background:url(' . get_bloginfo('home') . '/wp-content/' . self::$options->admin_logo . ') left center no-repeat !important;
          	padding-left:35px;
        }
       </style>';
	}
	
	function ac_login_head_setup() {
		if ( !empty( self::$options->login_logo ) )
		  echo '<style type="text/css">
          h1 a
          {
          	background:url(' . get_bloginfo('home') . '/wp-content/' . self::$options->login_logo . ') center top no-repeat !important;
          	height: 133px;
          	width: 309px;
          }
        </style>';
	}
			
	function ac_dashboard_setup() {
	 	global $wp_meta_boxes;
		self::$options->widgets = self::_get_unset_dashboard_widgets(self::$options->disabled_widgets);
	}
	
	private function _get_unset_dashboard_widgets($disabled_widgets = array()) {
		global $wp_meta_boxes;
		
		if ( isset($wp_meta_boxes['dashboard']) ) {
			foreach ( $wp_meta_boxes['dashboard'] as $context => $data ) {
				foreach ( $data as $priority=>$data ) {
					foreach( $data as $widget=>$data ) {
						$widgets[$widget] = array('id' => $widget,
										   'title' => strip_tags( preg_replace('/( |)<span.*span>/im', '', $data['title']) ),
										   'context' => $context,
										   'priority' => $priority
										   );
						// unset the required widgets
						if ( in_array( $widget, $disabled_widgets ) ) 
							unset($wp_meta_boxes['dashboard'][$context][$priority][$widget]);
					}
				}
			}
		}
		return $widgets;
	}
}