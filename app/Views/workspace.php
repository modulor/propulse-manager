<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Workspace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col-6">
        <p>Workspace ID: <?= $workspace_id ?></p>
      </div>
      <div class="col-6">
        <p>Projects:</p>
        <ul>
          <?php foreach ($projects as $project): ?>
            <li>
              <a href="/dashboard/project/<?= $account_id ?>/<?= $workspace_id ?>/<?= $project['id'] ?>"><?= $project['name'] ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <pre><?= print_r($projects) ?></pre>
      </div>
    </div>
  </div>
</body>

</html>