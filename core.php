<?php 
class AC_Core {
	static $options;
	
	static function init( $options ) {
		
		self::$options = $options;
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'ac_dashboard_setup' ), 99);
		add_action( 'admin_head', array( __CLASS__, 'ac_admin_head_setup' ) );
		add_action( 'admin_init', array( __CLASS__, 'ac_remove_update_notices' ) );
		add_action( 'admin_menu', array( __CLASS__, 'ac_remove_plugin_update_count' ) );
		add_filter( 'admin_user_info_links', array( __CLASS__, 'ac_redirect_on_logout' ) );
		add_action( 'login_head', array( __CLASS__, 'ac_login_head_setup' ) );
		add_filter( 'login_headerurl', array( __CLASS__, 'ac_login_url' ) );
		add_filter( 'login_headertitle', array( __CLASS__, 'ac_login_title' ) );
	}
	
	function ac_remove_update_notices()	{
//		global $current_user;	
//		get_currentuserinfo();
//		if ($current_user->user_login != 'admin')
		if ( in_array( 'hide_update_notices', (array) self::$options->general_settings ) )
			remove_action('admin_notices', 'update_nag', 3);	
	}
	
	function ac_remove_plugin_update_count() {
		if ( in_array( 'hide_plugin_count', (array) self::$options->general_settings ) )
		{
			global $menu, $submenu;
		 
		    $menu[65][0] = 'Plugins';  
		    $submenu['index.php'][10][0] = 'Updates';  
		}
	}
	
	function ac_redirect_on_logout($links) {
		if ( in_array( 'redirect_on_logout', (array) self::$options->general_settings ) ) {
			$links[15] = '| <a href="' . wp_logout_url( home_url() ) . '" title="Log Out">Log Out</a>';
		}
			return $links;
	}
	
	
	function ac_admin_head_setup() {
		// Favicon
		if ( !empty( self::$options->favicon ) )
			echo '<link rel="shortcut icon" href="' . get_bloginfo('home') . '/wp-content/' . self::$options->favicon . '" />';
		
		$site_title_styles = array();
		
		// Backend logo
		if ( in_array( 'hide_logo_name', (array) self::$options->style_settings ) )
		{
			array_push( $site_title_styles, 'text-indent: -9999em;' );
		}
		
		if ( !empty( self::$options->admin_logo ) )	{
			// Get logo information
			$logo_path = get_bloginfo( 'home' ) . '/wp-content/' . self::$options->admin_logo;
		 	$logo_size = getimagesize( $logo_path );
			// If the logo fits in the default bar, don't modify its margins
			$margins = ( $logo_size[1] + $margins[0] + $margins[2] < 46 ) ? array( 10, 8, 5, 15 ) : array( 8, 0, 8, 15 );
			// Calculate the new logo width
			$logo_width = $logo_size[0] + ( ( $margins[0] + $margins[2] ) / 2 );
			// Calculate the new padding needed to accomodate the height (26 is the default logo text height)
			$vertical_padding = $logo_size[1] - 26 > 0 ? round( ( $logo_size[1] - 26 ) / 2 ) : 0; 
			// Calculate the header height
			$adjusted_head_height = ( $logo_size[1] + $margins[0] + $margins[2] < 46 ) ? 46 : $logo_size[1] + $margins[0] + $margins[2];
			array_push( 
				$site_title_styles, 
				'background:url(' . get_bloginfo('home') . '/wp-content/' . self::$options->admin_logo . ') left center no-repeat !important;
				float: left;
          		padding:' . $vertical_padding . 'px 0 ' . $vertical_padding . 'px ' . $logo_width . 'px;'
			);

			$custom_logo_styles ='
				#header-logo {
				display: none !important;
			}
			#wphead {
				height: ' . $adjusted_head_height . 'px;
			}
			#wphead h1 {
				margin: ' . $margins[0] . 'px ' . $margins[1] . 'px ' . $margins[2] . 'px ' . $margins[3] . 'px;
				padding: 0;
			}
			#user_info, #user_info p {
				line-height: ' . $adjusted_head_height . 'px;
			}
			#favorite-actions {
				margin-top: ' . floor ( ( $adjusted_head_height - 22 ) / 2 ) . 'px;
			}
			#wphead #privacy-on-link {
					line-height: ' . ( $logo_size[1] + 7 ) . 'px;
			}';
			
			
		}

		if ( !empty( $site_title_styles ) )
		{
			if ( empty( $custom_logo_styles ) ) {
				array_push( $site_title_styles, 'float: left;' );
			}
			
			echo '<style type="text/css">
				#site-title {';
			foreach ( $site_title_styles as $rule ) {
				echo $rule;
			}
			echo '}' . $custom_logo_styles; 
			
		
			
			echo '</style>';
		}
	}
	
	function ac_login_head_setup() {
		if ( !empty( self::$options->login_logo ) ) {
		  $logo_path = get_bloginfo( 'home' ) . '/wp-content/' . self::$options->login_logo;
		  $logo_size = getimagesize( $logo_path );
		  echo '<style type="text/css">
          h1 a
          {
          	background:url(' . $logo_path . ') center top no-repeat !important;
          	height: '. $logo_size[1] . 'px;
          }
        </style>';
		}
	}

	function ac_login_url() {
		echo bloginfo( 'url' );
	}
	
	function ac_login_title() {
		echo get_option( 'blogname' );
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