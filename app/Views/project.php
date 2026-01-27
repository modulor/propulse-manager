<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project</title>
</head>

<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col">
        <h1>Project Details</h1>
        <pre><?= print_r($project) ?></pre>
        <p>Account ID: <?= $account_id ?></p>
        <p>Workspace ID: <?= $workspace_id ?></p>
        <p>Project ID: <?= $project_id ?></p>
        <p>Root Folder ID: <?php echo $project['data']['root_folder_id'] ?></p>
        <a href="/dashboard/project/<?= $account_id ?>/folders/<?php echo $project['data']['root_folder_id'] ?>">Folders</a>
      </div>
    </div>
  </div>
</body>

</html>