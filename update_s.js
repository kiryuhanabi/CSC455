document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("updateForm");
  const bioInput = document.getElementById("bio");
  const titleInput = document.getElementById("title");
  const degreesInput = document.getElementById("degrees");
  const experienceInput = document.getElementById("experience");
  const skillsInput = document.getElementById("skills");
  const errorMessage = document.getElementById("errorMessage");

  // Load existing data (demo via localStorage)
  const storedProfile = JSON.parse(localStorage.getItem("specialistProfile") || "{}");
  if (storedProfile.bio) bioInput.value = storedProfile.bio;
  if (storedProfile.title) titleInput.value = storedProfile.title;
  if (storedProfile.degrees) degreesInput.value = storedProfile.degrees;
  if (storedProfile.experience) experienceInput.value = storedProfile.experience;
  if (storedProfile.skills) skillsInput.value = storedProfile.skills;

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const bio = bioInput.value.trim();
    const title = titleInput.value.trim();
    const degrees = degreesInput.value.trim();
    const experience = experienceInput.value.trim();
    const skills = skillsInput.value.trim();

    if (!bio || !title) {
      errorMessage.textContent = "Please fill in at least short bio and professional title.";
      return;
    }

    const updated = {
      ...storedProfile,
      bio,
      title,
      degrees,
      experience,
      skills
    };

    localStorage.setItem("specialistProfile", JSON.stringify(updated));
    errorMessage.textContent = "";

    window.location.href = "profile_s.html";
  });
});
