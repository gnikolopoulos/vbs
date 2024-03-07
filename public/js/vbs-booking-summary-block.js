document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('a.back').addEventListener("click", (event) => {
    event.preventDefault();

    history.back();
  });

  document.querySelector('a.book_now_btn').addEventListener("click", (event) => {
    event.preventDefault();

    const urlParams = new URLSearchParams(window.location.search)
    const data = new FormData();
          data.append( 'action', 'booking_summary' );
          data.append( 'nonce', event.target.dataset.nonce );
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