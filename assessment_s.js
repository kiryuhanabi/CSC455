document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById("assessmentBody");
  const logoutLink = document.getElementById("logoutLink");

  // In a real app, you'd know the logged-in specialist from auth.
  // For this assignment, store specialist name in localStorage or hardcode:
  const specialistProfile = JSON.parse(localStorage.getItem("specialistProfile") || "{}");
  const specialistName = specialistProfile.fullName || "Specialist";

  // Demo: client names; for now, we only know their query and ID.
  // You can extend clientQuestions items (on signup) to include clientName and clientProfileUrl.
  const questionsRaw = localStorage.getItem("clientQuestions");
  let questions = [];
  if (questionsRaw) {
    try {
      questions = JSON.parse(questionsRaw);
    } catch {
      questions = [];
    }
  }

  tbody.innerHTML = "";

  questions.forEach((q) => {
    const tr = document.createElement("tr");

    // ID
    const tdId = document.createElement("td");
    tdId.textContent = q.id;

    // Client name with link to profile (if present)
    // Client
    const tdClient = document.createElement("td");
    if (q.clientName && q.clientProfileUrl) {
    const link = document.createElement("a");
    link.href = q.clientProfileUrl;
    link.textContent = q.clientName;
    link.className = "client-link";
    tdClient.appendChild(link);
    } else {
    tdClient.textContent = q.clientName || "Client";
    }

    // Feedback by (specialist)
    const tdBy = document.createElement("td");
    if (q.feedbackBy && q.specialistProfileUrl) {
    const sLink = document.createElement("a");
    sLink.href = q.specialistProfileUrl;
    sLink.textContent = q.feedbackBy;
    sLink.className = "specialist-link";
    tdBy.appendChild(sLink);
    } else if (q.feedbackBy) {
    tdBy.textContent = q.feedbackBy;
    } else {
    tdBy.textContent = "—";
    }

    // Query text
    const tdQuery = document.createElement("td");
    tdQuery.textContent = q.text;

    // Readiness
    const tdReady = document.createElement("td");
    const badge = document.createElement("span");
    badge.classList.add("readiness-badge");

    if (q.readiness === "Ready") {
      badge.classList.add("readiness-ready");
    } else if (q.readiness === "Almost ready") {
      badge.classList.add("readiness-almost");
    } else {
      badge.classList.add("readiness-not");
    }
    badge.textContent = q.readiness || "Not ready";
    tdReady.appendChild(badge);

    // Detailed feedback
    const tdDetail = document.createElement("td");
    tdDetail.textContent = q.detailedFeedback || "No feedback yet";

    // Action: Provide feedback
    const tdAction = document.createElement("td");
    const btn = document.createElement("button");
    btn.className = "primary-btn";
    btn.textContent = q.detailedFeedback ? "Update feedback" : "Provide feedback";
    btn.addEventListener("click", () => {
      localStorage.setItem("currentQuestionId", String(q.id));
      // Also store which specialist is answering (for convenience)
      localStorage.setItem("currentSpecialistName", specialistName);
      window.location.href = "feedback.html";
    });
    tdAction.appendChild(btn);

    tr.appendChild(tdId);
    tr.appendChild(tdClient);
    tr.appendChild(tdBy);
    tr.appendChild(tdQuery);
    tr.appendChild(tdReady);
    tr.appendChild(tdDetail);
    tr.appendChild(tdAction);

    tbody.appendChild(tr);
  });

  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "login.html";
    });
  }
});
