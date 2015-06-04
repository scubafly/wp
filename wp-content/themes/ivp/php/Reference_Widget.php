<?php
class Reference_Widget extends WP_Widget {
    function __construct() {
		$widget_ops = array('classname' => 'reference_widget', 'description' => __('Reference Widget'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('reference_widget', __('Reference Widget'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'ref_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $subtitle = apply_filters( 'ref_widget_subtitle', empty($instance['subtitle']) ? '' : $instance['subtitle'], $instance, $this->id_base);
		$link = apply_filters( 'ref_widget_link', empty($instance['link']) ? '' : $instance['link'], $instance, $this->id_base);
		echo $before_widget;
		?>
			<div class="actie_blokje">
                <a href="<?php echo $link; ?>" title="<?php echo $title.' '.$subtitle; ?>">
                    <div class="spacing_actie">
                        <div class="titel_actie"><?php echo $title; ?></div>
                        <p><?php echo $subtitle; ?></p>
                    </div>
                </a>
                <ul>
                    <li><a href="<?php echo $link; ?>" title="<?php echo $title.' '.$subtitle; ?>">klik voor meer info</a></li>
                </ul>
            </div>

		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
        $instance['subtitle'] = strip_tags($new_instance['subtitle']);
        $instance['link'] = strip_tags($new_instance['link']);
		return $instance;
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'subtitle' => '', 'link' => '' ) );
		$title = strip_tags($instance['title']);
        $subtitle = strip_tags($instance['subtitle']);
        $link = strip_tags($instance['link']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Subtitle:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Link:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></p>

<?php
	}
}
