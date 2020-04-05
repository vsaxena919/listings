<?php

$latitude = listing_get_metabox('latitude');
$phone          =   listing_get_metabox('phone');
$rating = get_post_meta(get_the_ID(), 'listing_rate', true);
$longitude = listing_get_metabox('longitude');
$gAddress = listing_get_metabox('gAddress');
$lp_lat         =   listing_get_metabox_by_ID('latitude', get_the_ID());
$lp_lng         =   listing_get_metabox_by_ID('longitude', get_the_ID());
$title = get_the_title();
require_once (THEME_PATH . "/include/aq_resizer.php");
$default_feature_img= lp_theme_option_url('lp_def_featured_image');
$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
if (!empty($image)) {
    $img_url = aq_resize( $image[0], '92', '92', true, true, true);
}elseif (!empty($default_feature_img)){
    $img_url = $default_feature_img;
}else{
    $img_url = 'https://via.placeholder.com/92x92';
}

$lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';
if( empty( $cat_img ) )
{
    $cat_img =   $lp_default_map_pin;
}

echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="'.$cat_img.'" alt="cat-icon"></span>';




$adStatus = get_post_meta( get_the_ID(), 'campaign_status', true );

$CHeckAd = '';

$adClass = '';
$claimed_section = listing_get_metabox('claimed_section');



$claim = '';

$claimStatus = '';



if($claimed_section == 'claimed') {

    if(is_singular( 'listing' ) ){
        $claimStatus = esc_html__('Claimed', 'listingpro');
    }
    $claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i> '.$claimStatus.'</span>';

}elseif($claimed_section == 'not_claimed') {

    $claim = '';

}
$openStatus = listingpro_check_time(get_the_ID());

?>
<style type="text/css">
    .lp-grid-box-contianer{
        margin-bottom: 5px;
    }
</style>

<div class="col-md-12 lp-grid-box-contianer list_view card1 lp-grid-box-contianer1 lp-list-view-compact-outer" data-title="<?php echo get_the_title(); ?>" data-postaddress="<?php echo $gAddress ; ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>" data-lppinurl="<?php echo esc_attr($lp_default_map_pin); ?>">
    <div class="list_thumbnail lp-grid-box-thumb lp-listing-top-thumb list_own_col_lt">
        <a href="<?php echo get_post_permalink() ; ?>"><img src="<?php echo $img_url ; ?>" ></a>
    </div>
    <div class="list_own_col_gt ">
        <p class="lp_list_title">


            <?php
            if($adStatus == 'active'){
                echo  $CHeckAd = '<span class="lp_compact_list_ad">'.esc_html__('Ad','listingpro').'</span>';
                $adClass = 'promoted';
            }
            ?>
            <a href="<?php echo get_post_permalink() ; ?>"><?php echo $title ; ?></a>
            <?php

            if($claimed_section == 'claimed') {

                if(is_singular( 'listing' ) ){
                    $claimStatus = esc_html__('Claimed', 'listingpro');
                }
                echo     $claim = '<span class="lp_list_compact_claim verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i> '.$claimStatus.'</span>';

            }elseif($claimed_section == 'not_claimed') {
                $claim = '';
            }

            ?>


        </p>
        <p class="lp_list_address "><?php echo $gAddress ; ?></p>
        <span class="gaddress" style="display:none;"><?php echo $loc->name; ?></span>
        <?php
        $cats   =   get_the_terms( get_the_ID(), 'listing-category' );
        if($cats):
            $counter    =   1;
            foreach ( $cats as $cat ):
                $cat_link   =   get_term_link( $cat );
                if( $counter == 1 ):
                    $cat_img = listing_get_tax_meta($cat->term_id,'category','image');
                    $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';
                    if( empty( $cat_img ) )
                    {
                        $cat_img =   $lp_default_map_pin;
                    }
                    if( !empty( $cat_img ) ):
                        echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="'.$cat_img.'" alt="cat-icon"></span>';
                        ?>
                    <?php endif; endif; ?>
                <?php $counter++; endforeach; ?>
        <?php endif; ?>
        <p class="lp_list_call"><a href="tel:<?php echo $phone; ?>"><i class="fa fa-phone" aria-hidden="true"></i><?php echo esc_html__( 'Call Now', 'listingpro' ); ?> </a></p>
        <p class="lp_list_map"><a href="" data-lid="<?php echo get_the_ID(); ?>" data-lat="<?php echo $lp_lat; ?>" data-lng="<?php echo $lp_lng; ?>" class="show-loop-map-popup"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php esc_html_e('Show Map','listingpro'); ?></a> </p>
		<?php echo $openStatus; ?>
        <p class="lp_list_rating">
            <?php
            $NumberRating = listingpro_ratings_numbers($post->ID);
            if( $NumberRating == 0 ){
                ?>
                <span class="lp_list_no_review"><?php echo esc_html__( 'Be the first to review!', 'listingpro' ); ?></span>
                <?php
            }else{
                echo '<span class="lp-list-rateing-good">'. $rating .'</span>';
            }
            ?>
        </p>
    </div>
   
</div>