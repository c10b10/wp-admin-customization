<?php 

class AC_Settings extends scbBoxesPage {

	function setup() {
		$this->textdomain = 'admin-customization';
		
		$this->args = array(
			'page_title' => __( 'Admin Customization Settings', $this->textdomain ),
			'menu_title' => __( 'Admin Customization' , $this->textdomain ),
			'page_slug' => 'admin-customization',
		);
		
		$this->boxes = array(
			array( 'style_preferences', __( 'Style preferences', $this->textdomain ), 'normal' ),
			array( 'dashboard_settings', __( 'Dashboard widgets', $this->textdomain ), 'side' ),
			array( 'general_settings', __( 'General settings', $this->textdomain ), 'normal' ),
		);
	}
	
	function general_settings_box() {
		$output = '';
		$checkboxes = array(
			__( 'Hide update notices', $this->textdomain ) => array(
				'value' => 'hide_update_notices',
			),
			__( 'Hide plugin update count', $this->textdomain ) => array(
				'value' => 'hide_plugin_count',
			),
			__( 'Redirect to home page on logout', $this->textdomain ) => array(
				'value' => 'redirect_on_logout',
			),
		);
		foreach ( $checkboxes as $name => $args )
		{
		$output .= html( 'tr',
				html( 'th scope="row" class="check-column"',
					$this->input( array(
						'type' => 'checkbox',
						'name' => 'general[]',
						'value' => $args['value'],
						'desc' => false,
						'checked' => in_array( $args['value'], (array) $this->options->general_settings ),
					) )
				)
				.html( 'td', $name ));
				
		}
		
		echo $this->form_wrap( html( 'table class="checklist widefat notitle"', $output ), array('action' => 'general_preferences_button'));
	}
	
	function general_settings_handler() {
		if ( !isset( $_POST['general_preferences_button'] ) )
			return;
			
		$this->admin_msg( __( 'General settings saved. You may need to refresh to see the changes.', $this->textdomain ) );
		$this->options->general_settings = (array) @$_POST['general'];
	}

	function style_preferences_box() {
		$output = $this->table( array(
			array(
				'title' => __( 'Favicon', $this->textdomain ),
				'desc' => __( '(favicon path realative to wp-content)', $this->textdomain ),
				'type' => 'text',
				'name' => 'favicon',
				'value' => implode( ', ', (array) $this->options->favicon )
			),

			array(
				'title' => __( 'Login logo', $this->textdomain ),
				'desc' => __( '(login logo path relative to wp-content)', $this->textdomain ),
				'type' => 'text',
				'name' => 'login_logo',
				'value' => implode( ', ', (array) $this->options->login_logo )
			),

			array(
				'title' => __( 'Admin logo ', $this->textdomain ),
				'desc' => __( '(admin logo (max <strong>32x32px</strong>) path relative to wp-content.)<br />e.g.: "themes/mytheme/img/admin_logo.png"', $this->textdomain ),
				'type' => 'text',
				'name' => 'admin_logo',
				'value' => implode( ', ', (array) $this->options->admin_logo )
			)
		) );
		// same as $this->form_wrap( $output, '', 'style_preferences_button');
		echo $this->form_wrap( $output, array('action' => 'style_preferences_button'));
	}
	
	function style_preferences_handler() {
		if ( !isset( $_POST['style_preferences_button'] ) )
			return;
		
		$this->admin_msg( __( 'Style preferences changes saved. You may need to refresh to see the changes.', $this->textdomain ) );
		
		foreach ( array( 'favicon', 'login_logo', 'admin_logo' ) as $key )
			$this->options->$key = @$_POST[$key];
	}

	function dashboard_settings_box() {
		$output = '<p class="updated">' . __( 'To update this list of widgets you must first visit the Dashboard.', $this->textdomain ) . '<p>'.
			$this->_widget_table(__( 'Disable All Widgets', $this->textdomain ), $this->options->widgets);

		// same as $this->form_wrap( $output, '', 'dashboard_settings_button');
		echo $this->form_wrap( $output, array('action' => 'dashboard_settings_button'));
	}
	
	function dashboard_settings_handler() {
		if ( !isset ( $_POST['dashboard_settings_button'] ) )
			return;
			
		$this->admin_msg( __( 'Dashboard widgets settings saved.', $this->textdomain ) );
		$this->options->disabled_widgets = (array) @$_POST['widgets'];
	}
	
	function default_css() {
?>
<style type="text/css">
.postbox-container + .postbox-container {
	margin-left: 18px;
}
.postbox-container {
	padding-right: 0;
}
.inside {
	clear: both;
	overflow: hidden;
	padding: 10px 10px 0 !important;
}
.inside table {
	margin: 0 !important;
	padding: 0 !important;
}
.inside table td {
	vertical-align: middle !important;
}
.inside table .regular-text {
	width: 100% !important;
}
.inside .form-table th {
	width: 20%;
	max-width: 200px;
	padding: 10px 0 !important;
	font-size: 13px;
}
.inside .widefat .check-column {
	padding-bottom: 7px !important;
}
.inside p,
.inside table {
	margin: 0 0 10px !important;
}
.inside p.submit {
	float: right !important;
	padding: 0 !important;
}
.inside table.checklist {
	clear: none;
	margin-right: 1em !important;
}

.inside .checklist th input {
	margin: 0 0 0 4px !important;
}

.inside .checklist thead th {
	padding-top: 5px !important;
	padding-bottom: 5px !important;
}

.checklist thead th {
	background: none #F1F1F1 !important;
	padding: 5px 8px 8px;
	line-height: 1;
	font-size: 11px;
}

.checklist .check-column, .checklist th, .checklist td {
	padding-left: 0 !important
}
table.notitle {
	border: none !important;
}
.widefat tbody tr:last-child th, .widefat tbody tr:last-child td {
	border-bottom: 0;
}
.widefat thead th {
	border-top: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
} 
.widefat {
	border: 0;
}
p.updated {
	padding: 0.6em;
	background-color: #FFFFE0;
    border-color: #E6DB55;
    border-radius: 3px 3px 3px 3px;
    border-style: solid;
    border-width: 1px 0 1px 0;
    
}
</style>
<?php 
	}
	private function _checklist_wrap( $title, $tbody ) {
		$thead =
		html( 'tr',
			 html( 'th scope="col" class="check-column"', '<input type="checkbox" />' )
			.html( 'th scope="col"', $title )
		);
	 
		$table =
		html( 'table class="checklist widefat"',
			 html( 'thead', $thead )
			.html( 'tbody', $tbody )
		);

		return $table;
	}
	
	private function _widget_table( $title, $widgets ) {
		$tbody = '';
		foreach ( $widgets as $widget ) {
			if ( empty( $widget['title'] ) )
				continue;
			$tbody .=
			html( 'tr',
				html( 'th scope="row" class="check-column"',
					$this->input( array(
						'type' => 'checkbox',
						'name' => 'widgets[]',
						'value' => $widget['id'],
						'desc' => false,
						'checked' => in_array( $widget['id'], (array) $this->options->disabled_widgets ),
					) )
				)
				.html( 'td', $widget['title'] )
			);
		}
		return $this->_checklist_wrap( $title, $tbody );
	}	
}