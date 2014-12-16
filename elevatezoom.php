<?php
/*
Plugin Name: ElevateZoom
Plugin URI: http://premium.wpmudev.org
Description: Applies elevateZoom script to images (see www.elevate.co.uk/image-zoom)
Version: 1.0
Author URI: http://twitter.com/ChrisKnowles
*/

function initialize_elevatezoom() {

	$options = get_option( 'elevatezoom_settings' );

	if( false == $options ) { 

		$options = array(
			'ezClass' => 'elevatezoom',
			'zoomType' => 'window',
			'lensShape' => 'square',
			'lensSize' => 100,
			'scrollZoom' => false,
			'zoomWindowPosition' => 1,
			'zoomWindowWidth' => 400,
			'zoomWindowHeight' => 400,
			'galleryThumbWidth' => 100,
			'galleryThumbHeight' => 100,
			'galleryThumbCrop' => 1,
			'galleryDisplayWidth' => 300,
			'galleryDisplayHeight' => 300,
			'galleryDisplayCrop' => 1,
			'galleryZoomWidth' => 600,
			'galleryZoomHeight' => 600,
			'galleryZoomCrop' => 1,
			'imageCrossfade' => true,
			'loadingIcon' => 'http://traindaze.com/assets/images/loader.gif'
		);

		add_option( 'elevatezoom_settings' , $options);

	}

	add_image_size( 'zoom-thumb', $options['galleryThumbWidth'], $options['galleryThumbHeight'], $options['galleryThumbCrop'] );
	add_image_size( 'zoom-display', $options['galleryDisplayWidth'], $options['galleryDisplayHeight'], $options['galleryDisplayCrop'] );
	add_image_size( 'zoom-zoom', $options['galleryZoomWidth'], $options['galleryZoomHeight'], $options['galleryZoomCrop'] );

	if ( is_admin() ) {
		add_action( 'admin_menu', 'elevatezoom_add_admin_menu' );
		add_action( 'admin_init', 'elevatezoom_settings_init' );

	} else {
		add_action( 'wp_enqueue_scripts', 'elevatezoom_enqueue_script' );
		add_shortcode('zoomgallery', 'zoomgallery_shortcode');
	}

}

initialize_elevateZoom();

// enqueue elevateZoom jQuery plugin
function elevatezoom_enqueue_script() {
	
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

	$options = get_option('elevatezoom_settings');
	
	$elevateZoomSettings = $options;
	
	wp_localize_script( 'elevateZoom', 'ezSettings' , $elevateZoomSettings );
}


/**
 * The Zoom Gallery shortcode.
 *
 **/
function zoomgallery_shortcode( $attr ) {

	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {

		$data_atts = '';

		// pass through elevateZoom attributes
		foreach ($attr as $key => $value) {
    			if ( substr( $value, 0, 5 ) == 'data-' ) $data_atts = $data_atts . ' ' . $value;
		}

		$_attachments = get_posts( 
			array
			( 	'include' => $attr['ids'], 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'post__in' 
			) 
		);

		$attachments = array();
		
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
		
	}
		
	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = '\n';
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, 'thumbnail', true ) . '\n';
		}
		return $output;
	}

	$selector = 'zoomgallery-' . $instance;

	$output = '<div id="'. $selector . '" class="zoomgallery" style="margin-top: 5px">';

	$i = 0;

	foreach ( $attachments as $id => $attachment ) {

		$thumb	= wp_get_attachment_image_src( $id, 'zoom-thumb' );
		$display = wp_get_attachment_image_src( $id, 'zoom-display' );
		$zoom	= wp_get_attachment_image_src( $id, 'zoom-zoom' );

		if ($i==0) $output = '<img class="zoomDisplay" data-gallery="' . $selector . '" src="' . $display[0] . '" id="zoom-' . $instance . '" data-zoom-image="' . $zoom[0] . '"' . $data_atts . '>' . $output;

		$output .= '
		<a href="#" data-image="' . $display[0] . '" data-zoom-image="' . $zoom[0] . '" title="Click to view zoomable picture" class="' . ($i==0 ? 'active' : '') . '">
			<img src="' . $thumb[0] . '" alt="gallery image">
		</a>';

		$i++;			
	}

	$output .= '</div>';

	return $output;
}

function elevatezoom_add_admin_menu(  ) { 

	add_options_page( 'elevateZoom', 'elevateZoom', 'manage_options', 'elevatezoom', 'elevatezoom_options_page' );

}


function elevatezoom_settings_init(  ) { 

	register_setting( 'general', 'elevatezoom_settings' );
	register_setting( 'gallery', 'elevatezoom_settings' );

	add_settings_section(
		'ez_general_section', 
		__( 'General settings', 'elevatezoom' ), 
		'ez_general_settings_section_callback', 
		'general'
	);

	add_settings_section(
		'ez_gallery_section', 
		__( 'Gallery image settings', 'elevatezoom' ), 
		'ez_gallery_settings_section_callback', 
		'gallery'
	);

	add_settings_field( 
		'ez_class', 
		__( 'Activate elevateZoom for images with this class: ', 'elevatezoom' ), 
		'ez_class_render', 
		'general', 
		'ez_general_section' 
	);

	add_settings_field( 
		'ez_zoomType', 
		__( 'zoomType : ', 'elevatezoom' ), 
		'ez_zoomType_render', 
		'general', 
		'ez_general_section'
	);

	add_settings_field( 
		'ez_scrollZoom', 
		__( 'scrollZoom : ', 'elevatezoom' ), 
		'ez_scrollZoom_render', 
		'general', 
		'ez_general_section'
	);

	add_settings_field( 
		'ez_lensShape', 
		__( 'lensShape : ', 'elevatezoom' ), 
		'ez_lensShape_render', 
		'general', 
		'ez_general_section'
	);

	add_settings_field( 
		'ez_lensSize', 
		__( 'lensSize : ', 'elevatezoom' ), 
		'ez_lensSize_render', 
		'general', 
		'ez_general_section' 
	);

	add_settings_field( 
		'ez_zoomWindowPosition', 
		__( 'zoomWindowPosition (1 to 16): ', 'elevatezoom' ), 
		'ez_zoomWindowPosition_render', 
		'general', 
		'ez_general_section' 
	);

	add_settings_field( 
		'ez_zoomWindowSize', 
		__( 'zoomWindowSize : ', 'elevatezoom' ), 
		'ez_zoomWindowSize_render', 
		'general', 
		'ez_general_section' 
	);

	add_settings_field( 
		'ez_galleryThumb', 
		__( 'Thumbnail : ', 'elevatezoom' ), 
		'ez_galleryThumb_render', 
		'gallery', 
		'ez_gallery_section' 
	);

	add_settings_field( 
		'ez_galleryDisplay', 
		__( 'Display : ', 'elevatezoom' ), 
		'ez_galleryDisplay_render', 
		'gallery', 
		'ez_gallery_section' 
	);

	add_settings_field( 
		'ez_galleryZoom', 
		__( 'Zoom : ', 'elevatezoom' ), 
		'ez_galleryZoom_render', 
		'gallery', 
		'ez_gallery_section'  
	);

	add_settings_field( 
		'ez_imageCrossfade', 
		__( 'imageCrossfade : ', 'elevatezoom' ), 
		'ez_imageCrossfade_render', 
		'gallery', 
		'ez_gallery_section'  
	);

	add_settings_field( 
		'ez_loadingIcon', 
		__( 'loadingIcon : ', 'elevatezoom' ), 
		'ez_loadingIcon_render', 
		'gallery', 
		'ez_gallery_section'  
	);
}

function ez_class_render(  ) { 

	$options = get_option( 'elevatezoom_settings' );

	?>
	<input type='text' name='elevatezoom_settings[ezClass]' value='<?php echo $options['ezClass']; ?>'>
	<?php

}

function ez_zoomType_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
	<select name='elevatezoom_settings[zoomType]'>
		<option value='window' <?php selected( $options['zoomType'], 'window' ); ?>>Window</option>
		<option value='lens' <?php selected( $options['zoomType'], 'lens' ); ?>>Lens</option>
		<option value='inner' <?php selected( $options['zoomType'], 'inner' ); ?>>Inner</option>
	</select>

<?php

}

function ez_zoomWindowPosition_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
	<input size="4" type='text' name='elevatezoom_settings[zoomWindowPosition]' value='<?php echo $options['zoomWindowPosition']; ?>'>
	<?php

}

function ez_zoomWindowSize_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
 	<label for="elevatezoom_settings[zoomWindowWidth]">Width:</label>
	<input size="4" type='text' name='elevatezoom_settings[zoomWindowWidth]' value='<?php echo $options['zoomWindowWidth']; ?>'>
 	<label for="elevatezoom_settings[zoomWindowHeight]">Height:</label>
	<input size="4" type='text' name='elevatezoom_settings[zoomWindowHeight]' value='<?php echo $options['zoomWindowHeight']; ?>'>
	<?php

}

function ez_galleryThumb_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
 	<label for="elevatezoom_settings[galleryThumbWidth]">Width:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryThumbWidth]' value='<?php echo $options['galleryThumbWidth']; ?>'>
 	<label for="elevatezoom_settings[galleryThumbHeight]">Height:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryThumbHeight]' value='<?php echo $options['galleryThumbHeight']; ?>'>
 	<label for="elevatezoom_settings[galleryThumbCrop]">Hard crop:</label>
	<select name='elevatezoom_settings[galleryThumbCrop]'>
		<option value='1' <?php selected( $options['galleryThumbCrop'], '1' ); ?>>Yes</option>
		<option value='0' <?php selected( $options['galleryThumbCrop'], '0' ); ?>>No</option>
	</select>
	<p>Using a hard crop is recommended as it ensures that the image will always be the set dimensions.</p>	
	<?php

}

function ez_galleryDisplay_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
 	<label for="elevatezoom_settings[galleryDisplayWidth]">Width:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryDisplayWidth]' value='<?php echo $options['galleryDisplayWidth']; ?>'>
 	<label for="elevatezoom_settings[galleryDisplayHeight]">Height:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryDisplayHeight]' value='<?php echo $options['galleryDisplayHeight']; ?>'>
 	<label for="elevatezoom_settings[galleryDisplayCrop]">Hard crop:</label>
	<select name='elevatezoom_settings[galleryDisplayCrop]'>
		<option value='1' <?php selected( $options['galleryDisplayCrop'], '1' ); ?>>Yes</option>
		<option value='0' <?php selected( $options['galleryDisplayCrop'], '0' ); ?>>No</option>
	</select>
	<p>Using a hard crop is recommended as it ensures that the image will always be the set dimensions.</p>	
	<?php

}

function ez_galleryZoom_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
 	<label for="elevatezoom_settings[galleryZoomWidth]">Width:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryZoomWidth]' value='<?php echo $options['galleryZoomWidth']; ?>'>
 	<label for="elevatezoom_settings[galleryZoomHeight]">Height:</label>
	<input size="4" type='text' name='elevatezoom_settings[galleryZoomHeight]' value='<?php echo $options['galleryZoomHeight']; ?>'>
 	<label for="elevatezoom_settings[galleryZoomCrop]">Hard crop:</label>
	<select name='elevatezoom_settings[galleryZoomCrop]'>
		<option value='1' <?php selected( $options['galleryZoomCrop'], '1' ); ?>>Yes</option>
		<option value='0' <?php selected( $options['galleryZoomCrop'], '0' ); ?>>No</option>
	</select>
	<p>Using a hard crop is recommended as it ensures that the image will always be the set dimensions.</p>	
	<?php

}


function ez_scrollZoom_render(  ) { 

	$options = get_option( 'elevatezoom_settings' );

	?>
	<select name='elevatezoom_settings[scrollZoom]'>
		<option value='1' <?php selected( $options['scrollZoom'], '1' ); ?>>Yes</option>
		<option value='0' <?php selected( $options['scrollZoom'], '0' ); ?>>No</option>
	</select>
	<?php

}

function ez_lensShape_render( ) {

	$options = get_option( 'elevatezoom_settings' );

	?>
	<select name='elevatezoom_settings[lensShape]'>
		<option value='square' <?php selected( $options['lensShape'], 'square' ); ?>>Square</option>
		<option value='round' <?php selected( $options['lensShape'], 'round' ); ?>>Round</option>
	</select>

<?php

}


function ez_lensSize_render(  ) { 

	$options = get_option( 'elevatezoom_settings' );

	?>
	<input size="4" type='text' name='elevatezoom_settings[lensSize]' value='<?php echo $options['lensSize']; ?>'>
	<?php

}


function ez_imageCrossfade_render(  ) { 

	$options = get_option( 'elevatezoom_settings' );

	?>
	<select name='elevatezoom_settings[imageCrossfade]'>
		<option value='1' <?php selected( $options['imageCrossfade'], '1' ); ?>>Yes</option>
		<option value='0' <?php selected( $options['imageCrossfade'], '0' ); ?>>No</option>
	</select>
	<?php

}


function ez_loadingIcon_render(  ) { 

	$options = get_option( 'elevatezoom_settings' );

	?>
	<img src="<?php echo $options['loadingIcon']; ?>" alt="loading icon"><br/>
	<input size="40" type='text' name='elevatezoom_settings[loadingIcon]' value='<?php echo $options['loadingIcon']; ?>'>
	<?php

}


function ez_general_settings_section_callback(  ) { 

	echo __( 'Set the defaults for using elevateZoom. Refer to the <a href="http://www.elevateweb.co.uk/image-zoom/configuration" title="Read more about the configuration settings on the elevateZoom website">elevateZoom configuration page</a> for more details.', 'elevatezoom' );

}

function ez_gallery_settings_section_callback(  ) { 

	echo __( 'Set the size and crop of the gallery images (thumbnail, display and zoom)', 'elevatezoom' );

}

function elevatezoom_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>elevateZoom</h2>
		
		<?php
		settings_fields( 'general' );
		do_settings_sections( 'general' );
		settings_fields( 'gallery' );
		do_settings_sections( 'gallery' );
		submit_button();
		?>
		
	</form>
	<?php

}
?>