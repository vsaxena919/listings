<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_Locations extends Widget_Base {

    public function get_name() {
        return 'listing-locations';
    }

    public function get_title() {
        return __( 'Locations', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $categories = get_terms('location', array('hide_empty' => false));
        $locations = array();
        foreach($categories as $category) {
            $locations[$category->term_id] = $category->name;
        }
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );

        $this->add_control(
            'locstyles',
            [
                'label' => __( 'Location Styles', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'loc_abstracted',
                'options' => [
                    'loc_abstracted' => __("Abstracted View", "elementor-listingpro"),
                    "loc_boxed" => __("Boxed View", "elementor-listingpro"),
                    "loc_boxed_2" => __("Boxed View 2", "elementor-listingpro"),
                    "grid_abstracted" => __("Grid View", "elementor-listingpro"),
                ],
            ]
        );
        $this->add_control(
            'location_ids',
            [
                'label' => __( 'Select Locations', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'multiple' => true,
                'options' => $locations,
            ]
        );
        $this->add_control(
            'location_order',
            [
                'label' => __( 'Order', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [
                    'ASC' => __("ASC", "elementor-listingpro"),
                    "DESC" => __("DESC", "elementor-listingpro"),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'elementor-listingpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_transform',
            [
                'label' => __( 'Text Transform', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __( 'None', 'elementor-hello-world' ),
                    'uppercase' => __( 'UPPERCASE', 'elementor-listingpro' ),
                    'lowercase' => __( 'lowercase', 'elementor-listingpro' ),
                    'capitalize' => __( 'Capitalize', 'elementor-listingpro' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_locations( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}

if(!function_exists('listingpro_shortcode_locations')) {
    function listingpro_shortcode_locations($atts, $content = null) {
        extract(shortcode_atts(array(
            'location_ids'   => '',
            'location_order'   => 'ASC',
            'locstyles'    => 'loc_abstracted',
        ), $atts));
        require_once (THEME_PATH . "/include/aq_resizer.php");
        $output = null;
        global $listingpro_options;
        $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
        if ($locstyles == 'loc_boxed_2') {
            $Locations = $location_ids;
            $ucat = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'order' => $location_order,
                'include' => $Locations
            );
            $allLocations = get_terms('location', $ucat);

            $output .= '<div class="lp-section-content-container"> <div class="lp-locations">';
            $output .= '   <div class="lp-locations-slider"> ';
            foreach ($allLocations as $location) {
                $location_image = listing_get_tax_meta($location->term_id, 'location', 'image');
                $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','location', $location->term_id);
                $imgurl = aq_resize($location_image, '185', '175', true, true, true);
                if (empty($imgurl)) {
                    $imgurl = 'https://via.placeholder.com/185x175';
                }
                $output .= '<div class="col-md-2 col-xs-6">
                        <div class="lp-location-box">
                            <div class="lp-location-thumb">
                                <a href="' . esc_url(get_term_link($location->term_id, 'location')) . '"><img src="' . $imgurl . '" alt="' . esc_attr($location->name) . '"></a>
                            </div>
                            <div class="lp-location-bottom">
                                <a href="' . esc_url(get_term_link($location->term_id, 'location')) . '"><span class="lp-cat-name">' . esc_attr($location->name) . '</span> <span class="lp-cat-list-count">' . esc_attr($totalListinginLoc) . ' ' . esc_html__('Listings', 'listingpro-plugin') . '</span></a>
                            </div>
                        </div>
                    </div>';
            }

            $output .= '   <div class="clearfix"></div> </div>';
            $output .= '</div></div>';
        } else {
            if( ($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2' ) && wp_is_mobile() ){


                $app_view2_location_class   =   '';
                if( $listing_mobile_view == 'app_view2' )
                {
                    $app_view2_location_class   =   'app-view2-location-container';
                }

                $output .= '<div class="lp-section-content-container lp-location-slider clearfix '. $app_view2_location_class .'">';

                $Locations = $location_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'order' => $location_order,
                    'include'=> $Locations
                );
                $allLocations = get_terms( 'location',$ucat);


                foreach($allLocations as $location) {
                    $location_image = listing_get_tax_meta($location->term_id,'location','image');

                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','location', $location->term_id);

                    $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                    $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                    $image_alt = "";
                    if( !empty($location_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_400', true );
                        $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                        $imgurl = $thumbnail_url[0];
                    }
                    else{
                        $imgurl = aq_resize( $location_image, '270', '400', true, true, true);
                        if(empty($imgurl) ){
                            $imgurl = 'https://via.placeholder.com/270x400';
                        }
                    }



                    $output .= '<div class="'.$gridStyle.'">
										<div class="city-girds lp-border-radius-8 location-girds4">
											<div class="city-thumb">
												<img src="'. $imgurl.'" alt="'.$image_alt.'" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="'.esc_url( get_term_link( $location->term_id , 'location')).'">'.esc_attr($location->name).'</a>
												</h3>
												<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
											</div>
											<a href="'.esc_url( get_term_link( $location )).'" class="overlay-link"></a>
										</div>
									</div>';
                }


                $output .= '</div>';

            }else{
                if($locstyles == "loc_abstracted") {
                    $output .= '<div class="lp-section-content-container row">';

                    $Locations = $location_ids;
                    $ucat = array(
                        'post_type' => 'listing',
                        'hide_empty' => false,
                        'order' => $location_order,
                        'include'=> $Locations
                    );
                    $allLocations = get_terms( 'location',$ucat);

                    $grid = 0;


                    foreach($allLocations as $location) {
                        $location_image = listing_get_tax_meta($location->term_id,'location','image');

                        $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','location', $location->term_id);

                        if($grid == 0){
                            $gridStyle = 'col-md-6 col-sm-6  col-xs-12 cities-app-view';

                            $image_alt = "";
                            $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                            if( !empty($location_image_id) ){
                                $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location570_455', true );
                                $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                                $imgurl = $thumbnail_url[0];
                            }
                            else{
                                $imgurl = aq_resize( $location_image, '570', '455', true, true, true);
                                if(empty($imgurl) ){
                                    $imgurl = 'https://via.placeholder.com/570x455';
                                }
                            }

                        }elseif($grid == 1){
                            $gridStyle = 'col-md-6 col-sm-6  col-xs-12 cities-app-view';

                            $image_alt = "";
                            $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                            if( !empty($location_image_id) ){
                                $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location570_228', true );
                                $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                                $imgurl = $thumbnail_url[0];
                            }
                            else{
                                $imgurl = aq_resize( $location_image, '570', '228', true, true, true);
                                if(empty($imgurl) ){
                                    $imgurl = 'https://via.placeholder.com/570x228';
                                }
                            }

                        }else{
                            $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                            $image_alt = "";
                            $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                            if( !empty($location_image_id) ){
                                $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_197', true );
                                $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                                $imgurl = $thumbnail_url[0];
                            }
                            else{
                                $imgurl = aq_resize( $location_image, '270', '197', true, true, true);
                                if(empty($imgurl) ){
                                    $imgurl = 'https://via.placeholder.com/270x197';
                                }
                            }

                        }


                        $output .= '<div class="'.$gridStyle.'">
										<div class="city-girds lp-border-radius-8">
											<div class="city-thumb">
												<img src="'. $imgurl.'" alt="'.$image_alt.'" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="'.esc_url( get_term_link( $location->term_id , 'location')).'">'.esc_attr($location->name).'</a>
												</h3>
												<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
											</div>
											<a href="'.esc_url( get_term_link( $location )).'" class="overlay-link"></a>
										</div>
									</div>';
                        $grid++;
                    }


                    $output .= '</div>';
                }
                elseif($locstyles == "loc_boxed"){
                    $output .= '<div class="lp-section-content-container row">';

                    $Locations = $location_ids;
                    $ucat = array(
                        'post_type' => 'listing',
                        'hide_empty' => false,
                        'order' => $location_order,
                        'include'=> $Locations,
                    );
                    $allLocations = get_terms( 'location',$ucat);


                    foreach($allLocations as $location) {
                        $location_image = listing_get_tax_meta($location->term_id,'location','image');


                        $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                        if( !empty($location_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_197', true );
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        }
                        else{
                            $imgurl = aq_resize( $location_image, '270', '197', true, true, true);
                            if(empty($imgurl) ){
                                $imgurl = 'https://via.placeholder.com/270x197';
                            }
                        }

                        $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','location', $location->term_id);
                        $output .= '<div class="'.$gridStyle.'">
										<div class="city-girds lp-border-radius-8">
											<div class="city-thumb">
												<img src="'. $imgurl.'" alt="'.$image_alt.'" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="'.esc_url( get_term_link( $location->term_id , 'location')).'">'.esc_attr($location->name).'</a>
												</h3>
												<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
											</div>
											<a href="'.esc_url( get_term_link( $location )).'" class="overlay-link"></a>
										</div>
									</div>';
                    }


                    $output .= '</div>';
                }
                else{
                    $output .= '<div class="lp-section-content-container row">';

                    $Locations = $location_ids;
                    $ucat = array(
                        'post_type' => 'listing',
                        'hide_empty' => false,
                        'order' => $location_order,
                        'include'=> $Locations
                    );
                    $allLocations = get_terms( 'location',$ucat);


                    foreach($allLocations as $location) {
                        $location_image = listing_get_tax_meta($location->term_id,'location','image');
                        $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','location', $location->term_id);

                        $gridStyle = 'col-md-3 col-sm-3 col-xs-12 cities-app-view';

                        $image_alt = "";
                        $location_image_id = listing_get_tax_meta($location->term_id,'location','image_id');
                        if( !empty($location_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($location_image_id, 'listingpro_location270_400', true );
                            $image_alt = get_post_meta($location_image_id, '_wp_attachment_image_alt', true);
                            $imgurl = $thumbnail_url[0];
                        }
                        else{
                            $imgurl = aq_resize( $location_image, '270', '400', true, true, true);
                            if(empty($imgurl) ){
                                $imgurl = 'https://via.placeholder.com/270x400';
                            }
                        }


                        $output .= '<div class="'.$gridStyle.'">
										<div class="city-girds lp-border-radius-8 location-girds4">
											<div class="city-thumb">
												<img src="'. $imgurl.'" alt="'.$image_alt.'" />
											</div>
											<div class="city-title text-center">
												<h3 class="lp-h3">
													<a href="'.esc_url( get_term_link( $location->term_id , 'location')).'">'.esc_attr($location->name).'</a>
												</h3>
												<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
											</div>
											<a href="'.esc_url( get_term_link( $location )).'" class="overlay-link"></a>
										</div>
									</div>';
                    }


                    $output .= '</div>';
                }


            }
        }
        return $output;
    }
}

