 // Get the modal
 var modal = document.getElementById("myModal");

 // Get the button that opens the modal
 var btn = document.getElementById("myBtn");

 // Get the <span> element that closes the modal
 var span = document.getElementsByClassName("close")[0];

 // Get the modal-footer element that closes the modal
 var closeFooter = document.getElementsByClassName("close-footer")[0];

 // When the user clicks the button, open the modal 
 btn.onclick = function () {
     modal.style.display = "block";
 }

 // When the user clicks on <span> (x), close the modal
 span.onclick = function () {
     modal.style.display = "none";
 }

 // When the user clicks on modal-footer (Cerrar), close the modal
 closeFooter.onclick = function () {
     modal.style.display = "none";
 }

 // When the user clicks anywhere outside of the modal, close it
 window.onclick = function (event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 }
  // Copy link function
  function copyLink() {
     var copyText = document.getElementById("inp-link");
     copyText.select();
     copyText.setSelectionRange(0, 99999); /* For mobile devices */
     document.execCommand("copy");
     alert("Link copiado: " + copyText.value);
 }