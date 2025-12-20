document.addEventListener("DOMContentLoaded", () => {
  const programsList = document.getElementById("programsList");
  const logoutLink = document.getElementById("logoutLink");

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

  // Current client identity
  const clientProfile = loadJSON("clientProfile", {});
  const clientEmail = clientProfile.email || "";
  const clientName = clientProfile.fullName || "Client";

  // Array of all programs
  const programs = loadJSON("programs", []);

  // Set of program IDs this client has applied for
  const appliedKey = clientEmail ? `appliedPrograms_${clientEmail}` : "appliedPrograms_guest";
  const appliedIds = new Set(loadJSON(appliedKey, []));

  function renderPrograms() {
    programsList.innerHTML = "";

    if (programs.length === 0) {
      const msg = document.createElement("p");
      msg.className = "page-subtitle";
      msg.textContent = "No mentorship programs are available yet.";
      programsList.appendChild(msg);
      return;
    }

    programs.forEach((p) => {
      const card = document.createElement("div");
      card.className = "program-card";

      const titleRow = document.createElement("div");
      titleRow.className = "program-title-row";

      const title = document.createElement("div");
      title.className = "program-title";
      title.textContent = p.title;

      titleRow.appendChild(title);

      const owner = document.createElement("div");
      owner.className = "program-meta";
      if (p.owner) owner.textContent = p.owner;

      const duration = document.createElement("div");
      duration.className = "program-meta";
      if (p.duration) duration.textContent = `Duration: ${p.duration}`;

      const desc = document.createElement("div");
      desc.className = "program-desc";
      desc.textContent = p.description || "";

      const actions = document.createElement("div");
      actions.className = "program-actions";

      if (appliedIds.has(p.id)) {
        const label = document.createElement("span");
        label.className = "secondary-label";
        label.textContent = "Already applied";
        actions.appendChild(label);
      } else {
        const applyBtn = document.createElement("button");
        applyBtn.className = "primary-btn";
        applyBtn.textContent = "Apply for program";
        applyBtn.addEventListener("click", () => {
          // Add to applied list for this client
          appliedIds.add(p.id);
          saveJSON(appliedKey, Array.from(appliedIds));

          // Optional: also store richer mapping so profile_c.html can show details
          const keyApps = clientEmail ? `appliedProgramDetails_${clientEmail}` : "appliedProgramDetails_guest";
          const existing = loadJSON(keyApps, []);
          const already = existing.find((e) => e.id === p.id);
          if (!already) {
            existing.push({
              id: p.id,
              title: p.title,
              owner: p.owner,
              duration: p.duration,
              description: p.description
            });
            saveJSON(keyApps, existing);
          }

          // Redirect to client profile
          window.location.href = "profile_c.html";
        });
        actions.appendChild(applyBtn);
      }

      card.appendChild(titleRow);
      if (p.owner) card.appendChild(owner);
      if (p.duration) card.appendChild(duration);
      card.appendChild(desc);
      card.appendChild(actions);

      programsList.appendChild(card);
    });
  }

  renderPrograms();

  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "login.html";
    });
  }
});
