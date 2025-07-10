<?php
$auth = Auth::getInstance($db);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap - <?= $site_name ?></title>
    <?php include("./files/assets/css-files.php"); ?>
    <link rel="stylesheet" href="./style/css/login.css">
</head>
<body>
    <div class="login-form-container">
        <div class="logo-container">
            <i class="fas fa-file"></i>
            <span class="logo-text"><?= $site_name ?></span>
        </div>
        
        <?php if (!empty($err)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $err; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" class="needs-validation" method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($auth->generateCSRFToken()); ?>">
            
            <div class="mb-3">
                <label for="email" class="form-label">E-Posta</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="emailInput" id="email" placeholder="ornek@openfile.com" required>
                    <div class="invalid-feedback">
                        Lütfen geçerli bir E-Posta adresi girin.
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Parola</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="passwordInput" id="password" placeholder="************" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        Lütfen parolanızı girin.
                    </div>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                <label class="form-check-label" for="rememberMe">Beni Hatırla</label>
            </div>
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                </button>
            </div>
            <div class="text-center">
                <a href="forgot-password" class="text-decoration-none">Şifremi Unuttum</a>
            </div>
            <div class="divider">
                <span>veya</span>
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary">
                    <i class="fab fa-google me-2"></i>Google ile Giriş Yap
                </button>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">Hesabın yok mu? <a href="register" class="text-decoration-none">Hesap Oluştur!</a></p>
            </div>
        </form>
    </div>
    
    <script type="text/javascript" src="<?= $site_url; ?>style/js/login.js"></script>
</body>
</html>