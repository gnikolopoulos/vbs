<?php
  $helper = new Vbs_Helper( $this->plugin_name, $this->version );
?>
<form id="vbs_booking_form" class="booking_form_container">
  <input type="hidden" name="uuid" id="uuid" value="<?php echo wp_generate_uuid4(); ?>" />
  <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('initiate_search_nonce'); ?>" />
  <div class="container_row">
    <div class="container_column">
      <fieldset>
        <label for="pickup"><?php _e( 'Pickup', 'vbs' ); ?></label>
        <a href="#" class="pickup switch"><span class="active"><?php _e( 'Address', 'vbs' ); ?></span><span><?php _e( 'Location', 'vbs' ); ?></span></a>
        <input type="text" class="form-input" name="pickup" id="pickup" placeholder="<?php _e( 'Type address here...', 'vbs' ); ?>" />
        <select style="display: none;" class="form-input" name="pickup_location" id="pickup">
          <option value="">...</option>
          <?php foreach($helper::getLocations() as $id => $name): ?>
            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="pickup_type" id="pickup_type" value="address" />
        <input type="hidden" name="pickup_lat" id="pickup_lat" value="" />
        <input type="hidden" name="pickup_lng" id="pickup_lng" value="" />
        <small></small>
      </fieldset>
    </div>

    <div class="container_column">
      <fieldset>
        <label for="dropoff"><?php _e( 'Dropoff', 'vbs' ); ?></label>
        <a href="#" class="dropoff switch"><span class="active"><?php _e( 'Address', 'vbs' ); ?></span><span><?php _e( 'Location', 'vbs' ); ?></span></a>
        <input type="text" class="form-input" name="dropoff" id="dropoff" placeholder="<?php _e( 'Type address here...', 'vbs' ); ?>" />
        <select style="display: none;" class="form-input" name="dropoff_location" id="dropoff">
          <option value="">...</option>
          <?php foreach($helper::getLocations() as $id => $name): ?>
            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="dropoff_type" id="dropoff_type" value="address" />
        <input type="hidden" name="dropoff_lat" id="dropoff_lat" value="" />
        <input type="hidden" name="dropoff_lng" id="dropoff_lng" value="" />
        <small></small>
      </fieldset>
    </div>
  </div>
  <div class="container_row">
    <div class="container_column">
      <fieldset>
        <label for="pickup_datetime"><?php _e( 'Pickup Date / Time', 'vbs' ); ?></label>
        <input type="text" class="form-input" name="pickup_datetime" id="pickup_datetime" />
        <small></small>
      </fieldset>
    </div>

    <div class="container_column">
      <fieldset>
        <label for="return_datetime"><?php _e( 'Return Date / Time', 'vbs' ); ?></label>
        <input type="text" class="form-input" name="return_datetime" id="return_datetime" />
        <small></small>
      </fieldset>
    </div>

    <div class="container_column">
      <fieldset>
        <label for="passengers"><?php _e( 'Passengers', 'vbs' ); ?></label>
        <input type="number" class="form-input" name="passengers" id="passengers" min="1" max="10" value="1" />
        <small></small>
      </fieldset>
    </div>

    <div class="container_column last">
      <button class="submit" type="submit"><?php _e( 'Go!', 'vbs' ); ?></button>
    </div>
  </div>
</form>