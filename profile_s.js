document.addEventListener("DOMContentLoaded", () => {
  const updateBtn = document.getElementById("updateProfileBtn");
  const startAssessingBtn = document.getElementById("startAssessingBtn");
  const logoutLink = document.getElementById("logoutLink");

  // Load stored specialist profile data (demo via localStorage)
  const storedProfile = JSON.parse(localStorage.getItem("specialistProfile") || "{}");

  if (storedProfile.fullName) {
    document.getElementById("sFullName").textContent = storedProfile.fullName;
  }
  if (storedProfile.email) {
    document.getElementById("sEmail").textContent = storedProfile.email;
  }
  if (storedProfile.bio) {
    document.getElementById("sBio").textContent = storedProfile.bio;
  }
  if (storedProfile.title) {
    document.getElementById("sTitle").textContent = storedProfile.title;
  }
  if (storedProfile.degrees) {
    document.getElementById("sDegrees").textContent = storedProfile.degrees;
  }
  if (storedProfile.experience) {
    document.getElementById("sExperience").textContent = storedProfile.experience;
  }
  if (storedProfile.skills) {
    document.getElementById("sSkills").textContent = storedProfile.skills;
  }

  updateBtn.addEventListener("click", () => {
    window.location.href = "update_s.html";
  });

  startAssessingBtn.addEventListener("click", () => {
    window.location.href = "assessment.html";
  });

  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      // optional: clear specialist data on logout
      // localStorage.removeItem("specialistProfile");
      window.location.href = "login.html";
    });
  }
});
