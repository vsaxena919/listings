<?php
global $listingpro_options;
$lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];
$listing_discount_data_init  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );

$listing_discount_data_final    =   array();
$strNow =   strtotime("NOW");

if( !empty( $listing_discount_data_init ) ):
    foreach ( $listing_discount_data_init as $key => $val )
    {
        if( isset($val['disExpE'] ) ) :
            if( ( $strNow < $val['disExpE'] || empty( $val['disExpE'] ) ) && ( $strNow > $val['disExpS'] || empty( $val['disExpS'] ) ) ) :
                $listing_discount_data_final[]  =   $val;
            endif;
        endif;
    }

    if( is_array( $listing_discount_data_final ) && !empty( $listing_discount_data_final ) ):
echo '<div class="list-discount-outer">';
        require_once (THEME_PATH . "/include/aq_resizer.php");
        ?>



        <?php

        $deal_counter   =   1;
        $total_deals    =   count( $listing_discount_data_final );

        $col_class      =   'col-md-6';

        $post_author_id = get_post_field( 'post_author', get_the_ID() );
        $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
	    if( isset( $GLOBALS['call_from'] ) && $GLOBALS['call_from'] == 'sidebar-area' )
	    {
		    $col_class  =   'col-md-12';
	    }
        elseif ( isset( $GLOBALS['call_from'] ) && $GLOBALS['call_from'] == 'content-area' )
	    {
		    $col_class  =   'col-md-6';
	    }
        elseif( $discount_displayin == 'sidebar' )
	    {
		    $col_class   =   'col-md-12';
	    }
        foreach ( $listing_discount_data_final as $key => $discount_data ):
            if( $deal_counter == 1 ):
                ?>
                <h4 class="lp-detail-section-title"><?php echo esc_html__( 'Deals', 'listingpro' ); ?></h4>
                <div class="lp-deals-wrap">
                <div class="row">
            <?php endif; ?>
            <?php

            $img_url    =   'https://via.placeholder.com/360x260';

            if( $discount_data['disImg'] )

            {

                $img_url    =   $discount_data['disImg'];

                $img_url  = aq_resize( $img_url, '360', '260', true, true, true);

            }



            ?>

            <div class="<?php echo $col_class; ?>">

                <div class="lp-deal">

                    <div class="deal-thumb">

                        <img src="<?php echo $img_url; ?>">

                    </div>

                    <div class="deal-details">

                        <?php
                        if( !empty( $discount_data['disExpE'] ) ):
                            ?>
                            <div class="deal-countdown-wrap">
                                <div id="lp-deals-countdown<?php echo $deal_counter; ?>" class="lp-countdown lp-deals-countdown<?php echo $deal_counter; ?>"
                                     data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>"
                                    data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>"
                                    data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>"
                                     data-day="<?php echo date( 'd', $discount_data['disExpE'] ); ?>"
                                     data-month="<?php echo date( 'm', $discount_data['disExpE'] )-1; ?>"
                                     data-year="<?php echo date( 'Y', $discount_data['disExpE'] ); ?>"></div>
                            </div>

                        <?php endif; ?>

                        <?php

                        if( $discount_data['disOff'] ) echo '<span class="lp-deal-off">'. $discount_data['disOff'] .'</span>';

                        ?>

                        <div class="deal-content">

                            <?php

                            if( $discount_data['disHea'] ) echo '<strong>'. $discount_data['disHea'] .'</strong>';

                            if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode($discount_data['disDes']) .'</p>';

                            $btn_href   =   '';

                            $btn_class  =   'lp-copy-code';
							$bnt_text_def   =   esc_html__( 'Click Here', 'listingpro' );
                           if( empty( $discount_data['disBT'] ) )
                           {
                               $discount_data['disBT'] =   $bnt_text_def;
                           }

                            if( $discount_data['disBL'] && !empty( $discount_data['disBL'] ) )

                            {

                                $btn_href   =   'href="'. $discount_data['disBL'] .'"';

                                $btn_class  =   '';

                            }

                            ?>

                            <a target="_blank" data-html="<?php echo $discount_data['disBT']; ?>" data-target-code="deal-copy-<?php echo $deal_counter; ?>" <?php echo $btn_href; ?> class="deal-button <?php echo $btn_class; ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo $discount_data['disBT']; ?></a>

                        </div>

                    </div>
                    <?php
                    if( empty( $discount_data['disBL'] ) ):
                        ?>
                        <div class="dis-code-copy-pop deal-copy-<?php echo $deal_counter; ?>" id="dicount-copy-<?php echo $deal_counter; ?>">
                            <span class="close-right-icon" data-target="deal-copy-<?php echo $deal_counter; ?>"><i class="fa fa-times"></i></span>
                            <div class="dis-code-copy-pop-inner">
                                <div class="dis-code-copy-pop-inner-cell">
                                    <p><?php echo esc_html__( 'Copy to clipboard', 'listingpro' ); ?></p>
                                    <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo $deal_counter; ?>" type="text" value="<?php echo $discount_data['disCod']; ?>"> <a data-target-code="dicount-copy-<?php echo $deal_counter; ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__( 'Copied', 'listingpro' ); ?>"><?php echo esc_html__( 'Copy', 'listingpro' ); ?></a></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php $deal_counter++; ?>

            <?php

            if( $deal_counter == $total_deals+1 )

            {

                echo '<div class="clearfix"></div></div>

                            </div>';

            }

            ?>

        <?php endforeach;; ?>

        <?php
echo '</div>';
    endif; endif;
?>