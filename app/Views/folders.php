<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Folders</title>
</head>

<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col">
        <h1>Folders</h1>
        <p>Account Id: <?= $account_id ?></p>
        <p>Root Folder Id: <?= $root_folder_id ?></p>
        <ul>
          <?php foreach ($folders as $folder): ?>
            <li><?= $folder['name'] ?> (ID: <?= $folder['id'] ?>)</li>
          <?php endforeach; ?>
        </ul>
        <pre><?php print_r($folders) ?></pre>
      </div>
    </div>
  </div>
</body>

</html>