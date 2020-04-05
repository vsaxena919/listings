<?php
//header_fullwidth
global $listingpro_options;
$header_fullwidth   =   $listingpro_options['header_fullwidth'];
$container_class    =   'container';
if( $header_fullwidth == 1 )
{
    $container_class    =   'container-fluid';
}
?>
<div class="lp-top-bar">
    <div class="<?php echo $container_class; ?>">
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="lp-top-bar-menu">
                    <?php echo listingpro_top_bar_menu(); ?>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="lp-top-bar-social text-right">
                    <?php listingpro_sharing_topbar(); ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>