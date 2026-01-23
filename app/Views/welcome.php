<!DOCTYPE html>
<html>

<head>
  <title>Frame.io API Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            <h1 class="card-title">Frame.io API Test</h1>
            <p class="card-text">Conecta tu aplicación con Frame.io V4 API</p>

            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
              <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <a href="/auth/login" class="btn btn-primary">Conectar con Frame.io</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>