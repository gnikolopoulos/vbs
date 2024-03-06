document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.book_now_btn').forEach(button => {
    button.addEventListener("click", (event) => {
      event.preventDefault();

      const urlParams = new URLSearchParams(window.location.search)
      const data = new FormData();
            data.append( 'action', 'select_addon' );
            data.append( 'nonce', event.target.dataset.nonce );
            data.append( 'addon', event.target.dataset.addon );
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