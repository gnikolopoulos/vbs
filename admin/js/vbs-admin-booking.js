document.addEventListener('DOMContentLoaded', () => {
  (async () => {
    const element = await waitForElement("#map");
    window.initMap = initMap();
  })();
});

async function waitForElement(selector) {
  while (!document.querySelector(selector)) {
    await new Promise(resolve => requestAnimationFrame(resolve));
  }
  return document.querySelector(selector);
}

function initMap() {
  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer();
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 7,
    center: { lat: 41.85, lng: -87.65 },
  });

  directionsRenderer.setMap(map);

  calculateAndDisplayRoute(directionsService, directionsRenderer);
}

function calculateAndDisplayRoute(directionsService, directionsRenderer) {
  directionsService
    .route({
      origin: document.getElementsByName("carbon_fields_compact_input[_pickup_address_coordinates]")[0].value,
      destination: document.getElementsByName("carbon_fields_compact_input[_dropoff_address_coordinates]")[0].value,
      travelMode: google.maps.TravelMode.DRIVING,
    })
    .then((response) => {
      directionsRenderer.setDirections(response);
    })
    .catch((e) => window.alert("Directions request failed due to " + status));
}