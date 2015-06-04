<?php foreach ($vacatures as $vacature) : ?>
    <p><strong class="title"><?php echo $vacature['post_title']; ?></strong><br /> 
    <a title="<?php echo $vacature['post_title']; ?>" href="<?php echo $vacature['post_link']; ?>">Lees de volledige vacature</a></p>
<?php endforeach; ?>