<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

function mytheme_customize_register( $wp_customize ) {
	
	$wp_customize->add_setting( 'video_loop_setting', array( 'default' => false ) );
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
		  	'video_loop_control',
			array(
			    'label'    => 'Video loop',
			    'type'     => 'checkbox',
			    'section'  => 'header_image',
			    'settings' => 'video_loop_setting',
			    'priority' => 1,
			    'description' => __( 'This option is added by Yatterukun.' ),
			)
		)
	);
	
}
add_action( 'customize_register', 'mytheme_customize_register' );

