document.addEventListener('DOMContentLoaded', () => {

  setInterval(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const data = new FormData();
          data.append( 'action', 'check_payment' );
          data.append( 'intent', urlParams.get( 'payment_intent' ) );

    fetch(wp_ajax.ajaxurl, {
      method: 'post',
      body: data,
    })
    .then(response => response.json())
    .then((response) => {
      if (response.result) {
        const urlParams = new URLSearchParams(window.location.search)
        const data = new FormData();
              data.append( 'action', 'booking_summary' );
              data.append( 'payment_method', 'stripe');
              data.append( 'nonce', nonce );
              data.append( 'search', response.session_id );

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
      } else {
        window.location.href = response.redirect;
      }
    }).catch((error) => {
      console.log(error)
    });
  }, 5000);

});