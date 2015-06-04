<ul class="referenties clearfix">
	<?php foreach ($results as $result) : ?>
        <?php $img = wp_get_attachment_image($result['ID'], 'large'); ?>
		<li><a href="<?php echo $result['redirect_url']; ?>" target="_blank"><?php echo $img; ?></a></li>
	<?php endforeach; ?>
</ul>