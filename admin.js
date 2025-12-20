document.addEventListener("DOMContentLoaded", () => {
  const usersBody = document.getElementById("usersBody");
  const queriesBody = document.getElementById("queriesBody");
  const programsList = document.getElementById("programsList");

  const addUserBtn = document.getElementById("addUserBtn");
  const addProgramBtn = document.getElementById("addProgramBtn");
  const logoutLink = document.getElementById("logoutLink");

  // ---------- Helpers ----------
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

  // ---------- Users ----------
  function renderUsers() {
    const users = loadJSON("users", []);
    usersBody.innerHTML = "";

    users.forEach((u) => {
      const tr = document.createElement("tr");

      const tdId = document.createElement("td");
      tdId.textContent = u.id;

      const tdName = document.createElement("td");
      tdName.textContent = u.fullName;

      const tdEmail = document.createElement("td");
      tdEmail.textContent = u.email;

      const tdRole = document.createElement("td");
      tdRole.textContent = u.role;

      const tdProfile = document.createElement("td");
      if (u.profileUrl) {
        const link = document.createElement("a");
        link.href = u.profileUrl;
        link.textContent = "View profile";
        link.className = "link";
        tdProfile.appendChild(link);
      } else {
        tdProfile.textContent = "—";
      }

      const tdActions = document.createElement("td");
      const delBtn = document.createElement("button");
      delBtn.className = "secondary-btn";
      delBtn.textContent = "Remove";
      delBtn.addEventListener("click", () => {
        const updated = users.filter((x) => x.id !== u.id);
        saveJSON("users", updated);
        renderUsers();
      });
      tdActions.appendChild(delBtn);

      tr.appendChild(tdId);
      tr.appendChild(tdName);
      tr.appendChild(tdEmail);
      tr.appendChild(tdRole);
      tr.appendChild(tdProfile);
      tr.appendChild(tdActions);

      usersBody.appendChild(tr);
    });
  }

  addUserBtn.addEventListener("click", () => {
    const fullName = prompt("Full name:");
    if (!fullName) return;
    const email = prompt("Email:");
    if (!email) return;
    const role = prompt("Role (client/specialist/admin):", "client");
    if (!role) return;

    let profileUrl = "";
    if (role === "client") profileUrl = "profile_c.html";
    else if (role === "specialist") profileUrl = "profile_s.html";
    else if (role === "admin") profileUrl = "admin.html";

    const users = loadJSON("users", []);
    const newId = users.length > 0 ? users[users.length - 1].id + 1 : 1;

    users.push({ id: newId, fullName, email, role, profileUrl });
    saveJSON("users", users);
    renderUsers();
  });

  // ---------- Queries ----------
  function renderQueries() {
    const questions = loadJSON("clientQuestions", []);
    queriesBody.innerHTML = "";

    questions.forEach((q) => {
      const tr = document.createElement("tr");

      const tdId = document.createElement("td");
      tdId.textContent = q.id;

      const tdClient = document.createElement("td");
      if (q.clientName && q.clientProfileUrl) {
        const link = document.createElement("a");
        link.href = q.clientProfileUrl;
        link.textContent = q.clientName;
        link.className = "link";
        tdClient.appendChild(link);
      } else {
        tdClient.textContent = q.clientName || "Client";
      }

      const tdQuery = document.createElement("td");
      tdQuery.textContent = q.text;

      const tdStatus = document.createElement("td");
      tdStatus.textContent = q.status || "Pending";

      const tdReady = document.createElement("td");
      tdReady.textContent = q.readiness || "Not ready";

      const tdActions = document.createElement("td");
      const delBtn = document.createElement("button");
      delBtn.className = "secondary-btn";
      delBtn.textContent = "Remove";
      delBtn.addEventListener("click", () => {
        const updated = questions.filter((x) => x.id !== q.id);
        saveJSON("clientQuestions", updated);
        renderQueries();
      });
      tdActions.appendChild(delBtn);

      tr.appendChild(tdId);
      tr.appendChild(tdClient);
      tr.appendChild(tdQuery);
      tr.appendChild(tdStatus);
      tr.appendChild(tdReady);
      tr.appendChild(tdActions);

      queriesBody.appendChild(tr);
    });
  }

  // ---------- Programs ----------
  function renderPrograms() {
    const programs = loadJSON("programs", []);
    programsList.innerHTML = "";

    programs.forEach((p) => {
      const card = document.createElement("div");
      card.className = "program-card";

      const titleRow = document.createElement("div");
      titleRow.className = "program-title-row";

      const title = document.createElement("div");
      title.className = "program-title";
      title.textContent = p.title;

      const actions = document.createElement("div");
      actions.className = "program-actions";

      const editBtn = document.createElement("button");
      editBtn.className = "secondary-btn";
      editBtn.textContent = "Edit";
      editBtn.addEventListener("click", () => {
        localStorage.setItem("currentProgramId", String(p.id));
        window.location.href = "program.html";
      });

      const delBtn = document.createElement("button");
      delBtn.className = "secondary-btn";
      delBtn.textContent = "Remove";
      delBtn.addEventListener("click", () => {
        const updated = programs.filter((x) => x.id !== p.id);
        saveJSON("programs", updated);
        renderPrograms();
      });

      actions.appendChild(editBtn);
      actions.appendChild(delBtn);

      titleRow.appendChild(title);
      titleRow.appendChild(actions);

      const meta = document.createElement("div");
      meta.className = "program-meta";
      meta.textContent = p.owner || "";

      const duration = document.createElement("div");
      duration.className = "program-meta";
      if (p.duration) duration.textContent = `Duration: ${p.duration}`;

      const desc = document.createElement("div");
      desc.className = "program-desc";
      desc.textContent = p.description || "";

      card.appendChild(titleRow);
      if (p.owner) card.appendChild(meta);
      if (p.duration) card.appendChild(duration);
      card.appendChild(desc);

      programsList.appendChild(card);
    });
  }

  addProgramBtn.addEventListener("click", () => {
    localStorage.removeItem("currentProgramId"); // adding new
    window.location.href = "program.html";
  });

  // ---------- Logout ----------
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "login.html";
    });
  }

  // Initial render
  renderUsers();
  renderQueries();
  renderPrograms();
});
