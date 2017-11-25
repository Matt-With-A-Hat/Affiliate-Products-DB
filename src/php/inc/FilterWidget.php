<?php
// Register and load the widget
function wpb_load_apd_filter_widget() {
	register_widget( 'ApdFilterWidget' );
}

add_action( 'widgets_init', 'wpb_load_apd_filter_widget' );

// Creating the widget
class ApdFilterWidget extends WP_Widget {

	function __construct() {
		parent::__construct(

// Base ID of your widget
			'apd_filter_widget',

// Widget name will appear in UI
			__( 'ApdFilterWidget', 'apd_filter_widget' ),

// Widget description
			array( 'description' => __( 'Widget to display filter for products', 'apd filter widget' ), )
		);
	}

// Creating widget front-end

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			$title = $args['before_title'] . $title . $args['after_title'];
		}
		$tablename = $instance['tablename'];
		$pageId    = $instance['targetpage'];
		$pageUrl   = get_permalink( $pageId );
		$template  = $tablename . '-filter';

		$atts = array( $tablename, $template, $title );

		$html = "<div class='box--normal box--widget'>";
		$html .= apd_filter_handler( $atts );
		$html .= "<div class='spacing-top-20'>";
		$html .= "<div class='row'><div class='col-md-12'><a href='$pageUrl' class='btn btn-apd-heavy btn-center'>Filter</a></div></div>";
		$html .= "</div>";
		$html .= "</div>";

		echo $html;
		echo $args['after_widget'];
	}

// Widget Backend
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'New title', 'wpb_widget_domain' );
		}

		$tablenameInst = $instance['tablename'];
		$pageId        = $instance['targetpage'];
		$template      = $tablenameInst . '-filter';

		//table selection
		$htmlTablenames  = '';
		$databaseService = new ApdDatabaseService();
		$productTables   = $databaseService->getProductTables();

		foreach ( $productTables as $tablename ) {
			$tablename = remove_table_prefix( $tablename );
			if ( $tablenameInst == $tablename ) {
				$htmlTablenames .= "<option value='$tablename' selected>$tablename</option>";
			} else {
				$htmlTablenames .= "<option value='$tablename'>$tablename</option>";
			}
		}

		//target website selection
		$args            = array(
			'depth'                 => 0,
			'child_of'              => 0,
			'selected'              => $pageId,
			'echo'                  => 0,
			'name'                  => $this->get_field_name( 'targetpage' ),
			'id'                    => $this->get_field_id( 'targetpage' ),
			'class'                 => 'widefat',
			'show_option_none'      => null, // string
			'show_option_no_change' => null, // string
			'option_none_value'     => null, // string
		);
		$htmlTargetpages = wp_dropdown_pages( $args );

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tablename' ); ?>"><?php _e( 'Tablename:' ); ?></label>
			<select class="widefat" name="<?= $this->get_field_name( 'tablename' ); ?>" id="<?= $this->get_field_id( 'tablename' ); ?>">
				<?= $htmlTablenames; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'targetpage' ); ?>"><?php _e( 'Zielseite:' ); ?></label>
			<?= $htmlTargetpages; ?>
		</p>
		<?php
	}

// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance               = array();
		$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['tablename']  = ( ! empty( $new_instance['tablename'] ) ) ? strip_tags( $new_instance['tablename'] ) : '';
		$instance['targetpage'] = ( ! empty( $new_instance['targetpage'] ) ) ? strip_tags( $new_instance['targetpage'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here