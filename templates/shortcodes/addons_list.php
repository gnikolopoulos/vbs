<div class="addon_list_container">
  <?php $nonce = wp_create_nonce('addon_list_nonce'); ?>
  <?php foreach ( $transient_data['available_addons'] as $id => $name ): ?>
    <div class="addon_container" data-id="<?php echo $id; ?>">
      <div class="container_column image">
        <img src="<?php echo get_the_post_thumbnail_url( $id, 'medium' ); ?>" />
      </div>
      <div class="container_column contents">
        <span class="addon_title"><?php echo get_the_title( $id ); ?></span>
        <p class="addon_description"><?php echo get_post_field( 'post_content', $id ); ?></p>
      </div>
      <div class="container_column actions">
        <div>
          <h6 class="book_price">
            <?php echo $helper->formatPrice( (float)carbon_get_post_meta( $id, 'cost' ) ); ?>
          </h6>
        </div>
        <a class="book_now_btn" data-nonce="<?php echo $nonce; ?>" data-addon="<?php echo $id; ?>" href="#"><?php echo __( 'Select', 'vbs' ); ?></a>
      </div>
    </div>
  <?php endforeach; ?>

  <a class="skip_btn" data-nonce="<?php echo $nonce; ?>" data-addon="0" href="#"><?php echo __( 'Skip', 'vbs' ); ?></a>
</div>