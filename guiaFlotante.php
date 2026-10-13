<style>
#btnGuiaFlotante {
    position: fixed !important;
    bottom: 16px !important;
    left: 16px !important;
    right: auto !important;
    top: auto !important;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #002B45;
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    font-family: 'Montserrat', sans-serif;
    border: none;
    cursor: pointer;
    display: flex !important;
    align-items: center;
    justify-content: center;
    z-index: 99999 !important;
    transition: box-shadow 0.1s ease;
    opacity: 1 !important;
    visibility: visible !important;
}
#btnGuiaFlotante:hover {

    background: #003a5c;
}   
</style>
<button id="btnGuiaFlotante" title="Guía" onclick="activarRecorrido()">?</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('btnGuiaFlotante');
    if (!btn) return;

    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'style') {
                var tourActivo = localStorage.getItem('ixah_tour_activo') === 'true' ||
                                 localStorage.getItem('ixah_plus_tour_activo') === 'true';
                if (btn.style.display === 'none' && !tourActivo) {
                    setTimeout(function() {
                        var sigueActivo = localStorage.getItem('ixah_tour_activo') === 'true' ||
                                          localStorage.getItem('ixah_plus_tour_activo') === 'true';
                        if (!sigueActivo) btn.style.display = 'flex';
                    }, 600);
                }
            }
        });
    });
    observer.observe(btn, { attributes: true, attributeFilter: ['style'] });
});
</script>