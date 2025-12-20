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

  // Load stored profile data (if you used this earlier)
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

  // Load questions from localStorage and render table
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

  questions.forEach((q) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = q.id;

    const tdText = document.createElement("td");
    tdText.textContent = q.text;

    const tdStatus = document.createElement("td");
    const statusSpan = document.createElement("span");
    statusSpan.classList.add("status");
    if (q.status === "Replied") {
      statusSpan.classList.add("status-replied");
    } else {
      statusSpan.classList.add("status-pending");
    }
    statusSpan.textContent = q.status;
    tdStatus.appendChild(statusSpan);

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
    readySpan.textContent = q.readiness;
    tdReady.appendChild(readySpan);

    tr.appendChild(tdId);
    tr.appendChild(tdText);
    tr.appendChild(tdStatus);
    tr.appendChild(tdReady);

    tbody.appendChild(tr);
  });

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

