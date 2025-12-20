document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("programForm");
  const heading = document.getElementById("programTitleHeading");
  const titleInput = document.getElementById("pTitle");
  const ownerInput = document.getElementById("pOwner");
  const durationInput = document.getElementById("pDuration");
  const descInput = document.getElementById("pDescription");
  const errorMessage = document.getElementById("errorMessage");

  function loadJSON(key, fallback) {
    const raw = localStorage.getItem(key);
    if (!raw) return fallback;
    try {
      return JSON.parse(raw);
    } catch {
      return fallback;
    }
  }

  function saveJSON(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  const programs = loadJSON("programs", []);
  const currentIdStr = localStorage.getItem("currentProgramId");
  let editingProgram = null;

  if (currentIdStr) {
    const currentId = Number(currentIdStr);
    editingProgram = programs.find((p) => p.id === currentId) || null;
  }

  if (editingProgram) {
    heading.textContent = "Edit mentorship program";
    titleInput.value = editingProgram.title || "";
    ownerInput.value = editingProgram.owner || "";
    durationInput.value = editingProgram.duration || "";
    descInput.value = editingProgram.description || "";
  } else {
    heading.textContent = "Add mentorship program";
  }

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const title = titleInput.value.trim();
    const owner = ownerInput.value.trim();
    const duration = durationInput.value.trim();
    const description = descInput.value.trim();

    if (!title || !description) {
      errorMessage.textContent = "Please fill in at least title and detailed description.";
      return;
    }

    if (editingProgram) {
      // Update existing
      editingProgram.title = title;
      editingProgram.owner = owner;
      editingProgram.duration = duration;
      editingProgram.description = description;
    } else {
      const newId = programs.length > 0 ? programs[programs.length - 1].id + 1 : 1;
      programs.push({
        id: newId,
        title,
        owner,
        duration,
        description
      });
    }

    saveJSON("programs", programs);
    errorMessage.textContent = "";

    localStorage.removeItem("currentProgramId");
    window.location.href = "admin.html";
  });
});
