document.addEventListener("DOMContentLoaded", () => {
  const primaryBtn = document.querySelector(".btn-primary");
  const secondaryBtn = document.querySelector(".btn-secondary");

  if (primaryBtn) {
    primaryBtn.addEventListener("click", () => {
      alert("Starting assessment...");
    });
  }

  if (secondaryBtn) {
    secondaryBtn.addEventListener("click", () => {
      alert("Opening profile...");
    });
  }
});
