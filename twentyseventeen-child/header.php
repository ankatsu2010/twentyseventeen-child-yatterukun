<?php
/*
 * Theme Name:twentyseventeen-child
 * Template:twentyseventeen
 * Version:2.2
 */

function get_header_image_tag_override(){

	$header      = get_custom_header();
    $header->url = get_header_image();

    if ( ! $header->url ) {
            return '';
    }

    $width  = absint( $header->width );
    $height = absint( $header->height );

    $attr =  array(
                'src'    => $header->url,
                'width'  => $width,
                'height' => $height,
                'alt'    => get_bloginfo( 'name' ),
            );

    // Generate 'srcset' and 'sizes' if not already present.
	if ( empty( $attr['srcset'] ) && ! empty( $header->attachment_id ) ) {
            $image_meta = get_post_meta( $header->attachment_id, '_wp_attachment_metadata', true );
            $size_array = array( $width, $height );

            if ( is_array( $image_meta ) ) {
                    $srcset = wp_calculate_image_srcset( $size_array, $header->url, $image_meta, $header->attachment_id );
                    $sizes  = ! empty( $attr['sizes'] ) ? $attr['sizes'] : wp_calculate_image_sizes( $size_array, $header->url, $image_meta, $header->attachment_id );

                    if ( $srcset && $sizes ) {
                            $attr['srcset'] = $srcset;
                            $attr['sizes']  = $sizes;
                    }
            }
    }

    $attr = array_map( 'esc_attr', $attr );
    $html = '<img';

    foreach ( $attr as $name => $value ) {
            $html .= ' ' . $name . '="' . $value . '"';
    }

    $html .= ' />';
    
    /*
     *
     */
    $buster = '?x=' . rand();
    $pattern = '/\/yatterukun\/(yatterukun.*?\.)(jpg|mp4)/';
    $replacement = '/yatterukun/$1$2' . $buster;
    	
    $html = preg_replace($pattern, $replacement, $html);

    /**
     * Filters the markup of header images.
     *
     * @since 4.4.0
     *
     * @param string $html   The HTML image tag markup being filtered.
     * @param object $header The custom header object returned by 'get_custom_header()'.
     * @param array  $attr   Array of the attributes for the image tag.
     */
    return apply_filters( 'get_header_image_tag', $html, $header, $attr );
	
}

function get_header_video_settings_override() {
    $header     = get_custom_header();
    $video_url  = get_header_video_url();
    $video_type = wp_check_filetype( $video_url, wp_get_mime_types() );
    
     /*
     *
     */
    $buster = '?x=' . rand();
 	//$videoLoop = 0;    // 0:none 1:loop
 	$videoLoop = get_theme_mod( 'video_loop_setting' ); //false:none (default) true:loop
 	
    $settings = array(
        'mimeType'  => '',
        'posterUrl' => get_header_image(),
        'videoUrl'  => $video_url . $buster,
        'width'     => absint( $header->width ),
        'height'    => absint( $header->height ),
        'minWidth'  => 900,
        'minHeight' => 500,
        'l10n'      => array(
            'pause'      => __( 'Pause' ),
            'play'       => __( 'Play' ),
            'pauseSpeak' => __( 'Video is paused.' ),
            'playSpeak'  => __( 'Video is playing.' ),
        ),
        'loop'     => $videoLoop,
    );
 
    if ( preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url ) ) {
        $settings['mimeType'] = 'video/x-youtube';
    } elseif ( ! empty( $video_type['type'] ) ) {
        $settings['mimeType'] = $video_type['type'];
    }
 
    /**
     * Filters header video settings.
     *
     * @since 4.7.0
     *
     * @param array $settings An array of header video settings.
     */
    return apply_filters( 'header_video_settings', $settings );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<div class="custom-header">

		<div class="custom-header-media">
			<?php
				
				if ( ! has_custom_header() && ! is_customize_preview() ) {
					//nothing
				}
				else{
					echo sprintf('<div id="wp-custom-header" class="wp-custom-header">%s</div>', get_header_image_tag_override() );
					
					if ( is_header_video_active() && ( has_header_video() || is_customize_preview() ) ) {
						/*
				         *wp_enqueue_script( 'wp-custom-header' );
				         *wp_localize_script( 'wp-custom-header', '_wpCustomHeaderSettings', get_header_video_settings() );
				         */
				        
				        wp_enqueue_script( 'wp-custom-header-override', get_stylesheet_directory_uri() . '/js/wp-custom-header-override.js' );
				        wp_localize_script( 'wp-custom-header-override', '_wpCustomHeaderSettings', get_header_video_settings_override() );
				        
				    }
					
				}
				
			?>
		</div>

	<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>

</div><!-- .custom-header -->

		<?php if ( has_nav_menu( 'top' ) ) : ?>
			<div class="navigation-top">
				<div class="wrap">
					<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .wrap -->
			</div><!-- .navigation-top -->
		<?php endif; ?>

	</header><!-- #masthead -->

	<?php

	/*
	 * If a regular post or page, and not the front page, show the featured image.
	 * Using get_queried_object_id() here since the $post global may not be set before a call to the_post().
	 */
	if ( ( is_single() || ( is_page() && ! twentyseventeen_is_frontpage() ) ) && has_post_thumbnail( get_queried_object_id() ) ) :
		echo '<div class="single-featured-image-header">';
		echo get_the_post_thumbnail( get_queried_object_id(), 'twentyseventeen-featured-image' );
		echo '</div><!-- .single-featured-image-header -->';
	endif;
	?>

	<div class="site-content-contain">
		<div id="content" class="site-content">


