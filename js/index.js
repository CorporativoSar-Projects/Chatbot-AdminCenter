
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 


    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

   
    var validUsername = "enersavy";
    var validPassword = "1234"; 
  
    if (username === validUsername && password === validPassword) {
        window.location.href = "menu.html"; 
    } else {
       
        document.getElementById('message').textContent = "Ingresa los datos correctos";
        document.getElementById('message').style.color = 'red';
    }
});
