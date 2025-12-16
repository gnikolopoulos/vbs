document.addEventListener('DOMContentLoaded', () => {
  var currentPaymentMethod = null;
  var paymentHandlers = {};
      paymentHandlers.cash = {
        confirm: function (nonce) {
          finalizeBooking(nonce);
        }
      };

      paymentHandlers.stripe = {
        confirm: function (nonce) {
          stripe.confirmPayment({
            elements,
            redirect: 'if_required'
          })
          .then(function (result) {
            if (result.error) {
              console.log(result.error.message);
              return;
            }

            if (
              result.paymentIntent &&
              result.paymentIntent.status === 'succeeded'
            ) {
              console.log(result.paymentIntent);
              finalizeBooking(nonce);
            }

            // Async methods will redirect automatically
          });
        }
      };
  var stripe = Stripe(wp_data.stripe_pk);
  var elements = null;
  var paymentElement = null;

  document.getElementById('stripe').addEventListener("click", (event) => {
    currentPaymentMethod = 'stripe';

    if (paymentElement) {
      return;
    }

    const urlParams = new URLSearchParams(window.location.search)
    const data = new FormData();
          data.append( 'action', 'payment_intent' );
          data.append( 'search', urlParams.get( 'search' ) );

    fetch(wp_data.ajaxurl, {
      method: 'post',
      body: data,
    })
    .then(response => response.json())
    .then((response) => {
      if (response.result) {
        elements = stripe.elements({
          clientSecret: response.key
        });
        paymentElement = elements.create('payment');
        paymentElement.mount('#form_container');
      }
    }).catch((error) => {
      console.log(error)
    });
  });

  document.getElementById('cash').addEventListener("click", (event) => {
    currentPaymentMethod = 'cash';

    if (paymentElement) {
      paymentElement.unmount();
      paymentElement = null;
    }
  });

  document.getElementById('paypal').addEventListener("click", (event) => {
    currentPaymentMethod = 'paypal';

    if (paymentElement) {
      paymentElement.unmount();
      paymentElement = null;
    }
  });

  document.querySelector('a.back').addEventListener("click", (event) => {
    event.preventDefault();

    history.back();
  });

  document.querySelector('a.book_now_btn').addEventListener("click", (event) => {
    event.preventDefault();

    if (!currentPaymentMethod) {
      alert('Please select a payment method.');
      return;
    }

    if (!paymentHandlers[currentPaymentMethod]) {
      alert('Invalid payment method.');
      return;
    }

    paymentHandlers[currentPaymentMethod].confirm(event.target.dataset.nonce);
  });

  function finalizeBooking(nonce) {
    const urlParams = new URLSearchParams(window.location.search)
    const data = new FormData();
          data.append( 'action', 'booking_summary' );
          data.append( 'payment_method', document.querySelector('input[name="payment-method"]:checked').value);
          data.append( 'nonce', nonce );
          data.append( 'search', urlParams.get( 'search' ) );

    fetch(wp_data.ajaxurl, {
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
  }
});