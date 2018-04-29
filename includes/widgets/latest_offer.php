<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_action( 'widgets_init', function () {
	register_widget( 'Aff_Latest_Offer_Widget' );
});

/**
 * Custom widgets.
 *
 * @package
 */

if ( ! class_exists( 'Aff_Latest_Offer_Widget' ) ) :
	
	/**
	 * Latest Offer widget class.
	 *
	 * @since 0.1.6
	 */
	class Aff_Latest_Offer_Widget extends WP_Widget {
		
		private $post_type          = AFFILIATE_PROMOTIONS_PREFIX.'offer';
		private $post_category_term = AFFILIATE_PROMOTIONS_PREFIX.'category';
		
		function __construct() {
			
			$opts = array(
				'classname'   => AFFILIATE_PROMOTIONS_PREFIX.'latest_offers_widget',
				'description' => esc_html__( 'Affiliate offers widget', AFFILIATE_PROMOTIONS_PLUG ),
			);
			parent::__construct(
				AFFILIATE_PROMOTIONS_PREFIX.'latest_offers' ,
				esc_html__( 'Aff-Promos: Latest Offers', AFFILIATE_PROMOTIONS_PLUG ), $opts );
			$this->include_third_parties();
			
		}
		
		function include_third_parties(){
			
			if (!wp_style_is(AFFILIATE_PROMOTIONS_PREFIX.'latest_offers_widget','queue'))
				wp_enqueue_style( AFFILIATE_PROMOTIONS_PREFIX.'latest_offers_widget', plugins_url('public/assets/css/affpromos_latest_offers_widget.css',dirname(dirname(__FILE__)) ));
			
			if (!wp_style_is('jquery-slick','queue'))
				wp_enqueue_style( 'jquery-slick', plugins_url('libs/slick/slick.css',dirname(__FILE__) ));
			
			if (!wp_script_is('jquery-slick','queue'))
				wp_enqueue_script( 'jquery-slick', plugins_url('libs/slick/slick.js',dirname(__FILE__)), array('jquery'), '1.6.0', true );
		}
		
		
		function widget( $args, $instance ) {
			
			$title             	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			
			$offer_category     = ! empty( $instance['offer_category'] ) ? $instance['offer_category'] : 0;
			
			$offer_vendor       = ! empty( $instance['offer_vendor'] ) ? $instance['offer_vendor'] : 0;
			
			$offer_number       = ! empty( $instance['offer_number'] ) ? $instance['offer_number'] : 6;
			
			echo $args['before_widget']; ?>

            <div class="affpromos_latest_offer_widget <?php echo AFFILIATE_PROMOTIONS_PREFIX ?>latest-offers-wrapper">
				
				<?php
				
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}
				
				$carousel_args = array(
					'slidesToShow' 	=> 4,
					'slidesToScroll'=> 4,
					'dots'         	=> true,
					'arrows'       	=> false,
					'responsive'   	=> array(
						array(
							'breakpoint' => 1024,
							'settings'   => array(
								'slidesToShow' => 4,
							),
						),
						array(
							'breakpoint' => 992,
							'settings'   => array(
								'slidesToShow' => 3,
							),
						),
						array(
							'breakpoint' => 659,
							'settings'   => array(
								'slidesToShow' => 2,
							),
						),
						array(
							'breakpoint' => 479,
							'settings'   => array(
								'slidesToShow' => 1,
							),
						),
					),
				);
				
				$carousel_args_encoded = wp_json_encode( $carousel_args );
				
				$meta_query = array();
				
				$tax_query  = array();
				
				if($offer_category){
					$tax_query[] = array(
						'taxonomy' => $this->post_category_term,
						'field'    => 'id',
						'terms'    => absint( $offer_category ),
						'operator' => 'IN',
					);
					
				}
				
				if($offer_vendor){
					$meta_query[] = array(
						'relation' => 'IN',
						array(
							'key'   => AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor',
							'value' => array($offer_vendor),
						),
					);
				}
				$meta_query[] = array(
					'relation' => 'IN',
					array(
						'key'       => AFFILIATE_PROMOTIONS_PREFIX.'offer_price_sale',
						'type'      => 'numeric',
						'compare'   => '>',
						'value'     => 0,
					),
				);
				
				$query_args = array(
					'post_type'             => $this->post_type,
					'post_status'           => 'publish',
					'ignore_sticky_posts'   => 1,
					'posts_per_page'        => absint( $offer_number ),
					'meta_query'            => $meta_query,
					'no_found_rows'         => true,
                    'orderby'               => array(
                            'date'  => 'DESC'
                    )
				);
				
				if ( !empty($tax_query) ) {
					$query_args['tax_query'] = $tax_query;
				}
				
				$latest_offers = new WP_Query( $query_args );
				if ( $latest_offers->have_posts() ) :?>

                    <div class="inner-wrapper">
                        <div class="<?php echo AFFILIATE_PROMOTIONS_PREFIX ?>offers-carousel-wrap">
                            <ul class="<?php echo AFFILIATE_PROMOTIONS_PREFIX ?>slick " data-slick='<?php echo $carousel_args_encoded; ?>'>
								<?php
								while ( $latest_offers->have_posts() ) :
									$latest_offers->the_post();
									include affpromos_get_template_file('widget-offer-item','widget');
								endwhile;
								
								wp_reset_postdata(); ?>
                            </ul>
                        </div>
                    </div>
				<?php else: ?>
                        <div class="inner-wrapper">
                            <?php _e('No offers available',AFFILIATE_PROMOTIONS_PLUG)?>
                        </div>
				<?php endif; ?>
            </div><!-- .latest-offers-widget -->
			<?php
			wp_reset_query();
			echo $args['after_widget'];
			
		}
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']          	= sanitize_text_field( $new_instance['title'] );
			$instance['offer_category']  	= absint( $new_instance['offer_category'] );
			$instance['offer_vendor']  	    = absint( $new_instance['offer_vendor'] );
			$instance['offer_number']  	    = absint( $new_instance['offer_number'] );
			
			return $instance;
		}
		
		function form( $instance ) {
			
			$instance = wp_parse_args( (array) $instance, array(
				'title'          		=> '',
				'offer_category' 		=> '',
				'offer_vendor'          => '',
				'offer_number' 		    => 6,
			) );
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <strong><?php esc_html_e( 'Title:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong>
                </label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
            </p>

            <p>
                <label for="<?php echo  esc_attr( $this->get_field_id( 'offer_category' ) ); ?>">
                    <strong><?php esc_html_e( 'Select Offer category:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong></label>
				<?php
				$cat_args = array(
					'orderby'         => 'name',
					'hide_empty'      => 0,
					'class' 		  => 'widefat',
					'taxonomy'        => $this->post_category_term,
					'name'            => $this->get_field_name( 'offer_category' ),
					'id'              => $this->get_field_id( 'offer_category' ),
					'selected'        => absint( $instance['offer_category'] ),
					'show_option_all' => esc_html__( 'All Categories',AFFILIATE_PROMOTIONS_PLUG ),
				);
				wp_dropdown_categories( $cat_args );
				?>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'product_type' ) ); ?>">
                    <strong><?php _e( 'Offer vendor:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong></label>
				<?php
				$this->dropdown_offer_vendor( array(
						'id'       => $this->get_field_id( 'offer_vendor' ),
						'name'     => $this->get_field_name( 'offer_vendor' ),
						'selected' => esc_attr( $instance['offer_vendor'] ),
					)
				);
				?>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_name('offer_number') ); ?>">
					<?php esc_html_e('Number of Offers:', AFFILIATE_PROMOTIONS_PLUG); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('offer_number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('offer_number') ); ?>" type="number" value="<?php echo absint( $instance['offer_number'] ); ?>" />
            </p>
			
			<?php
		}
		
		function dropdown_offer_vendor( $args ) {
			$defaults = array(
				'id'       => '',
				'class'    => 'widefat',
				'name'     => '',
				'selected' => 0,
			);
			
			$r = wp_parse_args( $args, $defaults );
			$output = '';
			
			$vendors = get_posts([
				'post_type'     => AFFILIATE_PROMOTIONS_PREFIX.'vendor',
				'post_status'   => 'publish',
				'numberposts'   => -1,
				'order'         => 'ASC',
				'orderby'       => 'title'
			]);
			
			
			if ( ! empty( $vendors ) ) {
				$output = "<select name='" . esc_attr( $r['name'] ) . "' id='" . esc_attr( $r['id'] ) . "' class='" . esc_attr( $r['class'] ) . "'>\n";
				$output .= '<option value="' . esc_attr( 0 ) . '" ';
				$output .= selected( $r['selected'],0 , false );
				$output .= '>' . esc_html( __('All Vendors',AFFILIATE_PROMOTIONS_PLUG) ) . '</option>\n';
				foreach ( $vendors as $vendor ) {
					$output .= '<option value="' . esc_attr( $vendor->ID ) . '" ';
					$output .= selected( $r['selected'], $vendor->ID, false );
					$output .= '>' . esc_html( $vendor->post_title ) . '</option>\n';
				}
				$output .= "</select>\n";
			}
			
			echo $output;
		}
		
	}

endif;