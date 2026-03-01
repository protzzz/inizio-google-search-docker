<?php

// define("BACKEND_URL", getenv("BACKEND_URL") ?: "http://localhost:3000");

$backendUrl = getenv('BACKEND_URL') ?: '';
$backendPrivate = getenv('BACKEND_PRIVATE_URL') ?: 'http://backend:3000';
$backendPublic  = getenv('BACKEND_PUBLIC_URL')  ?: 'http://localhost:3000';

$base = $backendUrl !== '' ? $backendUrl : $backendPrivate;
$base = rtrim($base, '/');

define('BACKEND_BASE_URL', $base);


$query = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$start = isset($_GET["start"]) ? (int)$_GET["start"] : 1;
$total = isset($_GET["num"]) ? (int)$_GET["num"] : 10;

if ($total < 1) $total = 1;
if ($total > 10) $total = 10;
if ($start < 1) $start = 1;

$apiUrl = BACKEND_BASE_URL . '/api/search?' . http_build_query([
  'q' => $query,
  'num' => $total,
  'start' => $start,
]);

$results = null;
$error = null;

if ($query != "") {
  $curl = curl_init($apiUrl);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_TIMEOUT, 20);

  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  if ($response === false) {
    $error = "Network error: " . curl_error($curl);
  } else {
    $data = json_decode($response, true); // associative: true -> returns as Array

    if ($httpCode >= 200 && $httpCode < 300) {
      $results = $data;
    } else {
      $error = isset($data["error"]) ? $data["error"] : ("Request failed (HTTP " . $httpCode . ")");
    }
  }
}

$downloadJsonUrl = BACKEND_BASE_URL . '/api/search?' . http_build_query([
  'q' => $query,
  'num' => $total,
  'start' => $start,
  'download' => 1,
]);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Google Search</title>
</head>

<body>
  <main class="container">
    <h1 class="logo">Google</h1>

    <form class="search" method="get" action="">
      <input class="search-input" id="query" name="q" placeholder="Search Google" value="<?php echo htmlspecialchars($query); ?>">
      <button class="search-btn" type="submit">Search</button>
    </form>

    <?php if ($query !== ""): ?>
      <div class="short-info">
        Query: <b><?php echo htmlspecialchars($query); ?></b>
        | page=<?php echo $start; ?>
        | shown=<?php echo (is_array($results) && isset($results["results"]) && is_array($results["results"])) ? count($results["results"]) : 0; ?>
        <a class="btn" href="<?php echo htmlspecialchars($downloadJsonUrl); ?>">Download JSON</a>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (is_array($results) && isset($results["results"]) && is_array($results["results"])): ?>
      <div class="results">
        <?php foreach ($results["results"] as $result): ?>
          <div class="result">
            <div class="title">
              <?php echo (int)($result["position"] ?? 0); ?>.
              <a href="<?php echo htmlspecialchars($result["link"] ?? ""); ?>" target="_blank" rel="noreferrer">
                <?php echo htmlspecialchars($result["title"] ?? ""); ?>
              </a>
            </div>
            <div class="link"><?php echo htmlspecialchars($result["link"] ?? ""); ?></div>
            <div class="snippet"><?php echo htmlspecialchars($result["snippet"] ?? ""); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>
</body>

</html>