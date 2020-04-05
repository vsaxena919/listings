<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Video_Testimonial extends Widget_Base {

    public function get_name() {
        return 'video-testimonial';
    }

    public function get_title() {
        return __( 'Video Testimonial', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'screen_image',
            [
                'label' => __( 'Video preview Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'video_url',
            [
                'label' => __( 'Video URL', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'testi_title',
            [
                'label' => __( 'Testimonial Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'author_name',
            [
                'label' => __( 'Author name', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'author_company',
            [
                'label' => __( 'Author Company', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'author_image',
            [
                'label' => __( 'Author Avatar', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'testi_content',
            [
                'label' => __( 'Testimonial Content', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
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
        echo listingpro_shortcode_video_box( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_video_box')) {
    function listingpro_shortcode_video_box($atts, $content = null) {
        extract(shortcode_atts(array(
            'screen_image'   => '',
            'video_url'   => '',
            'testi_title'   => '',
            'author_name'   => '',
            'author_company'   => '',
            'author_image'   => '',
            'testi_content'   => '',

        ), $atts));

        $output = null;
        $screenImage=null;
        $authorImage=null;


        if ( $screen_image ) {
            if( is_array( $screen_image ) )
            {
                $screen_image   =   $screen_image['id'];
            }
            $imgurl = wp_get_attachment_image_src( $screen_image, 'full');
            $screenImage = '<img src="'. $imgurl[0] .'">';
        }
        if ( $author_image ) {
            if( is_array( $author_image ) )
            {
                $author_image   =   $author_image['id'];
            }
            $imgurl = wp_get_attachment_image_src( $author_image, 'listingpro-author-thumb');
            $authorImage = '<img src="'. $imgurl[0] .'">';
        }


        $output .= '<div class="testimonial lp-section-content-container row">';

        $output .= '<div class="col-md-6">
						<div class="video-thumb">
								'.$screenImage.'
							<a href="'.$video_url.'" class="overlay-video-thumb popup-vimeo">
								<i class="fa fa-play-circle-o"></i>
							</a>
						</div><!-- ../video-thumb -->
					</div>';

        $output .= '<div class="col-md-6">
						<div class="testimonial-inner-box">
							<h3 class="margin-top-0">'.$testi_title.'</h3>
							<div class="testimonial-description lp-border-radius-5">
								<p>'.esc_attr($testi_content).'	</p>
							</div><!-- ../testimonial-description -->
							<div class="testimonial-user-info user-info">
								<div class="testimonial-user-thumb user-thumb">
									'.$authorImage.'
								</div>
								<div class="testimonial-user-txt user-text">
									<label class="testimonial-user-name user-name">'.$author_name.'</label><br>
									<label class="testimonial-user-position user-position">'.$author_company.'</label>
								</div>
							</div><!-- ../testimonial-user-info -->
						</div><!-- ../testimonial-inner-box -->
					</div>';

        $output .= '</div>';


        return $output;
    }
}