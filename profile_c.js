/*document.addEventListener("DOMContentLoaded", () => {
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
});*/

document.addEventListener("DOMContentLoaded", () => {
  const updateBtn = document.getElementById("updateProfileBtn");
  const logoutLink = document.getElementById("logoutLink");

  // ---- Load stored client profile (name, email, academic info) ----
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

  // ---- Load all questions and show only this client's ----
  const tbody = document.getElementById("questionsBody");
  tbody.innerHTML = "";

  const questionsRaw = localStorage.getItem("clientQuestions");
  let questions = [];
  if (questionsRaw) {
    try {
      questions = JSON.parse(questionsRaw);
    } catch {
      questions = [];
    }
  }

  const clientEmail = storedProfile.email || null;

  // Filter: only show queries that belong to this client
  const myQuestions = clientEmail
    ? questions.filter((q) => q.clientEmail === clientEmail)
    : questions; // fallback: show all if email not set

  myQuestions.forEach((q) => {
    const tr = document.createElement("tr");

    // ID
    const tdId = document.createElement("td");
    tdId.textContent = q.id;

    // Query text
    const tdText = document.createElement("td");
    tdText.textContent = q.text;

    // Status (Pending / Replied)
    
    // Readiness
    const tdReady = document.createElement("td");
    const readySpan = document.createElement("span");
    readySpan.classList.add("readiness");
    if (q.readiness === "Ready") {
      readySpan.classList.add("readiness-ready");
    } else if (q.readiness === "Almost ready") {
      readySpan.classList.add("readiness-almost");
    } else {
      readySpan.classList.add("readiness-not");
    }
    readySpan.textContent = q.readiness || "Not ready";
    tdReady.appendChild(readySpan);

    // Feedback by
    const tdBy = document.createElement("td");
    tdBy.textContent = q.feedbackBy || "—";

    // Detailed feedback
    const tdDetail = document.createElement("td");
    tdDetail.textContent = q.detailedFeedback || "No feedback yet";

    tr.appendChild(tdId);
    tr.appendChild(tdText);
    tr.appendChild(tdReady);
    tr.appendChild(tdBy);
    tr.appendChild(tdDetail);

    tbody.appendChild(tr);
  });
  // after rendering questions in profile_c.js
  const appliedBody = document.getElementById("appliedProgramsBody");
  if (appliedBody) {
    const clientEmail = storedProfile.email || "";
    const keyApps = clientEmail ? `appliedProgramDetails_${clientEmail}` : "appliedProgramDetails_guest";
    const applied = JSON.parse(localStorage.getItem(keyApps) || "[]");

    appliedBody.innerHTML = "";
    applied.forEach((p) => {
      const tr = document.createElement("tr");

      const tdTitle = document.createElement("td");
      tdTitle.textContent = p.title;

      const tdOwner = document.createElement("td");
      tdOwner.textContent = p.owner || "—";

      const tdDur = document.createElement("td");
      tdDur.textContent = p.duration || "—";

      tr.appendChild(tdTitle);
      tr.appendChild(tdOwner);
      tr.appendChild(tdDur);

      appliedBody.appendChild(tr);
    });
  }


  // ---- Navigation ----
  updateBtn.addEventListener("click", () => {
    window.location.href = "update_c.html";
  });

  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "login.html";
    });
  }
});


