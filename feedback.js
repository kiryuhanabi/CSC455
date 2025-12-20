document.addEventListener("DOMContentLoaded", () => {
  const queryEl = document.getElementById("fbQueryText");
  const fbByInput = document.getElementById("fbBy");
  const fbReadiness = document.getElementById("fbReadiness");
  const fbDetail = document.getElementById("fbDetail");
  const errorMessage = document.getElementById("errorMessage");
  const form = document.getElementById("feedbackForm");

  const currentIdStr = localStorage.getItem("currentQuestionId");
  if (!currentIdStr) {
    errorMessage.textContent = "No question selected. Returning to assessment.";
    setTimeout(() => {
      window.location.href = "assessment_s.html";
    }, 800);
    return;
  }

  const currentId = Number(currentIdStr);

  const raw = localStorage.getItem("clientQuestions");
  let questions = [];
  if (raw) {
    try {
      questions = JSON.parse(raw);
    } catch {
      questions = [];
    }
  }

  const question = questions.find((q) => q.id === currentId);
  if (!question) {
    errorMessage.textContent = "Could not find the selected question.";
    setTimeout(() => {
      window.location.href = "assessment_s.html";
    }, 800);
    return;
  }

  // Pre-fill fields
  queryEl.textContent = question.text;
  fbByInput.value = question.feedbackBy || (localStorage.getItem("currentSpecialistName") || "");
  fbReadiness.value = question.readiness || "Not ready";
  fbDetail.value = question.detailedFeedback || "";

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const by = fbByInput.value.trim();
    const readiness = fbReadiness.value;
    const detail = fbDetail.value.trim();

    if (!by || !detail) {
      errorMessage.textContent = "Please fill in your name and detailed feedback.";
      return;
    }

    // Update the question object
    question.feedbackBy = by;
    question.readiness = readiness;
    question.detailedFeedback = detail;
    // Optionally link specialist profile
    question.specialistProfileUrl = "profile_s.html";

    localStorage.setItem("clientQuestions", JSON.stringify(questions));
    errorMessage.textContent = "";

    window.location.href = "assessment_s.html";
  });
});
