<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
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
