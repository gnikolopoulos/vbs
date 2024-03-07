document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('a.back').addEventListener("click", (event) => {
    event.preventDefault();

    history.back();
  });

  document.querySelector('a.book_now_btn').addEventListener("click", (event) => {
    event.preventDefault();
  });
});