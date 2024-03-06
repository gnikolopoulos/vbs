<div class="vehicle_list_container">
  <?php $nonce = wp_create_nonce('vehicle_list_nonce'); ?>
  <?php foreach ( $transient_data['available_cars'] as $id ): ?>
    <div class="vehicle_container" data-id="<?php echo $id; ?>">
      <div class="container_column image">
        <img src="<?php echo get_the_post_thumbnail_url( $id, 'medium' ); ?>" />
      </div>
      <div class="container_column contents">
        <span class="vehicle_title"><?php echo get_the_title( $id ); ?></span>
        <p class="vehicle_description"><?php echo get_post_field( 'post_content', $id ); ?></p>
        <div class="vehicle_bottom">
          <div class="vehicle_details">
            <span class="seats">
              <?php echo file_get_contents( VBS_BASE_PATH . 'public/img/icons/seats.svg' ); ?>
              <span><?php echo carbon_get_post_meta( $id, 'seats' ); ?></span>
            </span>
            <span class="luggage">
              <?php echo file_get_contents( VBS_BASE_PATH . 'public/img/icons/luggage.svg' ); ?>
              <span><?php echo carbon_get_post_meta( $id, 'luggage' ); ?></span>
            </span>
            <span class="doors">
              <?php echo file_get_contents( VBS_BASE_PATH . 'public/img/icons/doors.svg' ); ?>
              <span><?php echo carbon_get_post_meta( $id, 'doors' ); ?></span>
            </span>
          </div>

          <div class="vehicle_extras">
            <?php $all_features = carbon_get_theme_option( 'features' ); ?>
            <?php foreach ( carbon_get_post_meta( $id, 'features' ) as $feature_id ): ?>
              <?php echo wp_get_attachment_image( $all_features[$feature_id]['icon'], [32, 32], true, ['alt' => $all_features[$feature_id]['name']] ); ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="container_column actions">
        <div>
          <h6 class="book_price">
            <?php echo $helper->formatPrice($helper->calculatePrice( $id, $distance )); ?>
          </h6>
          <span class="distance"><?php _e( 'Total Distance:', 'vbs' ); ?> <?php echo number_format($distance/1000, 2); ?>km</span>
        </div>
        <a class="book_now_btn" data-nonce="<?php echo $nonce; ?>" data-vehicle="<?php echo $id; ?>" data-dist="<?php echo $distance; ?>" href="#"><?php echo __( 'Book Now', 'vbs' ); ?></a>
      </div>
    </div>
  <?php endforeach; ?>
</div>