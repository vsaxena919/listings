<?php
/*---------------------------------------------------
				adding reports page
----------------------------------------------------*/

add_action('admin_menu', 'lp_register_reviews_reports');
if(!function_exists('lp_register_reviews_reports')){ 
	function lp_register_reviews_reports() {
		add_submenu_page(
			'lp-flags',
			'Reviews',
			'Reviews Flags',
			'manage_options',
			'lp-review-flags',
			'lp_reviews_flag_submenu_page_callback' );
	}
}

/* post form code */
if( isset($_POST['review_id']) && isset($_POST['lp_review_report_submit']) ){
	
	$review_id = $_POST['review_id'];
	if(!empty($review_id)){
	  $review_post = array(
		  'ID'           => $review_id,
		  'post_status' => 'reported',
	  );
	  wp_update_post( $review_post );
	  
	  listing_set_metabox('review_reported', '', $review_id);
	  listing_set_metabox('review_reported_by', '', $review_id);
	  
	  if ( get_option( 'lp_reported_reviews' ) !== false ) {
								$reportedLisingsArray = array();
								$delReview = array();
								$reportedLisings = get_option( 'lp_reported_reviews' );
								if( strpos($reportedLisings, ',') !== false ){
									$reportedLisingsArray = explode(",",$reportedLisings);
								}else{
									$reportedLisingsArray[] = $reportedLisings;
									
								}
								
								$delReview[] = $review_id;
								$result=array_diff($reportedLisingsArray,$delReview);
								if(!empty($result)){
									$newRevieids = implode(",",$result);
									update_option( 'lp_reported_reviews', $newRevieids );
								}
								else{
									delete_option( 'lp_reported_reviews' );
								}
	  }
	  
	}
	
}
/* end post form code */

if(!function_exists('lp_reviews_flag_submenu_page_callback')){ 
	function lp_reviews_flag_submenu_page_callback() {		
?>
		<div class="wrap">
			<h2><?php esc_html_e('Reviews Reports', 'listingpro-plugin');?></h2>
			<div class="clearfix"></div>
			<ul class="subsubsu">
				<li class="all"><a href="#" class="current" aria-current="page">All <span class="count"></span></a></li>
			</ul>
			<?php
				global $paged;
				if ( get_option( 'lp_reported_reviews' ) !== false ) {
						$reportedLisings = get_option( 'lp_reported_reviews' );
						$ReportedLisints = array();
						if( strpos($reportedLisings, ',') !== false ){
							$ReportedLisints = explode(",",$reportedLisings);
						}else{
							$ReportedLisints[] = $reportedLisings;
							
						}
						$reports_query = new WP_Query( 
							array( 
								'post_type' => 'lp-reviews', 
								'post__in' => $ReportedLisints,
								'post_status' => 'publish',
								'posts_per_page' => 8,
								'paged' => $paged,
							) 
						);
						
						if ( $reports_query->have_posts() ) { ?>
							<div id="posts-filter" method="get">
							<div class="listingpro_coupon_table">
								<table class="table wp-list-table widefat fixed striped posts">
								<thead>
								  <tr>
									<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
									<th id="title" class="manage-column column-title column-primary sortable desc"><a href=""><span><?php echo esc_html__('Review Title', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Author', 'listingpro-plugin'); ?></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Reported For', 'listingpro-plugin'); ?></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
								  </tr>
								</thead>
								<tbody>
							<?php
											$confirmMsg = esc_html__('Are you sure you want to proceed?', 'listingpro-plugin');
											while ( $reports_query->have_posts() ) {
												$reports_query->the_post();
												$reportedCount = listing_get_metabox_by_ID('review_reported', get_the_ID());
												echo '<tr>';
												
													echo '<th scope="row" class="check-column">
														<input id="cb-select-1" type="checkbox" name="post[]" value="1">
													</th>';
												
													echo '<td class="manage-column column-categories">'.get_the_title().'</td>';
													echo '<td class="manage-column column-categories">'.get_the_author().'</td>';
													echo '<td class="manage-column column-categories">'.$reportedCount. ' '.esc_html__('Time', 'listingpro-plugin').'</td>';
													echo '<td class="manage-column column-categories"><form class="wp-core-ui" method="post">
													<input type="submit" name="lp_review_report_submit" class="button action" value="'.esc_html__('Confirm', 'listingpro-plugin').'">
													<input type="hidden" name="review_id" value="'.get_the_ID().'">
													</form></td>';
												echo '</tr>';
												wp_reset_postdata();
											}
											?>
									</tbody>
									
									<tfoot>
										<tr>
									<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
									<th id="title" class="manage-column column-title column-primary sortable desc"><a href=""><span><?php echo esc_html__('Review Title', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Author', 'listingpro-plugin'); ?></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Reported For', 'listingpro-plugin'); ?></th>
									<th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
								  </tr>
									</tfoot>
									
								</table>		
											
							<?php				
								
								echo listingpro_pagination();							
							}
						
				}
				else{
					?>
					<tr>
						<td  class="colspanchange" colspan="9">
						<?php esc_html_e('Sorry! There is no report found', 'listingpro-plugin');?>
						</td>
					</tr>
					<?php
				}
			?>
			
		</div>

<?php
	}
}
?>