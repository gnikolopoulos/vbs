<div class="booking_summary_container">
  <?php $nonce = wp_create_nonce('booking_summary_nonce'); ?>
  <div class="summary_container">
    <h3><?php echo __( 'Pickup', 'vbs' ); ?></h3>
    <ul>
      <li><span><?php echo __( 'Pickup Date/Time', 'vbs' ); ?>:</span> <?php echo date('l, F j Y H:i:s', strtotime($transient_data['pickup_datetime'])); ?></li>
      <li><span><?php echo __( 'Pickup Address', 'vbs' ); ?>:</span> <?php echo $transient_data['pickup']['address']; ?></li>
      <li><span><?php echo __( 'Passengers', 'vbs' ); ?>:</span> <?php echo $transient_data['passengers']; ?></li>
    </ul>

    <h3><?php echo __( 'Dropoff', 'vbs' ); ?></h3>
    <ul>
      <li><span><?php echo __( 'Dropoff Date/Time', 'vbs' ); ?>:</span> <?php echo date('l, F j Y H:i:s', strtotime($transient_data['pickup_datetime'])); ?></li>
      <li><span><?php echo __( 'Dropoff Address', 'vbs' ); ?>:</span> <?php echo $transient_data['pickup']['address']; ?></li>
    </ul>

    <?php if ($transient_data['return_datetime'] != ''): ?>
      <h3><?php echo __( 'Return', 'vbs' ); ?></h3>
      <ul>
        <li><span><?php echo __( 'Return Date/Time', 'vbs' ); ?>:</span> <?php echo date('l, F j Y H:i:s', strtotime($transient_data['return_datetime'])); ?></li>
      </ul>
    <?php endif; ?>

    <h3><?php echo __( 'Trip information', 'vbs' ); ?></h3>
    <ul>
      <li><span><?php echo __( 'Vehicle', 'vbs' ); ?>:</span> <?php echo get_post($transient_data['vehicle'])->post_title; ?></li>
      <li><span><?php echo __( 'Distance', 'vbs' ); ?>:</span> <?php echo $transient_data['distance']/1000; ?>km</li>
      <li><span><?php echo __( 'Addons', 'vbs' ); ?>:</span> <?php echo get_post($transient_data['addon'])->post_title; ?></li>
    </ul>

    <h3><?php echo __( 'Customer information', 'vbs' ); ?></h3>
    <ul>
      <li><span><?php echo __( 'First Name', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['first_name']; ?></li>
      <li><span><?php echo __( 'Last Name', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['last_name']; ?></li>
      <li><span><?php echo __( 'Email', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['email']; ?></li>
      <li><span><?php echo __( 'Phone', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['phone']; ?></li>
      <li><span><?php echo __( 'Mobile', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['mobile']; ?></li>
      <li><span><?php echo __( 'Notes', 'vbs' ); ?>:</span> <?php echo $transient_data['customer']['notes']; ?></li>
    </ul>

    <h3><?php echo __( 'Cost', 'vbs' ); ?></h3>
    <ul class="cost">
      <li>
        <span><?php echo __( 'Vehicle Cost', 'vbs' ); ?>: </span>
        <?php echo $helper->formatPrice((float)$transient_data['vehicle_cost']); ?>
      </li>
      <li>
        <span><?php echo __( 'Addon Cost', 'vbs' ); ?>: </span>
        <?php echo $helper->formatPrice((float)$transient_data['addon_cost']); ?>
      </li>
      <li>
        <span><?php echo __( 'Total', 'vbs' ); ?>: </span>
        <?php echo $helper->formatPrice((float)$transient_data['vehicle_cost'] + (float)$transient_data['addon_cost']); ?>
      </li>
    </ul>
  </div>

  <div class="payment_container">
    <h3><?php echo __('Payment Method', 'vbs' ); ?></h3>
    <div class="payment-methods-container">
      <?php foreach ($helper->getPaymentMethods() as $method): ?>
        <div class="payment-method-info">
          <input type="radio" id="<?php echo $method['id']; ?>" value="<?php echo $method['title']; ?>" name="payment-method">
          <span>
            <?php echo file_get_contents( $method['icon'] ); ?>
          </span>
          <label for="<?php echo $method['id']; ?>" aria-label="<?php echo $method['title']; ?>">
            <?php echo $method['title']; ?>
            <span>
              <?php echo $method['description']; ?>
            </span>
          </label>
        </div>
      <?php endforeach; ?>
    </div>

    <div id="form_container"></div>

    <div class="actions">
      <a class="back" href="#"><?php echo __( '&larr; Back', 'vbs' ); ?></a>
      <a class="book_now_btn" data-nonce="<?php echo $nonce; ?>" href="#"><?php echo __( 'Make Booking', 'vbs' ); ?></a>
    </div>
  </div>
</div>