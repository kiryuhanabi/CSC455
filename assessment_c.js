document.addEventListener("DOMContentLoaded", () => {
  const queryForm = document.getElementById("queryForm");
  const queryText = document.getElementById("queryText");
  const formStatus = document.getElementById("formStatus");

  const chatForm = document.getElementById("chatForm");
  const chatInput = document.getElementById("chatInput");
  const chatbox = document.getElementById("chatbox");
  const logoutLink = document.getElementById("logoutLink");

  // --- Handle query submission and push to localStorage ---
  queryForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const text = queryText.value.trim();
    if (!text) {
      formStatus.textContent = "Please write your query before submitting.";
      return;
    }

    // Read existing questions array
    let questions = [];
    const existingRaw = localStorage.getItem("clientQuestions");
    if (existingRaw) {
      try {
        questions = JSON.parse(existingRaw);
      } catch {
        questions = [];
      }
    }

    // Generate new incremental ID
    const newId = questions.length > 0 ? questions[questions.length - 1].id + 1 : 1;

    const profile = JSON.parse(localStorage.getItem("clientProfile") || "{}");
    const clientEmail = profile.email || "";
    const clientName = profile.fullName || "Client";

    const newQuestion = {
      id: newId,
      text,
      status: "Pending",
      readiness: "Not ready",
      clientEmail: clientEmail,
      clientName: clientName,
      clientProfileUrl: "profile_c.html",  // link to client profile
  // feedbackBy, detailedFeedback, specialistProfileUrl will be added later
};


    questions.push(newQuestion);
    localStorage.setItem("clientQuestions", JSON.stringify(questions));

    formStatus.textContent = "Query submitted successfully. Redirecting to profile...";
    queryText.value = "";

    setTimeout(() => {
      window.location.href = "profile_c.html";
    }, 700);
  });

  // --- Simple AI-style chat feedback (local only, no backend) ---
  function addChatMessage(type, message) {
    const wrapper = document.createElement("div");
    wrapper.className = `chat-message ${type}`;

    const bubble = document.createElement("div");
    bubble.className = "bubble";
    bubble.textContent = message;

    wrapper.appendChild(bubble);
    chatbox.appendChild(wrapper);
    chatbox.scrollTop = chatbox.scrollHeight;
  }

  chatForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const msg = chatInput.value.trim();
    if (!msg) return;

    // Show user message
    addChatMessage("user", msg);
    chatInput.value = "";

    // Very simple canned feedback
    setTimeout(() => {
      const reply =
        "Try to include: 1) your target role, 2) current background (skills/semester), and 3) what exact decision or feedback you need.";
      addChatMessage("bot", reply);
    }, 500);
  });

  // --- Logout behavior (optional) ---
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "login.html";
    });
  }
});
