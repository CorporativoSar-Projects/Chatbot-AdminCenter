// chat-toggle.js
(function() {
    // Crear el toggle
    const toggle = document.createElement('div');
    toggle.id = 'chatbot-toggle';
    toggle.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 90px;
        height: 90px;
        background: #002B45;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        z-index: 9999;
        /* background: linear-gradient(135deg,#007bff,#00c6ff);*/
    `;
    toggle.innerHTML = `<img src="img/Logo_cabeza.svg" alt="Chat" style="width:60px;">`;

    /*
    <div class="rectangulo-container">
    <img
      src="img/LOGOTIPO_IXAH-02.png"
      width="70px"
      alt="Logo"
      class="img-logo-chiq" />
      <img src="img/Logo_cabeza.svg" alt="Chat" style="width:40px;">
    </div>*/

    // Animación hover
    toggle.addEventListener('mouseenter', () => {
        toggle.style.transform = 'scale(1.1)';
        toggle.style.boxShadow = '0 12px 25px rgba(0,0,0,0.4)';
    });
    toggle.addEventListener('mouseleave', () => {
        toggle.style.transform = 'scale(1)';
        toggle.style.boxShadow = '0 8px 20px rgba(0,0,0,0.3)';
    });

    // Click redirecciona
    toggle.addEventListener('click', () => {
        // window.location.href = 'chatbot.php'; // cambia por tu URL
        window.location.href = 'copia_chatbot.php'; // cambia por tu URL
    });

    document.body.appendChild(toggle);
})();

