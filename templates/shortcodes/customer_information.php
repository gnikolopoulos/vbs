<div class="booking_form_container">
  <form id="vbs_customer_info_form" class="customer_info_form_container">
    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('customer_info_nonce'); ?>" />

    <div class="container_row">
      <div class="container_column">
        <fieldset>
          <label for="first_name"><?php _e( 'First Name', 'vbs' ); ?></label>
          <input type="text" class="form-input" name="first_name" id="first_name" />
          <small></small>
        </fieldset>
      </div>

      <div class="container_column">
        <fieldset>
          <label for="last_name"><?php _e( 'Last Name', 'vbs' ); ?></label>
          <input type="text" class="form-input" name="last_name" id="last_name" />
          <small></small>
        </fieldset>
      </div>
    </div>

    <div class="container_row">
      <div class="container_column">
        <fieldset>
          <label for="email"><?php _e( 'Email Address', 'vbs' ); ?></label>
          <input type="text" class="form-input" name="email" id="email" />
          <small></small>
        </fieldset>
      </div>
    </div>

    <div class="container_row">
      <div class="container_column">
        <fieldset>
          <label for="phone"><?php _e( 'Phone', 'vbs' ); ?></label>
          <input type="text" class="form-input" name="phone" id="phone" />
          <small></small>
        </fieldset>
      </div>

      <div class="container_column">
        <fieldset>
          <label for="mobile"><?php _e( 'Mobile', 'vbs' ); ?></label>
          <input type="text" class="form-input" name="mobile" id="mobile" />
          <small></small>
        </fieldset>
      </div>
    </div>

    <div class="container_row">
      <div class="container_column">
        <fieldset>
          <label for="notes"><?php _e( 'Notes', 'vbs' ); ?></label>
          <textarea rows="6" class="form-input" name="notes" id="notes"></textarea>
          <small></small>
        </fieldset>
      </div>
    </div>

    <div class="container_row">
      <div class="container_column">
        <button class="submit back" type="submit"><?php _e( '&larr; Back', 'vbs' ); ?></button>
      </div>

      <div class="container_column last">
        <button class="submit next" type="submit"><?php _e( 'Next &rarr;', 'vbs' ); ?></button>
      </div>
    </div>
  </form>
</div>