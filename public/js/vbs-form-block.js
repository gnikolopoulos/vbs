document.addEventListener('DOMContentLoaded', () => {
	const flatpickrOptions = {
		altInput: true,
		enableTime: true,
		minDate: new Date(),
		dateFormat: 'Y-m-d H:i:S',
		minuteIncrement: 15,
	}

	// Init the date/time picker
  flatpickr('#pickup_datetime', flatpickrOptions);
  flatpickr('#return_datetime', flatpickrOptions);

  // Event for switching between location and address
  document.querySelector('.pickup.switch').addEventListener("click", (event) => {
  	event.preventDefault();

  	toggle(document.querySelector('input#pickup'));
  	toggle(document.querySelector('select#pickup'));

  	document.getElementById('pickup_type').value = document.querySelector('input#pickup').style.display != 'none' ? 'address' : 'location';

  	document.querySelector('.pickup.switch span:first-child').classList.toggle('active');
  	document.querySelector('.pickup.switch span:last-child').classList.toggle('active');
  });

  // Event for switching between location and address
  document.querySelector('.dropoff.switch').addEventListener("click", (event) => {
  	event.preventDefault();

  	toggle(document.querySelector('input#dropoff'));
  	toggle(document.querySelector('select#dropoff'));

  	document.getElementById('dropoff_type').value = document.querySelector('input#dropoff').style.display != 'none' ? 'address' : 'location';

  	document.querySelector('.dropoff.switch span:first-child').classList.toggle('active');
  	document.querySelector('.dropoff.switch span:last-child').classList.toggle('active');
  });

  // AJAX call on button click
  document.querySelector('button.submit').addEventListener("click", (event) => {
  	event.preventDefault();

  	let isPickupLocationValid = checkPickupLocation(),
        isPickupDateValid = checkPickupDate(),
        isDropoffLocationValid = checkDropoffLocation(),
        isReturnDateValid = checkReturnDate(),
        idPassengersValid = checkPassengers();

    let isFormValid = isPickupLocationValid &&
        isPickupDateValid &&
        isDropoffLocationValid &&
        isReturnDateValid &&
        idPassengersValid;

    if (!isFormValid) {
    	return false;
    }

  	const data = new FormData( document.getElementById('vbs_booking_form') );
  				data.append( 'action', 'initiate_search' );
  				data.append( 'nonce', document.getElementById('nonce').value );

  	fetch(wp_ajax.ajaxurl, {
	    method: 'post',
	    body: data,
		})
		.then(response => response.json())
		.then((response) => {
			if (response.result) {
				window.location.href = response.redirect;
			}
		}).catch((error) => {
	    console.log(error)
		});
  });
});

// Toggle visibility of an elemenr
function toggle(e) {
	e.style.display = ((e.style.display != 'none') ? 'none' : 'block');
};

// Form Validation
const checkPassengers = () => {
  const min = 1;
  const max = 10;
  let passengers = document.getElementById('passengers');

  if (!isBetween(passengers.value, min, max)) {
    showError(passengers, `Passengers should be between ${min} and ${max}.`);
    return false;
  }

  showSuccess(passengers);
  return true;
};

const checkPickupDate = () => {
  let pickup_datetime = document.getElementById('pickup_datetime');

  if (!isDateValid(pickup_datetime.value)) {
    showError(pickup_datetime, `There pickup date is invalid.`);
    return false;
  }

  showSuccess(pickup_datetime);
  return true;
};

const checkReturnDate = () => {
  let return_datetime = document.getElementById('return_datetime');

  if (return_datetime.value == '') {
  	showSuccess(return_datetime);
  	return true;
  }

  if (!isDateValid(return_datetime.value)) {
    showError(return_datetime, `There return date is invalid.`);
    return false;
  }

  showSuccess(return_datetime);
  return true;
};

const checkPickupLocation = () => {
	const pickup_type = document.getElementById('pickup_type');

	if (pickup_type.value == 'location' && document.querySelector('select#pickup').value == '') {
		showError(pickup_type, `No location specified`);
		return false;
	}

	if (pickup_type.value == 'address' && document.querySelector('input#pickup').value == '') {
		showError(pickup_type, `No location specified`);
		return false;
	}

	showSuccess(pickup_type);
	return true;
};

const checkDropoffLocation = () => {
	const dropoff_type = document.getElementById('dropoff_type');

	if (dropoff_type.value == 'location' && document.querySelector('select#dropoff').value == '') {
		showError(dropoff_type, `No location specified`);
		return false;
	}

	if (dropoff_type.value == 'address' && document.querySelector('input#dropoff').value == '') {
		showError(dropoff_type, `No location specified`);
		return false;
	}

	showSuccess(dropoff_type);
	return true;
};

const isBetween = (num, min, max) => num < min || num > max ? false : true;
const isDateValid = (date) => !isNaN(new Date(date));

const showError = (input, message) => {
  // get the form-field element
  const formField = input.parentElement;
  // add the error class
  formField.classList.remove('success');
  formField.classList.add('error');

  // show the error message
  const error = formField.querySelector('small');
  error.textContent = message;
};

const showSuccess = (input) => {
  // get the form-field element
  const formField = input.parentElement;

  // remove the error class
  formField.classList.remove('error');
  formField.classList.add('success');

  // hide the error message
  const error = formField.querySelector('small');
  error.textContent = '';
};