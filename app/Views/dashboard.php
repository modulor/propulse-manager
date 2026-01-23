<!DOCTYPE html>
<html>

<head>
  <title>Dashboard - Frame.io API</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Dashboard</h1>
      <a href="/auth/logout" class="btn btn-outline-secondary">Cerrar Sesión</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5>Información del Usuario</h5>
          </div>
          <div class="card-body">
            <p><strong>ID:</strong> <?= $user['data']['id'] ?? 'N/A' ?></p>
            <p><strong>Email:</strong> <?= $user['data']['email'] ?? 'N/A' ?></p>
            <p><strong>Nombre:</strong> <?= $user['data']['name'] ?? 'N/A' ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5>Cuentas Disponibles</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($accounts)): ?>
              <ul class="list-group">
                <?php foreach ($accounts as $account): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= $account['name'] ?? 'Sin nombre' ?>
                    <a href="/dashboard/workspaces/<?= $account['id'] ?>" class="btn btn-sm btn-primary">Ver Workspaces</a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p>No se encontraron cuentas.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>