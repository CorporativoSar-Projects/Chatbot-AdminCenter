const nextBtns = document.querySelectorAll(".btnNext");
  const prevBtns = document.querySelectorAll(".btnPrev");
  const formSteps = document.querySelectorAll(".form-step");
  const progressSteps = document.querySelectorAll(".progress-step");

  let currentStep = 0;

  function updateStep(step) {
    if (step < 0 || step >= formSteps.length) return;
    formSteps[currentStep].classList.remove("active");
    progressSteps[currentStep].classList.remove("active");
    currentStep = step;
    formSteps[currentStep].classList.add("active");
    progressSteps[currentStep].classList.add("active");
  }

  nextBtns.forEach(btnNext => btnNext.addEventListener("click", () => updateStep(currentStep + 1)));
  prevBtns.forEach(btnPrev => btnPrev.addEventListener("click", () => updateStep(currentStep - 1)));