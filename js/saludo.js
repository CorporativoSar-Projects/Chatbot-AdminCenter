document.addEventListener('DOMContentLoaded', () => {
    let saludo = localStorage.getItem('inp_saludo') || window.chatbotData?.saludo;
    if (saludo) {
        localStorage.setItem('inp_saludo', saludo);
        const divCopiaSaludo = document.getElementById('txt-chatbot');
        if (divCopiaSaludo) divCopiaSaludo.textContent = saludo;
    }

});
