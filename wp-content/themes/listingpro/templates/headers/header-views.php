<?php

/**
 * Template header views.
 * templates/headers/header-views
 * @version 2.0
 */

?>

<?php
$topBannerView = lp_theme_option('top_banner_styles');
$header_views = lp_theme_option('header_views');
$listing_style = lp_theme_option('listing_style');
$listing_style_new = lp_theme_option('listing_style');
$page_header = lp_theme_option_url('page_header');
$map_height = lp_theme_option('map_height');
$videoBanner = lp_theme_option('lp_video_banner_on' );
$height = '';
if(!empty($map_height)){
    $height = ' style="height:'.$map_height.'px;"';
}else{
    $height = ' style="height:500px;"';
}

$imgClass = '';
if( $topBannerView == 'map_view' ) {
    $imgClass = '';
}elseif ( $topBannerView=="banner_view") {


    if($videoBanner=="video_banner"){
        $imgClass = 'lp-vedio-bg';
    }
    else{
        $imgClass = 'lp-header-bg';
    }

}
//-------------New view style banner------------------//
elseif ( $topBannerView=="banner_view_search_bottom"){

    if($videoBanner=="video_banner"){
        $imgClass = 'lp-vedio-bg';

    }else{

        $imgClass = 'lp-header-bg';
    }
}elseif ( $topBannerView=="banner_view_category_upper" || $topBannerView=="banner_view_search_inside"){

    if($videoBanner=="video_banner"){
        $imgClass = 'lp-vedio-bg';

    }else{

        $imgClass = 'lp-header-bg';
    }
}elseif ($topBannerView=="banner_view_search_inside"){

    if($videoBanner=="video_banner"){
        $imgClass = 'lp-vedio-bg';

    }else{

        $imgClass = 'lp-header-bg';
    }

}

$header_fixed_class =   '';
if( $header_views == 'header_with_topbar_menu' && ( $listing_style == '3' || $listing_style == 2 ) && ( is_search() || is_tax( 'listing-category' ) || is_tax( 'location' ) || is_tax( 'features' ) ) ) {
    $header_fixed_class = 'header-fixed';
}
switch ($header_views) {
    case 'header_with_bigmenu':
        get_template_part( 'templates/headers/header-with-bigmenu');
        break;
}

$headerfour_mobile_height = '';
if($topBannerView == 'banner_view2'){
    $headerfour_mobile_height = 'lp-headerfour-height';
}

$header_inner_page_class =   'header-inner-page-wrap';
if( is_front_page() )
{
    $header_inner_page_class =   'header-front-page-wrap';
}

$banner_height = lp_theme_option('banner_height');

$header_container_height    =   '';
if( !empty( $banner_height ) && $topBannerView == 'banner_view2' && is_front_page() && !wp_is_mobile() )

{
    $header_container_height    =   'header-container-height';
}

$default_banner_styles  =   '';
if( $imgClass == 'lp-header-bgg' )
{
    global $listingpro_options;
    $home_banner_img    =   get_template_directory_uri().'/assets/images/home-banner.jpg';
    if( !empty( $listingpro_options['home_banner']['url'] ) )
    {
        $home_banner_img    =   $listingpro_options['home_banner']['url'];
    }
    $default_banner_styles  =   'background-image: url('.$home_banner_img.');';
}


$header_fixed = '';
if (!is_front_page() && !is_page_template("template-dashboard.php") ) {
    $header_fixed = lp_theme_option('header_fixed_inner_page');
    $header_search = lp_theme_option('search_switcher');
    if ($header_fixed == 1) {
        ?>
        <style>
            .lp-stickyHeader{
                position: fixed !important;
                width: 100% !important;
                z-index: 99999 !important;
            }
        </style>
        <script>
            jQuery( document ).ready(function() {
                jQuery(window).scroll(function () {
                    var scroll = jQuery(window).scrollTop();
                    wp_nav = jQuery('header').height();
                    if (scroll >= 150) {
                        jQuery("header").addClass("lp-stickyHeader");
                        jQuery("body").find("section.aliceblue").css("margin-top", wp_nav);
                    }else{
                        jQuery("header").removeClass("lp-stickyHeader");
                        jQuery("body").find("section.aliceblue").css("margin-top", '0');
                    }
                });
            });
        </script>
        <?php
    }
}
if (is_front_page()) {
    $header_fixed = lp_theme_option('header_fixed_front');

    if ($header_fixed == 1) {
        ?>
        <style>
            .pos-relative header{
                position: fixed !important;
                width: 100% !important;
                z-index: 99999 !important;
            }
        </style>
        <script type="text/javascript">
            jQuery( document ).ready(function() {
                var $wp_nav = jQuery('#wpadminbar').height();

                if ($wp_nav == null) {
                    jQuery('.pos-relative header').css("top","0");
                }
                var $p_nav = jQuery('.pos-relative header').height();
                jQuery('.lp-home-banner-contianer').css("margin-top",$p_nav);
                jQuery('.pos-relative header').css("top",$wp_nav);


            })
        </script>
        <?php
    }
}
?>


<div class="pos-relative <?php echo $header_inner_page_class . ' ' . $header_fixed ; ?>">
    <div class="header-container <?php echo $header_container_height; ?> <?php echo $listing_style; ?> <?php echo $header_fixed_class; ?> <?php if(is_front_page()){ echo esc_attr($imgClass); } ?> <?php echo $headerfour_mobile_height; ?>" style="<?php echo $default_banner_styles; ?>">
        <?php if( !is_page_template('template-dashboard.php') ) {
            ?>
            <?php
            switch ($header_views) {
                case 'header_with_topbar':
                    get_template_part('templates/headers/header-with-topbar');
                    break;
                case 'header_menu_bar':
                    get_template_part('templates/headers/header-menu-dropdown');
                    break;
                case 'header_without_topbar':
                    get_template_part('templates/headers/header-without-topbar');
                    break;
                case 'header_with_topbar_menu':
                    get_template_part('templates/headers/header-with-topbar-menu');
                    break;
                // New header styles
                case 'header_style5':
                    get_template_part('templates/headers/header-style5');
                    break;
                case 'header_style6':
                    get_template_part('templates/headers/header-style6');
                    break;
                case 'header_style7':
                    get_template_part('templates/headers/header-style7');
                    break;
                case 'header_style8':
                    get_template_part('templates/headers/header-style8');
                    break;
                case 'header_style9':
                    get_template_part('templates/headers/header-style9');
                    break;

                default:
                    break;
            }
        }
        get_template_part( 'templates/popups');
        get_template_part( 'templates/banner');

        //-------------start New view style banner------------------//
        if( $topBannerView == 'banner_view_search_bottom' && is_front_page()){
            get_template_part( 'templates/banner-search-bottom-view');
        }



        if( $topBannerView == 'banner_view_search_inside' && is_front_page()){
            get_template_part( 'templates/banner-search-bottom-view');
        }
        //-------------End New view style banner------------------//

        if( $listing_style_new == '4' && ( is_search() || is_tax( 'listing-category' ) || is_tax( 'list-tags' ) || is_tax( 'location' ) || is_tax( 'features' ) ) ){
            get_template_part( 'templates/headers/header-archive' );
        }
        ?>
    </div>
    <!--==================================Header Close=================================-->

    <!--================================== Search Close =================================-->
    <?php
    if(is_front_page() && !is_home()){
        if( $topBannerView == 'map_view' ) {
            get_template_part( 'templates/search/template_search1' );
        }
    }
    ?>

    <!--================================== Search Close =================================-->
</div>