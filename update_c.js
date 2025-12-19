document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("updateForm");
  const universityInput = document.getElementById("university");
  const majorInput = document.getElementById("major");
  const minorInput = document.getElementById("minor");
  const cgpaInput = document.getElementById("cgpa");
  const gradDateInput = document.getElementById("gradDate");
  const skillsInput = document.getElementById("skills");
  const achievementsInput = document.getElementById("achievements");
  const certsInput = document.getElementById("certs");
  const errorMessage = document.getElementById("errorMessage");

  // Pre-fill from stored data if present
  const storedProfile = JSON.parse(localStorage.getItem("clientProfile") || "{}");
  if (storedProfile.university) universityInput.value = storedProfile.university;
  if (storedProfile.major) majorInput.value = storedProfile.major;
  if (storedProfile.minor) minorInput.value = storedProfile.minor;
  if (storedProfile.cgpa) cgpaInput.value = storedProfile.cgpa;
  if (storedProfile.gradDate) gradDateInput.value = storedProfile.gradDate;
  if (storedProfile.skills) skillsInput.value = storedProfile.skills;
  if (storedProfile.achievements) achievementsInput.value = storedProfile.achievements;
  if (storedProfile.certs) certsInput.value = storedProfile.certs;

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const university = universityInput.value.trim();
    const major = majorInput.value.trim();
    const minor = minorInput.value.trim();
    const cgpa = cgpaInput.value.trim();
    const gradDate = gradDateInput.value;
    const skills = skillsInput.value.trim();
    const achievements = achievementsInput.value.trim();
    const certs = certsInput.value.trim();

    if (!university || !major) {
      errorMessage.textContent = "Please fill in at least university and major.";
      return;
    }

    const updated = {
      ...storedProfile,
      university,
      major,
      minor,
      cgpa,
      gradDate,
      skills,
      achievements,
      certs
    };

    localStorage.setItem("clientProfile", JSON.stringify(updated));
    errorMessage.textContent = "";

    window.location.href = "profile_c.html";
  });
});
