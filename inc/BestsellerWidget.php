<?php
// Register and load the widget
function wpb_load_widget() {
	register_widget( 'BestsellerWidget' );
}

add_action( 'widgets_init', 'wpb_load_widget' );

// Creating the widget
class BestsellerWidget extends WP_Widget {

	function __construct() {
		parent::__construct(

// Base ID of your widget
			'bestseller_widget',

// Widget name will appear in UI
			__( 'BestsellerWidget', 'bestseller_widget' ),

// Widget description
			array( 'description' => __( 'Widget to display bestsellers', 'bestseller widget' ), )
		);
	}

// Creating widget front-end

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			$title = $args['before_title'] . $title . $args['after_title'];
		}
		$api        = new ApdApi();
		$bestseller = (object) $api->getBestseller( 'automowers' );
//		krumo( $bestseller );

		?>
		<div class="product--box">
			<?= $title; ?>
			<div class="product--image-box">
				<div class="image--promotion-widget"><?= $bestseller->Promo; ?></div>
				<div class="product--teaser">
					<a href="<?= $bestseller->AmazonUrl; ?>" title="<?= $bestseller->Longname; ?>" target="_blank" rel="nofollow"><img class="image-responsive" src="<?= $bestseller->MediumImageUrl; ?>" alt="<?= $bestseller->Longname; ?>"></a>
					<div class="product--amazon-rating text-center">
						<a class="neat-link" href="<?= $bestseller->AmazonUrl; ?>" title="<?= $bestseller->Longname; ?> auf Amazon ansehen" target="_blank" rel="nofollow"><?= $bestseller->RatingStars; ?></a>
					</div>
					<a class="neat-link" href="<?= $bestseller->Permalink;?>" title="<?= $bestseller->Longname;?> Produktbeschreibung lesen"><h3><?= $bestseller->Longname;?></h3></a>
					<div class="text-center product--price">
						<a class="neat-link" href="<?= $bestseller->AmazonUrl; ?>" title="<?= $bestseller->Longname; ?> auf Amazon ansehen" target="_blank" rel="nofollow"><?= $bestseller->AmazonPriceFormatted; ?></a>
					</div>
				</div>
			</div>
			<div class="product--text">
				<ul class="list-pro">
					<?= $bestseller->AdvantagesNarrow; ?>
				</ul>
			</div>
			<a class="btn btn-apd-default btn-block" target="_self" rel="nofollow" href="<?= $bestseller->Permalink;?>" title="<?= $bestseller->Longname;?> Produktbeschreibung lesen"><span>Produktbeschreibung</span></a>
			<a class="btn btn-apd-default btn-block" target="_blank" rel="nofollow" href="<?= $bestseller->AmazonUrl;?>" title="<?= $bestseller->Longname;?> auf Amazon ansehen"><i class="fa fa-amazon"></i><span>zu Amazon<span class="affiliate-hint">*</span></span></a>
		</div>
		<?php
		echo $args['after_widget'];
	}

// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'New title', 'wpb_widget_domain' );
		}
// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}

// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here