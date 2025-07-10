<?php
$auth = Auth::getInstance($db);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - <?= $site_name ?></title>
    <?php include("./files/assets/css-files.php"); ?>
    <link rel="stylesheet" href="./style/css/register.css">
</head>
<body>
    <div class="register-form-container">
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
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form id="registerForm" class="needs-validation" method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($auth->generateCSRFToken()); ?>">
            
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="@openfile" required>
                    <div class="invalid-feedback">
                        Lütfen bir kullanıcı adı girin.
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-Posta</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="ornek@openfile.com" required>
                    <div class="invalid-feedback">
                        Lütfen geçerli bir E-Posta adresi girin.
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Parola</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="************" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        Lütfen geçerli bir parola girin.
                    </div>
                </div>
                <div class="password-requirements mt-1">
                    <p class="mb-1">Parola şunları içermelidir:</p>
                    <ul>
                        <li id="length">En az 8 karakter,</li>
                        <li id="letter">En az 1 büyük ve küçük harf,</li>
                        <li id="number">En az 1 rakam,</li>
                        <li id="special">En az 1 özel harf.</li>
                    </ul>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Parola (Tekrar)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="************" required>
                    <button class="btn btn-outline-secondary toggle-confirm-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        Parolalar eşleşmiyor.
                    </div>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="termsCheck" name="termsCheck" required>
                <label class="form-check-label" for="termsCheck"><a href="#" class="text-decoration-none">Kullanım Şartları</a>'nı ve <a href="#" class="text-decoration-none">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.</label>
                <div class="invalid-feedback">
                    Kullanım şartlarını ve gizlilik politikasını kabul etmelisiniz.
                </div>
            </div>
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Kayıt Ol
                </button>
            </div>
            <div class="divider">
                <span>veya</span>
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" id="googleSignup">
                    <i class="fab fa-google me-2"></i>Google ile Kayıt Ol
                </button>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">Zaten bir hesaba sahip misin? <a href="login" class="text-decoration-none">Giriş Yap</a></p>
            </div>
        </form>
    </div>
    
    <script type="text/javascript" src="<?= $site_url; ?>style/js/register.js"></script>
</body>
</html>