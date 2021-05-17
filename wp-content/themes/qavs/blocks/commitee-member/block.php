<div class="commitee-member <?php block_field('className'); ?>">
  <img src="<?php block_field( 'picture' ); ?>" alt="<?php block_field( 'name' ); ?>" class="commitee-member__image" />
  <div class="commitee-member__details">
    <h4 class="commitee-member__name"><?php block_field( 'name' ); ?></h4>
    <dl class="commitee-member__detail">
      <dt>Areas of interest</dt>
      <dd><?php block_field('areas-of-interest'); ?></dd>
    </dl>
    <dl class="commitee-member__detail">
      <dt>About</dt>
      <dd><?php block_field('about'); ?></dd>
    </dl>
  </div>
</div>
