document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('button.back').addEventListener("click", (event) => {
    event.preventDefault();

    history.back();
  });

  document.querySelector('button.next').addEventListener("click", (event) => {
    event.preventDefault();

    let isEmailValid = checkEmail(),
        isFirstNameValid = checkFirstName(),
        isLastNameValid = checkLastName(),
        isPhoneValid = checkPhone(),
        isMobileValid = checkMobile();

    let isFormValid = isEmailValid &&
      isFirstNameValid &&
      isLastNameValid &&
      isPhoneValid &&
      isMobileValid;

    if (!isFormValid) {
      return false;
    }

    const urlParams = new URLSearchParams(window.location.search)
    const data = new FormData( document.getElementById('vbs_customer_info_form') );
          data.append( 'action', 'customer_data' );
          data.append( 'nonce', document.getElementById('nonce').value );
          data.append( 'search', urlParams.get( 'search' ) );

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

const isEmailValid = (email) => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
const isPhoneValid = (phone) => /^(\+?|0{2})?([0-9]{2})?([0-9]{10})$/.test(phone);
const isEmpty = (str) => (!str?.length);

const checkEmail = () => {
  const email = document.getElementById('email');

  if (isEmpty(email.value)) {
    showError(email, 'Field is required');
    return false;
  }

  if (!isEmailValid(email.value)) {
    showError(email, 'Please enter a valid email address');
    return false;
  }

  showSuccess(email);
  return true;
};

const checkFirstName = () => {
  const name = document.getElementById('first_name');

  if (isEmpty(name.value)) {
    showError(name, 'Field is required');
    return false;
  }

  showSuccess(name);
  return true;
};

const checkLastName = () => {
  const name = document.getElementById('last_name');

  if (isEmpty(name.value)) {
    showError(name, 'Field is required');
    return false;
  }

  showSuccess(name);
  return true;
};

const checkPhone = () => {
  const phone = document.getElementById('phone');

  if (isEmpty(phone.value)) {
    showError(phone, 'Field is required');
    return false;
  }

  if (!isPhoneValid(phone.value)) {
    showError(phone, 'Please enter a valid phone number');
    return false;
  }

  showSuccess(phone);
  return true;
};

const checkMobile = () => {
  const phone = document.getElementById('mobile');

  if (isEmpty(phone.value)) {
    showError(phone, 'Field is required');
    return false;
  }

  if (!isPhoneValid(phone.value)) {
    showError(phone, 'Please enter a valid phone number');
    return false;
  }

  showSuccess(phone);
  return true;
};

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