<?php
/*
Plugin Name: ElevateZoom
Plugin URI: http://premium.wpmudev.org
Description: Applies elevateZoom script to images (see www.elevate.co.uk/image-zoom)
Version: 1.0
Author URI: http://twitter.com/ChrisKnowles
*/

// enqueue elevateZoom jQuery plugin

function ez_enqueue_script() {
	
	wp_enqueue_script(
		'elevateZoom',
		plugins_url( '/jquery.elevateZoom.min.js' , __FILE__ ),
		array( 'jquery' ),
		null,
		true
	);
	
	wp_enqueue_script(
		'elevateZoomInit',
		plugins_url( '/elevateZoomInit.js' , __FILE__ ),
		array( 'elevateZoom' ),
		null,
		true
	);
	
	$options = get_option('ez_settings');
	
	$elevateZoomSettings = $options;
	
	wp_localize_script( 'elevateZoom', 'ezSettings' , $elevateZoomSettings );
}

add_action( 'wp_enqueue_scripts', 'ez_enqueue_script' );

// add default settings page

add_action( 'admin_menu', 'ez_add_admin_menu' );
add_action( 'admin_init', 'ez_settings_init' );


function ez_add_admin_menu(  ) { 

	add_options_page( 'elevateZoom', 'elevateZoom', 'manage_options', 'elevatezoom', 'elevatezoom_options_page' );

}


function ez_settings_exist(  ) { 

	if( false == get_option( 'elevatezoom_settings' ) ) { 

		add_option( 'elevatezoom_settings' );

	}

}


function ez_settings_init(  ) { 

	register_setting( 'pluginPage', 'ez_settings' );

	add_settings_section(
		'ez_pluginPage_section', 
		__( 'Set the defaults for elevateZoom', 'ez' ), 
		'ez_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'ez_class', 
		__( 'Activate elevateZoom for images with this class: ', 'ez' ), 
		'ez_class_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_zoomType', 
		__( 'zoomType : ', 'ez' ), 
		'ez_zoomType_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_scrollZoom', 
		__( 'scrollZoom : ', 'ez' ), 
		'ez_scrollZoom_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_lensShape', 
		__( 'lensShape : ', 'ez' ), 
		'ez_lensShape_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_lensSize', 
		__( 'lensSize : ', 'ez' ), 
		'ez_lensSize_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_zoomWindowPosition', 
		__( 'zoomWindowPosition (1 to 16): ', 'ez' ), 
		'ez_zoomWindowPosition_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_zoomWindowHeight', 
		__( 'zoomWindowHeight : ', 'ez' ), 
		'ez_zoomWindowHeight_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

	add_settings_field( 
		'ez_zoomWindowWidth', 
		__( 'zoomWindowWidth : ', 'ez' ), 
		'ez_zoomWindowWidth_render', 
		'pluginPage', 
		'ez_pluginPage_section' 
	);

}

function ez_class_render(  ) { 

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('ez_class', $options) ) $options['ez_class'] = 'elevateZoom';
	
	?>
	<input type='text' name='ez_settings[ez_class]' value='<?php echo $options['ez_class']; ?>'>
	<?php

}

function ez_zoomType_render( ) {

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('zoomType', $options) ) $options['zoomType'] = 'window';	
	
	?>
	<select name='ez_settings[zoomType]'>
		<option value='window' <?php selected( $options['zoomType'], 'window' ); ?>>Window</option>
		<option value='lens' <?php selected( $options['zoomType'], 'lens' ); ?>>Lens</option>
		<option value='inner' <?php selected( $options['zoomType'], 'inner' ); ?>>Inner</option>
	</select>

<?php

}

function ez_zoomWindowPosition_render( ) {

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('zoomWindowPosition', $options) ) $options['zoomWindowPosition'] = 1;	
	
	?>
	<input type='text' name='ez_settings[zoomWindowPosition]' value='<?php echo $options['zoomWindowPosition']; ?>'>
	<?php

}

function ez_zoomWindowHeight_render( ) {

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('zoomWindowHeight', $options) ) $options['zoomWindowHeight'] = 400;	
	
	?>
	<input type='text' name='ez_settings[zoomWindowHeight]' value='<?php echo $options['zoomWindowHeight']; ?>'>
	<?php

}

function ez_zoomWindowWidth_render( ) {

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('zoomWindowWidth', $options) ) $options['zoomWindowWidth'] = 400;	
	
	?>
	<input type='text' name='ez_settings[zoomWindowWidth]' value='<?php echo $options['zoomWindowWidth']; ?>'>
	<?php

}

function ez_scrollZoom_render(  ) { 

	$options = get_option( 'ez_settings' );

	if ( !array_key_exists('scrollZoom', $options) ) $options['scrollZoom'] = 0;	150

	?>
	<input type='checkbox' name='ez_settings[scrollZoom]' <?php checked( $options['scrollZoom'], 1 ); ?> value='1'>
	<?php

}

function ez_lensShape_render( ) {

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('lensShape', $options) ) $options['lensShape'] = 'square';	
	
	?>
	<select name='ez_settings[lensShape]'>
		<option value='square' <?php selected( $options['lensShape'], 'square' ); ?>>Square</option>
		<option value='round' <?php selected( $options['lensShape'], 'round' ); ?>>Round</option>
	</select>

<?php

}

function ez_lensSize_render(  ) { 

	$options = get_option( 'ez_settings' );
	
	if ( !array_key_exists('lensSize', $options) ) $options['lensSize'] = 200;
	
	?>
	<input type='text' name='ez_settings[lensSize]' value='<?php echo $options['lensSize']; ?>'>
	<?php

}


function ez_settings_section_callback(  ) { 

	echo __( 'Set the defaults for elevateZoom!', 'ez' );

}


function elevatezoom_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>elevateZoom</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}
?>