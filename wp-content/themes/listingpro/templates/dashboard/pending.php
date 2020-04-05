<div class="user-recent-listings-innerfade in active lp-listing-pending-tab" id="draft">
	<div class="tab-header">
		<h3><?php echo esc_html__('Pending Listings', 'listingpro'); ?></h3>
	</div>
	<div class="row lp-list-page-list">
		<?php
		global $paged, $wp_query, $listingpro_options;
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$args = array(
				'post_type' => 'listing',
				'post_status' => 'pending',
				'posts_per_page' => 20,
				'author' => $user_id,
				'paged' => $paged,
			);
			
			$deafaultFeatImg = lp_default_featured_image_listing();
			
			$listingsP_query = null;
			$listingsP_query = new WP_Query($args);

			if( $listingsP_query->have_posts() ) {
				while ($listingsP_query->have_posts()) : $listingsP_query->the_post();  
					$listingcurrency = listing_get_metabox('listingcurrency');
					$listingprice = listing_get_metabox('listingprice');
					$listingptext = listing_get_metabox('listingptext');
					$Plan_id = listing_get_metabox('Plan_id');
					$plan_time  = get_post_meta($Plan_id, 'plan_time', true);
					$postID = $post->ID;
					?>
					
					<div class="col-md-12 col-sm-6 col-xs-12 lp-list-view">
						<div class="lp-list-view-inner-contianer clearfix">
							<div class="col-md-1 col-sm-1 col-xs-12">
								<div class="lp-list-view-thumb">
									<div class="lp-list-view-thumb-inner">
										<?php
											if ( has_post_thumbnail()) {
												$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'thumbnail' );
												if(!empty($image[0])){
													echo "<a href='".get_the_permalink()."' >
															<img src='" . $image[0] . "' />
														</a>";
												}else {
													echo "<a href='".get_the_permalink()."' >
															<img src='".esc_url('https://via.placeholder.com/150x150')."' />
														</a>";
												}
											}elseif(!empty($deafaultFeatImg)){
												
												echo "<a href='".get_the_permalink()."' >
														<img src='".$deafaultFeatImg."' />
													</a>";
												
											}else {
												echo "<a href='".get_the_permalink()."' >
														<img src='".esc_url('https://via.placeholder.com/150x150')."' />
													</a>";
											}
										?>	
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 lp-listing-pending-tab-inner">
								<div class="lp-list-view-content lp-list-cnt">
									<div class="lp-list-view-content-upper">
										<a href="<?php echo get_the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
										<ul class="lp-grid-box-price list-style-none list-pt-display">
											<?php
											$catCycle = 1;
											$cats = get_the_terms( get_the_ID(), 'listing-category' );
											if(!empty($cats)){
												foreach ( $cats as $cat ) {
														if($catCycle==1){
															$category_image = listing_get_tax_meta($cat->term_id,'category','image');
															if(!empty($category_image)){ ?>
																<li class="category-cion">
																	<a href="<?php echo get_term_link($cat); ?>">
																		<img class="icon icons8-Food" src="<?php echo esc_attr($category_image); ?>" alt="cat-icon">
																	</a>
																</li>
																<?php
															} ?>
															<li class="">
																<a href="<?php echo get_term_link($cat); ?>">
																	<?php echo $cat->name; ?>
																</a>
															</li>
															<?php
														}
													$catCycle++;
												}
											}
											?>
											<li>
												<span><?php echo esc_html($listingcurrency.$listingprice); ?></span>
											</li>
											<li>
												<span class="lp-list-sp-icon">
													<i class="fa fa-calendar"></i>
												</span>
												<span class="lp-list-sp-text">
													<?php the_time( get_option( 'date_format' ) ); ?>
												</span>
											</li>
											<li>
												<span class="lp-list-sp-icon">
													<i class="fa fa-clock-o"></i>
												</span>
												<span class="lp-list-sp-text">
													<?php 
													if(!empty($plan_time)){
														$startdate = get_the_time('d-m-Y');

														$endDate = date('d-m-Y', strtotime($startdate. ' + '.$plan_time.' days'));		
														$diff = (strtotime($endDate) - time()) / 60 / 60 / 24;

														if ($diff < 1 && $diff > 0) {
															$days = 1;
														} else {
															$days = floor($diff);
														}
													}else{
														$days = esc_html__('Unlimited','listingpro');
													}
														echo esc_html($days).esc_html__('  Days Left','listingpro');
													?>
												</span>
											</li>
											<li>
												<span class="lp-list-sp-icon">
													<i class="fa fa-check"></i>
												</span>
												<span class="lp-list-sp-text">
													<?php
														if(get_post_status() == 'publish'){
															echo esc_html__('Published', 'listingpro');
														}elseif(get_post_status() == 'pending'){
															echo esc_html__('Pending', 'listingpro');
														}
													?>
												</span>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-md-5 col-sm-5 col-xs-12 pull-right padding-0">
								<div class="lp-rigt-icons lp-list-view-content-bottom lp-all-listing-action-btns lp-list-view-content-bottom-outer">
									<?php
										global $post;
										echo listingpro_post_confirmation($post);
									?>
									<ul class="lp-list-view-edit list-style-none aliceblue">
										<li><a href="#" data-modal="modal-<?php echo esc_attr($postID); ?>" class="md-trigger"><i class="fa fa-times"></i><span><?php echo esc_html__('Remove', 'listingpro'); ?></span></a></li>
										<li><?php echo listingpro_change_plan_button($post, '');?></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="md-modal md-effect-3" id="modal-<?php echo esc_attr($postID); ?>">
						<div class="md-content">
							<form class="form-horizontal"  method="post">
								<div class="form-group mr-bottom-0">
									<h3><?php echo esc_html__( 'Are you sure you want to delete this?', 'listingpro' ); ?></h3>
									<a href="#" class="md-close" data-postid="<?php echo esc_attr($postID); ?>">
										<?php echo esc_html__( 'No', 'listingpro' ); ?>
									</a>
									<input type="submit" value="<?php echo esc_html__( 'Yes', 'listingpro' ); ?>" class="lp-review-btn btn-second-hover">
									<input name="removeid" type="hidden" value="<?php echo esc_attr($postID); ?>" />
								</div>
							</form>	
						</div>
					</div>
			
					<?php
				endwhile;
				echo listingpro_pagination($listingsP_query);
			}else{
				?>
				<div class="text-center no-result-found col-md-12 col-sm-6 col-xs-12 margin-bottom-30">
					<h1><?php esc_html_e('Ooops!','listingpro'); ?></h1>
					<p><?php esc_html_e('Sorry ! You have no listing yet!','listingpro'); ?></p>
				</div>
				<?php
			}	
		?>
	</div>
</div>
<!-- /drafed listings -->