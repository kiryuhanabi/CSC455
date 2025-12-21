<?php
// assessment_c.php
require_once "connect.php";
session_start();

// Only logged-in clients
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$formStatus = "";
$chatReply = "";

// ---------- OpenAI API call (chat.completions) ----------
function call_openai(string $userMessage): ?string {
    // TODO: put your real API key here (or load from env)
    $OPENAI_API_KEY = "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";

    $url = "https://api.openai.com/v1/chat/completions";
    $payload = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "…"],
            ["role" => "user", "content" => $userMessage]
        ]
    ];


    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $OPENAI_API_KEY
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20
    ]);

    $result = curl_exec($ch);
    if ($result === false) {
        curl_close($ch);
        return "Sorry, the AI service is not available right now.";
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return "AI error (code $httpCode). Please try again later.";
    }

    $data = json_decode($result, true);
    if (!isset($data["choices"][0]["message"]["content"])) {
        return "AI response could not be parsed.";
    }
    return $data["choices"][0]["message"]["content"];  // main assistant reply[web:401]
}

// ---------- Handle form submissions ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Distinguish which form was submitted
    if (isset($_POST["queryText"])) {
        // New query submission -> insert into queries table
        $queryText = trim($_POST["queryText"]);
        if ($queryText === "") {
            $formStatus = "Please write your query before submitting.";
        } else {
            $stmt = $mysqli->prepare(
                "INSERT INTO queries (client_id, text, status, readiness, feedback_by, detailed_feedback)
                 VALUES (?, ?, 'pending', 'Not assessed', NULL, NULL)"
            );
            $stmt->bind_param("is", $userId, $queryText);
            if ($stmt->execute()) {
                $formStatus = "Your query has been submitted to specialists.";
            } else {
                $formStatus = "Error saving query: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST["chatInput"])) {
        // AI chat request through OpenAI
        $chatInput = trim($_POST["chatInput"]);
        if ($chatInput !== "") {
            $chatReply = call_openai($chatInput);
        } else {
            $chatReply = "Please type a message for the AI.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Assessment | CareerGuide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assessment_c.css">
</head>
<body>
  <!-- Dashboard header -->
  <header class="header">
    <div class="logo">CareerGuide</div>
    <nav class="nav">
      <a href="home.php">Home</a>
      <a href="assessment_c.php" class="active">Assessment</a>
      <a href="mentorship.php">Mentorship program</a>
      <a href="profile_c.php">Profile</a>
      <a href="login.php" id="logoutLink">Logout</a>
    </nav>
  </header>

  <main class="page">
    <!-- Left: query submission -->
    <section class="card query-card">
      <h1 class="page-title">Submit a new query</h1>
      <p class="page-subtitle">
        Explain your situation and what kind of feedback you are looking for.
      </p>

      <form id="queryForm" class="query-form" method="post" action="assessment_c.php">
        <label class="field field-textarea">
          <span>Your query</span>
          <textarea
            name="queryText"
            id="queryText"
            placeholder="Write your question here..."
            required
          ></textarea>
        </label>

        <button type="submit" class="primary-btn">Submit query</button>
        <p class="status-message" id="formStatus">
          <?php echo htmlspecialchars($formStatus); ?>
        </p>
      </form>
    </section>

    <!-- Right: AI feedback chatbox -->
    <section class="card chat-card">
      <h2 class="section-title">AI feedback</h2>
      <p class="chat-subtitle">
        Use the AI chat to quickly refine your query before specialists review it.
      </p>

      <div class="chatbox" id="chatbox">
        <div class="chat-message bot">
          <div class="bubble">
            Hi! Share your query on the left, then ask me how to make it clearer or more impactful.
          </div>
        </div>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["chatInput"])): ?>
          <?php if (trim($_POST["chatInput"]) !== ""): ?>
            <div class="chat-message user">
              <div class="bubble">
                <?php echo nl2br(htmlspecialchars($_POST["chatInput"])); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($chatReply)): ?>
            <div class="chat-message bot">
              <div class="bubble">
                <?php echo nl2br(htmlspecialchars($chatReply)); ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <form id="chatForm" class="chat-input-row" method="post" action="assessment_c.php">
        <input
          type="text"
          name="chatInput"
          id="chatInput"
          placeholder="Ask the AI for quick feedback..."
          autocomplete="off"
        >
        <button type="submit" class="secondary-btn">Send</button>
      </form>
    </section>
  </main>
</body>
</html>
