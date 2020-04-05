<?php
$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
$show_lead_form =   get_post_meta($plan_id, 'listingproc_leadform', true);
if(empty($plan_id)) {
    $plan_id    =   'none';
}
if($plan_id == 'none') {
    $show_lead_form =   'true';
}
if($show_lead_form != 'true') return false;
$gSiteKey = lp_theme_option('lp_recaptcha_site_key');
$enableCaptcha = lp_check_receptcha('lp_recaptcha_lead');


$lead_form_customizer   =   'no';
if( class_exists('Listingpro_lead_form') && get_option( 'lead-form-active' ) == 'yes' )
{
    $lead_form_customizer   =   'yes';
}
if( $lead_form_customizer == 'yes' )
{
    $lp_lead_form               =    get_post_meta( $post->ID, 'lp_lead_form', true );
    $lead_form_user_dashboard   =    get_option('lead_form_user_dashboard');

    if( !empty( $lp_lead_form ) && $lead_form_user_dashboard == 1 )
    {
        echo do_shortcode( $lp_lead_form );
    }
    else
    {
        $form_shortcode =   get_option('lead_form_admin');
        if( empty( $form_shortcode ) )
        {
            echo do_shortcode( "[lead-form][lp-customizer-field type='text' name='name7' placeholder='Name:' class='myclass' label='Name'][lp-customizer-field type='email' name='email7' placeholder='Email:' class='myclass' label='Email'][lp-customizer-field type='text' name='phone7' placeholder='Phone:' class='myclass' label='Phone'][lp-customizer-field type='textarea' name='message7' placeholder='Message:' class='myclass' label='Message'][/lead-form]" );
        }
        else
        {
            echo do_shortcode( $form_shortcode );
        }
    }

} else {
    global $listingpro_options, $post;
    $showleadform = false;
    $lp_leadForm = $listingpro_options['lp_lead_form_switch'];
    if($lp_leadForm=="1"){
        $claimed_section = listing_get_metabox('claimed_section');
        $show_leadform_only_claimed = $listingpro_options['lp_lead_form_switch_claim'];
        $showleadform = true;
        if($show_leadform_only_claimed== true){
            if($claimed_section == 'claimed') {
                $showleadform = true;
            }
            else{
                $showleadform = false;
            }
        }
    }
    $privacy_policy = $listingpro_options['payment_terms_condition'];
    $privacy_leadform = $listingpro_options['listingpro_privacy_leadform'];

    if($showleadform == true) { ?>

        <div class="widget-box business-contact">
            <div class="user_text">
                <?php
                $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                $avatar ='';
                if(!empty($author_avatar_url)) {
                    $avatar =  $author_avatar_url;

                } else {
                    $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '94' );
                    $avatar =  $avatar_url;

                }
                ?>
                <div class="author-img">
                    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><img src="<?php echo esc_url($avatar); ?>" alt=""></a>
                </div>
                <div class="author-social">
                    <div class="status">
                        <span class="online"><a ><?php echo get_the_author_meta('display_name'); ?></a></span>
                    </div>
                    <ul class="social-icons post-socials">
                        <?php if(!empty($user_facebook)) { ?>
                            <li>
                                <a href="<?php echo esc_url($user_facebook); ?>">
                                    <?php echo listingpro_icons('fbGrey'); ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if(!empty($user_instagram)) { ?>
                            <li>
                                <a href="<?php echo esc_url($user_instagram); ?>">
                                    <?php echo listingpro_icons('instaGrey'); ?>
                                </a>
                            </li>
                        <?php } if(!empty($user_twitter)) { ?>
                            <li>
                                <a href="<?php echo esc_url($user_twitter); ?>">
                                    <?php echo listingpro_icons('tmblrGrey'); ?>
                                </a>
                            </li>
                        <?php } if(!empty($user_linkedin)) { ?>
                            <li>
                                <a href="<?php echo esc_url($user_linkedin); ?>">
                                    <?php echo listingpro_icons('clinkedin'); ?>
                                </a>
                            </li>
                        <?php } if(!empty($user_pinterest)) { ?>
                            <li>
                                <a href="<?php echo esc_url($user_pinterest); ?>">
                                    <?php echo listingpro_icons('cinterest'); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="contact-form quickform">
                <form data-lp-recaptcha="<?php echo $enableCaptcha; ?>" data-lp-recaptcha-sitekey="<?php echo $gSiteKey; ?>" class="form-horizontal hidding-form-feilds margin-top-20"  method="post" id="contactOwner">
                    <?php

                    $author_id = '';
                    $author_email = '';
                    $author_email = get_the_author_meta( 'user_email' );
                    $author_id = get_the_author_meta( 'ID' );

                    ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name7" id="name7" placeholder="<?php esc_html_e('Name:','listingpro'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email7" id="email7" placeholder="<?php esc_html_e('Email:','listingpro'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="phone7" id="phone7" placeholder="<?php esc_html_e('Phone','listingpro'); ?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" name="message7" id="message7" placeholder="<?php esc_html_e('Message:','listingpro'); ?>"></textarea>
                    </div>

                    <?php
                    if( !empty( $privacy_policy  ) && $privacy_leadform == 'yes' )
                    {
                        ?>
                        <div class="form-group lp_privacy_policy_Wrap">
                            <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                            <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                            <div class="help-text">
                                <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php echo esc_html__('You agree & accept our Terms & Conditions for submitting this information?', 'listingpro'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group margin-bottom-0 pos-relative">
                            <input type="submit" value="<?php esc_html_e('Send', 'listingpro'); ?>"
                                   class="lp-review-btn btn-second-hover" disabled>
                            <input type="hidden" value="<?php the_ID(); ?>" name="post_id">
                            <input type="hidden" value="<?php echo esc_attr($author_email); ?>" name="author_email">
                            <input type="hidden" value="<?php echo esc_attr($author_id); ?>" name="author_id">
                            <i class="lp-search-icon fa fa-send"></i>
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="form-group margin-bottom-0 pos-relative">
                            <input type="submit" value="<?php esc_html_e('Send', 'listingpro'); ?>"
                                   class="lp-review-btn btn-second-hover">
                            <input type="hidden" value="<?php the_ID(); ?>" name="post_id">
                            <input type="hidden" value="<?php echo esc_attr($author_email); ?>" name="author_email">
                            <input type="hidden" value="<?php echo esc_attr($author_id); ?>" name="author_id">
                            <i class="lp-search-icon fa fa-send"></i>
                        </div>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>

    <?php }
}

?>