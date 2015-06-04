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
            <div class="subnavigation">
                <?php $show_en_sidebar = get_post_meta(get_the_ID(), 'show_en_sidebar' , true); ?>
                
                <?php if ($show_en_sidebar): ?>
                <?php $args = array('theme_location'=>'en_side_menu', 'container' => ''); ?>
                <?php wp_nav_menu( $args ); ?>
                <?php endif; ?>
            
            </div><!-- end subnavigation -->
        </div><!-- end page-content-wrapper -->
	</div><!-- end page-content -->
    <div class="page-breadcrumb">
        <?php $breadcrumbs = ivd_media_breadcrumbs($post, 'en'); ?>
        <ul>
            <li>You are here: </li>
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
        
        <ul></ul>
        <p>&copy; 2010 IVP - Nienoord 5 - 1112 XE Diemen.</p>
        <span class="arg">
            <a href="http://www.arq.org/" class="external" target="_blank">
                <img src="<?php echo get_template_directory_uri(); ?>/images/logo-arg.gif" alt="Psychotrauma Expert Groep" />
            </a>
        </span>              
    </div>
    <!-- main navigation -->
    <div class="page-navigation">
        <?php $args = array('theme_location'=>'en_top_menu', 'container' => ''); ?>
        <ul>
            <li class="top">
                <ul>
                </ul>
            </li>
            <li>
                <?php wp_nav_menu( $args ); ?> 
            </li>
        </ul>
    </div>		<!-- decorative -->
    <div class="page-decorative">
        <?php the_post_thumbnail('large');?>       
    </div>
            
    <div id="page-lang-selector">
        <ul>
            <li><a class="nl" href="<?php echo home_url( '/' ); ?>" title="Klik voor de nederlandse versie">Nederlandse versie</a></li>
        </ul>
    </div>	
</div><!-- end page container -->

	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.4.2.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/project_scripts.js%3ft=1323698883"></script>

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
