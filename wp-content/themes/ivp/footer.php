<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage IVP
 * @since IVP 1.0
 */
?>
            <div class="information">
                <?php ivp_call_me_back(); ?>

                <?php dynamic_sidebar( 'primary-widget-area' ); ?>
			</div><!-- end information -->

            <div class="subnavigation">
                <?php $args = array('theme_location'=>'nl_side_menu', 'container' => ''); ?>
                <?php wp_nav_menu( $args ); ?>

                <ul class="partnerList">
                    <?php dynamic_sidebar('secondary-widget-area'); ?>
                </ul>
            </div><!-- end subnavigation -->
        </div><!-- end page-content-wrapper -->
	</div><!-- end page-content -->
    <div class="page-breadcrumb">
        <?php $breadcrumbs = ivd_media_breadcrumbs($post); ?>
        <ul>
            <li>U bent hier: </li>
            <?php $i=0; foreach ($breadcrumbs as $breadcrumb): ?>
            <?php if ($i+1 == count($breadcrumbs)): ?>
            <li><a href="<?php echo $breadcrumb['link']; ?>" class="active"><?php echo $breadcrumb['post_title']; ?></a></li>
            <?php else: ?>
            <li><a href="<?php echo $breadcrumb['link']; ?>"><?php echo $breadcrumb['post_title']; ?></a></li>
            <?php endif; ?>
            <?php $i++; endforeach; ?>
        </ul>
    </div>
    <!-- footer -->
    <div class="page-footer">
        <?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
        <span class="arg">
            <a href="http://www.arq.org/" class="external" target="_blank">
                <img src="<?php echo get_template_directory_uri(); ?>/images/logo-arg.gif" alt="Psychotrauma Expert Groep" />
            </a>
        </span>
    </div>

    <?php
        $menu_name = 'nl_top_menu';
        $menu_links = array();
        if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
            $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
            $menu_links = wp_get_nav_menu_items($menu->term_id);
        }
    ?>

    <!-- main navigation -->
    <div class="page-navigation">
        <ul>
            <li class="top">
                <ul>
                    <?php for ($i = 0; $i < count($menu_links); $i++): $menu_link = $menu_links[$i]; ?>
                    <?php if ($menu_link->ID == $post->ID): ?>
                    <li><a href="<?php echo $menu_link->url; ?>" class="active"><?php echo $menu_link->title; ?></a></li>
                    <?php else: ?>
                    <li><a href="<?php echo $menu_link->url; ?>"><?php echo $menu_link->title; ?></a></li>
                    <?php endif; ?>
                    <?php if ($i == 3) break; ?>
                    <?php endfor; ?>
                </ul>
            </li>
            <li>
                <ul>
                <?php if (isset($menu_links[4])): ?>
                <?php for ($i = 4; $i < count($menu_links); $i++): $menu_link = $menu_links[$i]; ?>
                    <?php if ($menu_link->ID == $post->ID): ?>
                    <li><a href="<?php echo $menu_link->url; ?>" class="active"><?php echo $menu_link->title; ?></a></li>
                    <?php else: ?>
                    <li><a href="<?php echo $menu_link->url; ?>"><?php echo $menu_link->title; ?></a></li>
                    <?php endif; ?>
                    <?php endfor; ?>
                <?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>		<!-- decorative -->
    <div class="page-decorative">
        <?php the_post_thumbnail('large');?>
    </div>

    <div id="page-lang-selector">
        <ul>
            <li><a class="en" title="Click for the english version" href="<?php echo home_url( '/en' ); ?>">English version</a></li>
        </ul>
    </div>
</div><!-- end page container -->

	
	<script src="<?php echo get_template_directory_uri(); ?>/js/project_scripts.js%3ft=1323698883"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/thickbox/thickbox.js"></script>

	<script type="text/javascript">

		if ( $('h2') != null )
		{
			Cufon.replace('h2');
		}

		if ( $('h3') != null )
		{
			Cufon.replace('h3');
		}

		if ( $('.page-footer ul li a') != null )
		{
			Cufon.replace('.page-footer ul li a');
		}
		if ( $('.subnavigation ul li a') != null )
		{
			Cufon.replace('.subnavigation ul li a');
		}

	</script>

	<script type="text/javascript">

		Cufon.now();

	</script>



<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
