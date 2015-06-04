<ul class="medewerkers clearfix">
    <?php foreach ($results as $result) : ?>
    <li>
        <?php echo wp_get_attachment_image($result['ID'], 'full'); ?>
        
        <p><?php echo $result['caption']; ?></p>
        <?php if(ivp_is_en(get_the_ID())): ?>
        <p><?php echo $result['function-en']; ?></p>
        <?php else: ?>
        <p><?php echo $result['function-nl']; ?></p>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>    
  
