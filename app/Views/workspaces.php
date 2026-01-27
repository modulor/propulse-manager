<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col-6">
        <p>Account ID: <?= $account_id ?></p>
      </div>
      <div class="col-6">
        <p>Workspaces:</p>
        <ul>
          <?php foreach ($workspaces as $workspace): ?>
            <li>
              <a href="/dashboard/workspace/<?= $account_id ?>/<?= $workspace['id'] ?>"><?= $workspace['name'] ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <pre><?= print_r($workspaces) ?></pre>
      </div>
    </div>
  </div>

</body>

</html>