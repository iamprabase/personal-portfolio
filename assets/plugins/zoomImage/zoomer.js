var modal = document.getElementById("myModal");
var modalImg = document.getElementById("img01");

$('.display-imglists').on('click', function () {
  modal.style.display = "block";
  if (this.src) {
    modalImg.src = this.src;
  } else {
    modalImg.src = this.data('src');
  }
});

$('.close').on('click', function () {
  modal.style.display = "none";
});

$(document).on('click', '.display-imglists', function () {
  modal.style.display = "block";
  modalImg.src = $(this).attr('src');
});