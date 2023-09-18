document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.book_now_btn').forEach(button => {
    button.addEventListener("click", (event) => {
      event.preventDefault();

      const urlParams = new URLSearchParams(window.location.search)
      const data = new FormData();
            data.append( 'action', 'select_vehicle' );
            data.append( 'nonce', this.dataset.nonce );
            data.append( 'distance', this.dataset.dist );
            data.append( 'vehicle', this.dataset.vehicle );
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
});