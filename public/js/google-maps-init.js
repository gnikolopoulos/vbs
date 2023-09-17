const pickup_input = document.getElementById("pickup");
const dropoff_input = document.getElementById("dropoff");

const options = {
  componentRestrictions: { country: "gr" },
  fields: ["geometry"],
};

const autocomplete_pickup = new google.maps.places.Autocomplete(pickup_input, options);
const autocomplete_dropoff = new google.maps.places.Autocomplete(dropoff_input, options);

autocomplete_pickup.addListener("place_changed", () => {
  const place = autocomplete_pickup.getPlace();

  let lat = document.getElementById('pickup_lat');
      lat.value = place.geometry.location.lat();

  let lng = document.getElementById('pickup_lng');
      lng.value = place.geometry.location.lng();
});

autocomplete_dropoff.addListener("place_changed", () => {
  const place = autocomplete_dropoff.getPlace();

  let lat = document.getElementById('dropoff_lat');
      lat.value = place.geometry.location.lat();

  let lng = document.getElementById('dropoff_lng');
      lng.value = place.geometry.location.lng();
});
