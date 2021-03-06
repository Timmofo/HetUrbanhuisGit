<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Get Head file -->
<?php get_template_part('template-parts/header/header') ?>

<!-- Get main Navbar -->
<?php get_template_part('template-parts/navbar/navbarmain') ?>

<!-- Pagina content -->
<div class="banner__green"> 
    <div class="container">
        <div class="flex-container banner__green__content">
            <div class="banner__green__text"><br></div>
        </div>
    </div>
</div>

<div class="container containerpadding">
	<div class="row d-flex">
		<div class="col-12 col-lg-3 p-0">
			<h1 class="d-none d-lg-block col-12 p-0 p-lg-0 px-md-3 my-3">Shop</h1>
			<?php
				//parent category inputs
				$prodcat_featured = get_term_by('name', 'featured', 'product_cat');
				$prodcat_homepage = get_term_by('name', 'homepage', 'product_cat');

				$args = array(
					'taxonomy'=>'product_cat',
					'hide_empty'=>false,
					'parent'=>0,
					'exclude'=> array($prodcat_featured->term_id, $prodcat_homepage->term_id)
				);
				
				//spacing flag
				$first_item = 1;

				$all_categories = get_terms( $args );
				foreach($all_categories as $parent_category){
					$category_id = $parent_category->term_id;
					?>
					<div class="shopnavigation__category">
						<h2 class="shopnavigation__header text-lg-left d-none d-lg-block"><?php echo $parent_category->name; ?></h2><?php

						//child category inputs
						$child_args = array(
							'taxonomy'=>'product_cat',
							'hide_empty'=>false,
							'parent'=>$category_id
						);

						$sub_cats = get_terms($child_args);

						?>
						<ul class="shopnavigation__items">
						<?php

						foreach($sub_cats as $child_category){
							?><li class="shopnavigation__items text-lg-left d-none d-lg-block"><a href="<?php echo get_category_link($child_category->term_id); ?>"><?php echo $child_category->name; ?></a></li><?php
						}

						$first_item=0;
						?>
						</ul>
					</div>
					<?php
				}
			?>
		</div>
		<div class="col-12 col-lg-9 p-0">
			<h1 class="text-center text-lg-left my-3">Productcategorie: <?php woocommerce_page_title(); ?></h1>
			<div class="row d-flex">

				<?php
				if ( woocommerce_product_loop() ) {

					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					?>
					<div class="text-left pl-3 pl-lg-0 mb-4 mb-lg-0"> 
						<?php do_action( 'woocommerce_before_shop_loop' ); ?>
					</div>
					<?php
					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action( 'woocommerce_shop_loop' );
							
							$price = get_post_meta( get_the_ID() , '_regular_price', true);
							$saleprice = get_post_meta( get_the_ID() , '_sale_price', true);

                            //Calculate the date and see what badge is required.
                            $postdate = new DateTime( get_the_time(('c'), get_the_ID()) );
                            $now = new DateTime(date('c'));
                            $inventoryTime = $postdate->diff($now)->format('%a');                 
                            $class = '';

                            if($saleprice>0){
                                $class = 'product__sale';
                            } 
                            else if((int)$inventoryTime<30){
                                $class = 'product__new';
                            }

							?>
								<div class="shopcontent__container col-12 col-md-6 col-lg-4 pl-lg-0 px-3 mt-lg-5">                   
									<a class="<?php echo $class ?>" href="<?php the_permalink(); ?>">
										<img class="shopcontent__image" src="<?php the_post_thumbnail_url(); ?>">
										<div class="shopcontent__information shopcontent__information<?php echo $count ?>">
											<p class="shopcontent__name"><?php the_title_attribute();?></p>
											<p class="shopcontent__tag">Zaden</p>
											<?php 
												if ($saleprice == 0){
											?>
													<p class="shopcontent__price"><?php echo wc_price($price); ?></p>
											<?php
												} else {
											?>        
													<p class="shopcontent__price">
														<span class="price__oldprice"><?php echo wc_price($price); ?></span>
														<span class="price__sale"><?php echo wc_price($saleprice); ?></span>
													</p>                      
											<?php
												}
											?>
										</div>
									</a>
								</div> 
							<?php
						}
					}

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );
				}
				?>
			
			</div>
		</div>
	</div>
</div>

<!-- Get Footer file -->
<?php get_template_part('template-parts/footer/footer') ?>
