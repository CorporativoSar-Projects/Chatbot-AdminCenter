document.addEventListener("DOMContentLoaded", function () {
    const planElements = document.querySelectorAll(".container-plan-precio .container-plan-basico-precio, .container-plan-precio .container-plan-inter-precio, .container-plan-precio .container-plan-avan-precio");
    let selectedPlan = "";
    let selectedPrice = "";

    planElements.forEach(element => {
        element.addEventListener("click", function () {
            const planName = this.closest(".container-plan-precio").querySelector(".nombre-plan-b, .nombre-plan-i, .nombre-plan-a").innerText;
            const priceName = this.querySelector(".nombre-precio-b, .nombre-precio-i, .nombre-precio-a").innerText;
            const priceValue = this.querySelector(".container-precio-b, .container-precio-i, .container-precio-a").innerText;

            selectedPlan = `${planName} - ${priceName}`;
            selectedPrice = priceValue;

            planElements.forEach(el => el.classList.remove("selected"));
            this.classList.add("selected");
        });
    });

    document.getElementById("btnSiguiente").addEventListener("click", function () {
        if (!selectedPlan || !selectedPrice) {
            alert("Por favor, selecciona un plan y un precio.");
            return;
        }

        document.getElementById("selectedPlan").innerText = `Plan: ${selectedPlan}`;
        document.getElementById("selectedPrice").innerText = `Precio: ${selectedPrice}`;

        // Abre el modal solo si se ha seleccionado un plan y un precio
        $('#planModalUnique').modal('show');
    });

    const acceptTermsCheckbox = document.getElementById("acceptTerms");
    const confirmSelectionButton = document.getElementById("confirmSelection");

    acceptTermsCheckbox.addEventListener("change", function () {
        confirmSelectionButton.disabled = !this.checked;
    });

    confirmSelectionButton.addEventListener("click", function () {
        if (!acceptTermsCheckbox.checked) {
            return;
        }

        window.location.href = "https://buy.stripe.com/aEUfZyf1Gf5O5wYeVa";
    });

    // Desmarcar el checkbox y deshabilitar el botón al cerrar el modal
    $('#planModalUnique').on('hidden.bs.modal', function () {
        acceptTermsCheckbox.checked = false;
        confirmSelectionButton.disabled = true;
    });

    // Desmarcar el checkbox y deshabilitar el botón al cargar la página
    acceptTermsCheckbox.checked = false;
    confirmSelectionButton.disabled = true;
});

// Desmarcar el checkbox y deshabilitar el botón al cargar o recargar la página
window.addEventListener("pageshow", function () {
    const acceptTermsCheckbox = document.getElementById("acceptTerms");
    const confirmSelectionButton = document.getElementById("confirmSelection");
    
    if (acceptTermsCheckbox) {
        acceptTermsCheckbox.checked = false;
    }
    if (confirmSelectionButton) {
        confirmSelectionButton.disabled = true;
    }
});
