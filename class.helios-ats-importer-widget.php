<?php

class Helios_Widget extends WP_Widget {

	function Helios_Widget() {
		$widget_ops = array( 'classname' => 'helios', 'description' => __('A widget that displays the helios job ', 'helios_widget_domain') );
		
		//$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );
		
		$this->WP_Widget( 'helios-widget', __('Helios', 'helios_widget_domain'), $widget_ops/*,$control_ops*/ );
	}
	
	public function widget( $args, $instance ) {
		global $wpdb, $post;
		
		$permalink = get_permalink(get_option('helios_joblist_page_id'));
		if(isset($_GET['page_id'])){
			$targetpage = $permalink.'&job_id=';
		} else {
			$targetpage = $permalink.'?job_id=';
		}
		$html = '';
		if ( isset( $instance[ 'number' ] ) ) {
			$limit = $instance[ 'number' ];
		} else {
			$limit = 1;
		}
		$result = $wpdb->get_results("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE 1= 1 and publishedStatus = 'Y' order by id DESC limit $limit", OBJECT);
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ){
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if(count($result) > 0 ){
		$html .= '<ul class="'.$class.'">';
		foreach($result as $entry){
			$html .= '<li><a href="'.$targetpage.$entry->id.'">'.$entry->title."</a></li>";
		}
		$html .= "</ul>";
		echo $html;
		} else {
			echo 'No record found';
		}
		echo $args['after_widget'];
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = intval( strip_tags($new_instance['number']) );
		return $instance;
	}

	
	function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}
		if ( isset( $instance[ 'number' ] ) ) {
			$limit = $instance[ 'number' ];
		} else {
			$limit = 1;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        
        <p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'No of jobs to display:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" size="3" value="<?php echo esc_attr( $limit ); ?>">
		</p>
	<?php
	}
}

?>