
 var modal = document.getElementById("myModal");
 var btn = document.getElementById("myBtn");
 var span = document.getElementsByClassName("close")[0];


 var closeFooter = document.getElementsByClassName("close-footer")[0];


 btn.onclick = function () {
     modal.style.display = "block";
 }

 
 span.onclick = function () {
     modal.style.display = "none";
 }


 closeFooter.onclick = function () {
     modal.style.display = "none";
 }


 window.onclick = function (event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 }
  
  function copyLink() {
     var copyText = document.getElementById("inp-link");
     copyText.select();
     copyText.setSelectionRange(0, 99999); 
     document.execCommand("copy");
     alert("Link copiado: " + copyText.value);
 }