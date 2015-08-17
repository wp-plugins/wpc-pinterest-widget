<?php
/*
Plugin Name: WP Canvas - Pinterest Widget
Plugin URI: http://webplantmedia.com/starter-themes/wordpresscanvas/features/widgets/wordpress-canvas-widgets/
Description: Add official Pinterest widget to your site. Insert your Pinterest board widget, profile widget, and pin widget to any widget area.
Author: Chris Baldelomar
Author URI: http://webplantmedia.com/
Version: 1.1
License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPC_PINTEREST_WIDGET_VERSION', '1.1' );

function wpc_pinterest_widget_enqueue_scripts() {
	wp_deregister_script( 'pinit' );
	wp_register_script( 'pinit', '//assets.pinterest.com/js/pinit.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'wpc_pinterest_widget_enqueue_scripts');

function wpc_pinterest_widget_widgets_init() {
	register_widget('WPC_Pinterest_Widget');
}
add_action('widgets_init', 'wpc_pinterest_widget_widgets_init');

class WPC_Pinterest_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => __('Add your latest pins form Pinterest.') );
		parent::__construct( 'wpc_pinterest_widget', __('Pinterest Widget'), $widget_ops );
	}

	function widget($args, $instance) {
		wp_enqueue_script( 'pinterest' );

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		if ( ! empty( $instance['width'] ) && is_numeric( $instance['width'] ) )
			$width = ( (int) $instance['width'] - 20 );
		else
			$width = 280;

		if ( ! empty( $instance['height'] ) && is_numeric( $instance['height'] ) )
			$height = (int) $instance['height'];
		else
			$height = 400;

		if ( ! empty( $instance['url'] ) ) {
			$scale_width = round( $width / 2 ) - 2;
			$pin_do = 'embedUser';
			$parsed = parse_url( $instance['url'] );
			if ( isset( $parsed['path'] ) && ! empty ( $parsed['path'] ) ) {
				$path = trim( $parsed['path'], '/' );
				$p = explode( '/', $path );
				if ( isset( $p[0] ) && 'pin' == $p[0] ) {
					$pin_do = 'embedPin';
				}
				else if ( sizeof( $p ) > 1 ) {
					$pin_do = 'embedBoard';
				}

				echo '<a data-pin-do="'.$pin_do.'" href="'.esc_url( $instance['url'] ).'/" data-pin-scale-width="' . $scale_width . '" data-pin-scale-height="' . $height . '" data-pin-board-width="' . $width . '"></a>';
			}
		}

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['url'] = esc_url_raw($new_instance['url']);
		$instance['height'] = (int) strip_tags( stripslashes($new_instance['height']) );
		$instance['width'] = (int) strip_tags( stripslashes($new_instance['width']) );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'Latest Pins!';
		$url = isset( $instance['url'] ) ? $instance['url'] : '';
		$height = isset( $instance['height'] ) ? $instance['height'] : 400;
		$width = isset( $instance['width'] ) ? $instance['width'] : 240;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Pinterest URL:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $url; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width: (px)') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height: (px)') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" />
		</p>
		<?php
	}
}
