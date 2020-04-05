<?php
/**
 * Review ajax
 *
 */

/* ============== ListingPro Review Ajax Init ============ */



/* ============== ListingPro Claim Ajax Process ============ */

add_action('wp_ajax_listingpro_review_status', 'listingpro_review_status');
add_action('wp_ajax_nopriv_listingpro_review_status', 'listingpro_review_status');
if(!function_exists('listingpro_review_status')){
    function listingpro_review_status(){

        if( isset( $_POST[ 'formData' ] ) ) {
            $data = json_decode(stripslashes($_POST[ 'formData' ]));
            $id = '';
            $current_status = '';
            $passive_status = '';
            $response;

            $id = $data[0];
            $current_status = $data[1];
            $passive_status = $data[2];

            if( !empty($id) ){
                listing_set_metabox('review_status', '', $id);
                listing_set_metabox('review_status', $passive_status, $id);

                $response = array('statuss' => 'success', 'current_status' => $passive_status, 'passive_status' => $current_status);

                echo json_encode( $response );

            }
            else{
                $response = array('statuss' => 'fail');
                echo json_encode($response);
            }

        }

        else{
            $response = array('status' => 'fail');
            echo json_encode($response);
        }

        exit();


    }
}



add_action('wp_ajax_listingpro_reviews_settings', 'listingpro_reviews_settings');
add_action('wp_ajax_nopriv_listingpro_reviews_settings', 'listingpro_reviews_settings');
if(!function_exists('listingpro_reviews_settings')){
    function listingpro_reviews_settings(){

        if( isset( $_POST[ 'ratings_settings_data' ] ) )
        {
            $return =   array();
            if( is_user_logged_in() )
            {
                $userID =   get_current_user_id();
                update_option( 'lp-ratings-settings', $_POST['ratings_settings_data'] );
            }
            $return['status']   =   'success';
            $return['data']     =   $_POST['ratings_settings_data'];
            die(json_encode($return));
        }
    }
}

add_action('wp_ajax_listingpro_reviews_default_settings', 'listingpro_reviews_default_settings');
add_action('wp_ajax_nopriv_listingpro_reviews_default_settings', 'listingpro_reviews_default_settings');
if(!function_exists('listingpro_reviews_default_settings')){
    function listingpro_reviews_default_settings(){

        if( isset( $_POST[ 'ratings_default_settings_data' ] ) )
        {
            $return =   array();
            if( is_user_logged_in() )
            {
                update_option( 'lp-ratings-default-settings', $_POST['ratings_default_settings_data'] );
            }
            $return['status']   =   'success';
            $return['data']     =   $_POST['ratings_default_settings_data'];
            die(json_encode($return));
        }
    }
}


add_action('wp_ajax_listingpro_reviews_settings_remove', 'listingpro_reviews_settings_remove');
add_action('wp_ajax_nopriv_listingpro_reviews_settings_remove', 'listingpro_reviews_settings_remove');
if(!function_exists('listingpro_reviews_settings_remove')){
    function listingpro_reviews_settings_remove(){

        if( isset( $_POST ) )
        {
            $return =   array();

            $target         =   $_POST['target'];
            $removeType     =   $_POST['removeType'];
            $removeDefault  =   $_POST['removeDefault'];

            if( is_user_logged_in() )
            {
                if( $removeDefault == 'yes' )
                {
                    $default_settings   =   get_option( 'lp-ratings-default-settings' );

                    unset( $default_settings['default'][$target] );
                    $default_settings['default']   =   array_values($default_settings['default']);
                    update_option( 'lp-ratings-default-settings', $default_settings );

                    $return['status']   =   'success';
                    die(json_encode($return));
                }
                $existing_settings  =   get_option( 'lp-ratings-settings' );

                if( $removeType == 'field' )
                {
                    $targetArr  =   explode( '-', $target );
                    $catIndex   =   $targetArr[0];
                    $fieldIndex =   $targetArr[1];

                    unset($existing_settings[$catIndex][$fieldIndex]);
                    $existing_settings[$catIndex]  =   array_values($existing_settings[$catIndex]);
                }
                if( $removeType == 'group' )
                {
                    unset($existing_settings[$target]);
                    $existing_settings  =   array_values($existing_settings);
                }

                update_option( 'lp-ratings-settings', $existing_settings );
                $return['status']   =   'success';
                die(json_encode($return));
            }


        }

    }
}