<?php foreach ($news as $news) : ?>
    <p><?php echo $news['post_date']; ?>
    <?php if ($news['target']): ?>
    <a title="<?php echo $news['post_title']; ?>" href="<?php echo $news['post_link']; ?>" target="_blank"><?php echo $news['post_title']; ?></a>
    <?php else: ?>
    <a title="<?php echo $news['post_title']; ?>" href="<?php echo $news['post_link']; ?>"><?php echo $news['post_title']; ?></a>
    <?php endif; ?>
    </p>
<?php endforeach; ?>