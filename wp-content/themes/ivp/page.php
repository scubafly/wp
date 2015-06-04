<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage IVP
 * @since IVP 1.0
 */
get_header(); ?>
        <div class="content" role="main">
			<?php
			/* Run the loop to output the page.
			 * If you want to overload this in a child theme then include a file
			 * called loop-page.php and that will be used instead.
			 */
			get_template_part( 'loop', 'page' );
			?>			
		</div><!-- end content -->
<?php if (ivp_is_en(get_the_ID())): ?>
<?php get_footer('en'); ?>
<?php else: ?>
<?php get_footer(); ?>
<?php endif; ?>
