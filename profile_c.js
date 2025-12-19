document.addEventListener("DOMContentLoaded", () => {
  const updateBtn = document.getElementById("updateProfileBtn");

  // TODO: replace with real data loading (localStorage / API)
  const storedProfile = JSON.parse(localStorage.getItem("clientProfile") || "{}");

  if (storedProfile.fullName) {
    document.getElementById("pFullName").textContent = storedProfile.fullName;
  }
  if (storedProfile.email) {
    document.getElementById("pEmail").textContent = storedProfile.email;
  }
  if (storedProfile.university) {
    document.getElementById("pUniversity").textContent = storedProfile.university;
  }
  if (storedProfile.major) {
    document.getElementById("pMajor").textContent = storedProfile.major;
  }
  if (storedProfile.minor) {
    document.getElementById("pMinor").textContent = storedProfile.minor;
  }
  if (storedProfile.cgpa) {
    document.getElementById("pCgpa").textContent = storedProfile.cgpa;
  }
  if (storedProfile.gradDate) {
    document.getElementById("pGradDate").textContent = storedProfile.gradDate;
  }
  if (storedProfile.skills) {
    document.getElementById("pSkills").textContent = storedProfile.skills;
  }
  if (storedProfile.achievements) {
    document.getElementById("pAchievements").textContent = storedProfile.achievements;
  }
  if (storedProfile.certs) {
    document.getElementById("pCerts").textContent = storedProfile.certs;
  }

  updateBtn.addEventListener("click", () => {
    window.location.href = "update_c.html";
  });
});
