<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_action( 'widgets_init', function () {
	register_widget( 'Aff_Latest_Promotion_Widget' );
});

/**
 * Custom widgets.
 *
 * @package Affiliate Promotion
 */

if ( ! class_exists( 'Aff_Latest_Promotion_Widget' ) ) :
	
	/**
	 * Latest Promotion widget class.
	 *
	 * @since 0.1.6
	 */
	class Aff_Latest_Promotion_Widget extends WP_Widget {
		
		private $post_type          = AFFILIATE_PROMOTIONS_PREFIX.'promotion';
		private $post_category_term = AFFILIATE_PROMOTIONS_PREFIX.'category';
		
		function __construct() {
			
			$opts = array(
				'classname'   => AFFILIATE_PROMOTIONS_PREFIX.'latest_promotions_widget',
				'description' => esc_html__( 'Affiliate promotions widget', AFFILIATE_PROMOTIONS_PLUG ),
			);
			parent::__construct(
				AFFILIATE_PROMOTIONS_PREFIX.'latest_promotions' ,
				esc_html__( 'Aff-Promos: Latest Promotions', AFFILIATE_PROMOTIONS_PLUG ), $opts );
			$this->include_third_parties();
			
		}
		
		function include_third_parties(){
			
			if (!wp_style_is(AFFILIATE_PROMOTIONS_PREFIX.'latest_promotions_widget','queue'))
				wp_enqueue_style( AFFILIATE_PROMOTIONS_PREFIX.'latest_promotions_widget', plugins_url('public/assets/css/affpromos_latest_promotions_widget.css',dirname(dirname(__FILE__)) ));
			
		}
		
		function widget( $args, $instance ) {
			
			$title             	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			
			$promotion_category = ! empty( $instance['promotion_category'] ) ? $instance['promotion_category'] : 0;
			
			$promotion_vendor   = ! empty( $instance['promotion_vendor'] ) ? $instance['promotion_vendor'] : '';
			
			$promotion_count    = ! empty( $instance['promotion_count'] ) ? $instance['promotion_count'] : 6;
			
			echo $args['before_widget']; ?>

            <div class="affpromos_latest_promotion_widget <?php echo AFFILIATE_PROMOTIONS_PREFIX ?>latest-promotions-wrapper">
				
				<?php
				
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}
				
				$meta_query = array();
				
				$tax_query  = array();
				
				if($promotion_category){
					
					$tax_query[] = array(
						'taxonomy' => $this->post_category_term,
						'field'    => 'id',
						'terms'    => absint( $promotion_category ),
						'operator' => 'IN',
					);
					
				}
				
				if($promotion_vendor){
					$meta_query[] = array(
						'relation' => 'IN',
						array(
							'key'   => AFFILIATE_PROMOTIONS_PREFIX.'promotion_vendor',
							'value' => array($promotion_vendor),
						),
					);
				}
				
				$meta_query[] = array(
					'relation' => 'IN',
					array(
						'key'       => AFFILIATE_PROMOTIONS_PREFIX.'promotion_valid_until',
						'type'      => 'numeric',
						'compare'   => '>',
						'value'     => time(),
					),
				);
				
				$query_args = array(
					'post_type'           => $this->post_type,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => absint( $promotion_count ),
					'meta_query'          => $meta_query,
					'no_found_rows'       => true,
					'orderby'             => array(
						'date'  => 'DESC'
					)
				);
				
				if ( !empty($tax_query) ) {
					$query_args['tax_query'] = $tax_query;
				}
				
				$latest_promotions = new WP_Query( $query_args );
				if ( $latest_promotions->have_posts() ) :?>

                    <div class="inner-wrapper">
                        <div class="<?php echo AFFILIATE_PROMOTIONS_PREFIX ?>promotions-carousel-wrap">
							<?php
							while ( $latest_promotions->have_posts() ) :
								$latest_promotions->the_post();
								include affpromos_get_template_file('widget-promotion-item','widget');
							endwhile;
							
							wp_reset_postdata(); ?>
                        </div>
                    </div>
				<?php endif; ?>
            </div><!-- .latest-promotions-widget -->
			<?php
			echo $args['after_widget'];
			
		}
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']          	= sanitize_text_field( $new_instance['title'] );
			$instance['promotion_category'] = absint( $new_instance['promotion_category'] );
			$instance['promotion_vendor'] 	= sanitize_text_field( $new_instance['promotion_vendor'] );
			$instance['promotion_count']  	= absint( $new_instance['promotion_count'] );
			
			return $instance;
		}
		
		function form( $instance ) {
			
			$instance = wp_parse_args( (array) $instance, array(
				'title'          		=> '',
				'promotion_category' 	=> '',
				'promotion_vendor'      => '',
				'promotion_count'       => 8,
			) );
			?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <strong><?php esc_html_e( 'Title:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong>
                </label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
            </p>

            <p>
                <label for="<?php echo  esc_attr( $this->get_field_id( 'promotion_category' ) ); ?>"><strong><?php esc_html_e( 'Select Category:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong></label>
				<?php
				$cat_args = array(
					'orderby'         => 'name',
					'hide_empty'      => 0,
					'class' 		  => 'widefat',
					'taxonomy'        => AFFILIATE_PROMOTIONS_PREFIX.'category',
					'name'            => $this->get_field_name( 'promotion_category' ),
					'id'              => $this->get_field_id( 'promotion_category' ),
					'selected'        => absint( $instance['promotion_category'] ),
					'show_option_all' => esc_html__( 'All Categories',AFFILIATE_PROMOTIONS_PLUG ),
				);
				wp_dropdown_categories( $cat_args );
				?>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'promotion_vendor' ) ); ?>">
                    <strong><?php _e( 'Promotion vendor:', AFFILIATE_PROMOTIONS_PLUG ); ?></strong>
                </label>
				<?php
				$this->dropdown_promotion_vendor( array(
						'id'       => $this->get_field_id( 'promotion_vendor' ),
						'name'     => $this->get_field_name( 'promotion_vendor' ),
						'selected' => esc_attr( $instance['promotion_vendor'] ),
					)
				);
				?>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_name('promotion_count') ); ?>">
					<?php esc_html_e('Number of Promotions:', AFFILIATE_PROMOTIONS_PLUG); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('promotion_count') ); ?>" name="<?php echo esc_attr( $this->get_field_name('promotion_count') ); ?>" type="number" value="<?php echo absint( $instance['promotion_count'] ); ?>" />
            </p>
			
			<?php
		}
		
		function dropdown_promotion_vendor( $args ) {
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