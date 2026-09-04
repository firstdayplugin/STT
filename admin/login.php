<?php
require_once dirname(__DIR__) . '/core/config/config.php';
require_once dirname(__DIR__) . '/core/database/Database.php';
require_once dirname(__DIR__) . '/core/helpers/helpers.php';

if (isset($_SESSION['admin_user'])) {
    header('Location: ' . admin_url());
    exit;
}

$error = '';
$attempt_key = 'login_attempts';
$lockout_key = 'login_lockout';
$now = time();
$is_locked = isset($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > $now;
$remaining = $is_locked ? $_SESSION[$lockout_key] - $now : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token tidak valid. Silakan refresh halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi.';
        } else {
            $db = Database::getInstance();
            $user = $db->fetchOne("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1", [$username, $username]);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION[$attempt_key] = 0;
                $_SESSION[$lockout_key] = 0;
                $_SESSION['admin_user'] = [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'nama'     => $user['nama'],
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                    'avatar'   => $user['avatar'],
                ];
                $db->execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
                log_activity('login', 'User login: ' . $user['username'], $user['id']);

                $redirect = $_GET['redirect'] ?? admin_url();
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION[$attempt_key] = ($_SESSION[$attempt_key] ?? 0) + 1;
                if ($_SESSION[$attempt_key] >= 5) {
                    $_SESSION[$lockout_key] = time() + 300;
                    $_SESSION[$attempt_key] = 0;
                    $error = 'Terlalu banyak percobaan. Akun dikunci 5 menit.';
                    $is_locked = true;
                    $remaining = 300;
                } else {
                    $sisa = 5 - $_SESSION[$attempt_key];
                    $error = 'Username atau password salah. Sisa percobaan: ' . $sisa;
                }
            }
        }
    }
}

$csrf = generate_csrf();
$site_name = get_setting('site_name', 'Reklamepedia');
$logo = get_setting('logo');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — <?= htmlspecialchars($site_name) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #1E2127;
  --bg-2: #252830;
  --surface: rgba(255,255,255,0.03);
  --border: rgba(255,255,255,0.08);
  --text: #FFFFFF;
  --text-muted: rgba(255,255,255,0.55);
  --accent: #E8A020;
  --accent-2: #D08F18;
  --danger: #ef4444;
  --danger-bg: rgba(239,68,68,0.1);
}
body {
  font-family: 'Outfit', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  position: relative;
  overflow: hidden;
}
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background:
    radial-gradient(ellipse 800px 600px at 20% 20%, rgba(232,160,32,0.06), transparent),
    radial-gradient(ellipse 600px 500px at 80% 80%, rgba(232,160,32,0.04), transparent);
  pointer-events: none;
}
.login-container {
  position: relative;
  width: 100%;
  max-width: 1080px;
  display: grid;
  grid-template-columns: 1fr 440px;
  gap: 60px;
  align-items: center;
}
.login-info { padding: 40px; }
.login-info .logo {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 36px;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin-bottom: 24px;
}
.login-info .logo-r {
  font-family: 'DM Serif Display', serif;
  font-style: italic;
  color: var(--accent);
  font-size: 44px;
  line-height: 1;
}
.login-info .tagline {
  font-size: 14px;
  color: var(--accent);
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  margin-bottom: 16px;
}
.login-info h1 {
  font-size: 36px;
  font-weight: 700;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 16px;
}
.login-info p {
  font-size: 15px;
  color: var(--text-muted);
  line-height: 1.7;
  max-width: 420px;
}
.login-card {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 40px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.4);
}
.login-card-header {
  text-align: center;
  margin-bottom: 32px;
}
.login-card-header .brand {
  font-size: 28px;
  font-weight: 700;
  color: var(--accent);
  margin-bottom: 4px;
}
.login-card-header .brand img { height: 36px; margin: 0 auto; }
.login-card-header .sub {
  font-size: 13px;
  color: var(--text-muted);
  letter-spacing: 0.05em;
}
.alert {
  padding: 14px 16px;
  border-radius: 12px;
  font-size: 13px;
  line-height: 1.5;
  margin-bottom: 20px;
  display: flex;
  align-items: flex-start;
  gap: 10px;
}
.alert-error {
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid rgba(239,68,68,0.25);
}
.alert-lockout {
  background: var(--danger-bg);
  color: var(--danger);
  border: 1px solid rgba(239,68,68,0.25);
  text-align: center;
  flex-direction: column;
  align-items: center;
}
.lockout-timer {
  font-size: 28px;
  font-weight: 700;
  margin-top: 8px;
}
.form-group { margin-bottom: 18px; }
.form-group label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 8px;
}
.form-group input {
  width: 100%;
  padding: 14px 18px;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: 12px;
  color: var(--text);
  font-family: inherit;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, background 0.2s;
}
.form-group input:focus {
  border-color: var(--accent);
  background: rgba(255,255,255,0.06);
}
.input-wrapper { position: relative; }
.input-wrapper input { padding-right: 50px; }
.toggle-pass {
  position: absolute;
  right: 14px; top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 4px;
  display: flex;
}
.toggle-pass:hover { color: var(--accent); }
.btn-login {
  width: 100%;
  padding: 15px;
  background: var(--accent);
  color: #000;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.02em;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 8px;
}
.btn-login:hover { background: var(--accent-2); transform: translateY(-1px); }
.btn-login:disabled { opacity: 0.5; cursor: not-allowed; }
.back-link {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
}
.back-link a {
  color: var(--accent);
  text-decoration: none;
}
.back-link a:hover { text-decoration: underline; }

@media (max-width: 900px) {
  .login-container { grid-template-columns: 1fr; gap: 24px; }
  .login-info { display: none; }
  .login-card { padding: 32px 28px; }
}
</style>
</head>
<body>

<div class="login-container">

  <div class="login-info">
    <?php 
      $login_logo  = get_setting('admin_login_logo', $logo);
      $login_title = get_setting('admin_login_title', 'Kelola website Anda dengan mudah.');
      $login_desc  = get_setting('admin_login_desc', 'Login untuk mengakses dashboard admin. Kelola konten, layanan, galeri, blog, produk, dan semua pengaturan website dari satu tempat.');
      $brand_text  = htmlspecialchars($site_name ?: 'Admin');
      $first_letter = mb_substr($brand_text, 0, 1);
      $rest_text    = mb_substr($brand_text, 1);
    ?>
    <div class="logo">
      <?php if ($login_logo): ?>
        <img src="<?= uploads_url($login_logo) ?>" alt="<?= $brand_text ?>" style="max-height:48px;width:auto">
      <?php else: ?>
        <span class="logo-r"><?= $first_letter ?></span><span><?= $rest_text ?></span>
      <?php endif; ?>
    </div>
    <div class="tagline">Panel Admin</div>
    <h1><?= htmlspecialchars($login_title) ?></h1>
    <p><?= htmlspecialchars($login_desc) ?></p>
  </div>

  <div class="login-card">
    <div class="login-card-header">
      <?php if ($logo): ?>
        <div class="brand"><img src="<?= uploads_url($logo) ?>" alt=""></div>
      <?php else: ?>
        <div class="brand"><?= htmlspecialchars($site_name) ?></div>
      <?php endif; ?>
      <div class="sub">Masuk ke dashboard</div>
    </div>

    <?php if ($is_locked): ?>
      <div class="alert alert-lockout">
        <div>🔒 Akun dikunci karena terlalu banyak percobaan gagal.</div>
        <div class="lockout-timer" id="lockout-timer"><?= gmdate('i:s', $remaining) ?></div>
      </div>
    <?php elseif ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$is_locked): ?>
    <form method="POST" action="" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="form-group">
        <label for="username">Username / Email</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="admin"
               autocomplete="username" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password"
                 placeholder="••••••••"
                 autocomplete="current-password" required>
          <button type="button" class="toggle-pass" onclick="togglePass()" aria-label="Toggle password">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" id="eye-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">Masuk ke Dashboard</button>
    </form>
    <?php endif; ?>

    <div class="back-link">
      <a href="<?= url('/') ?>">← Kembali ke Website</a>
    </div>
  </div>
</div>

<script>
function togglePass() {
  const inp = document.getElementById('password');
  const icon = document.getElementById('eye-icon');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
  } else {
    inp.type = 'password';
    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
  }
}

<?php if ($is_locked): ?>
let timeLeft = <?= $remaining ?>;
const timer = document.getElementById('lockout-timer');
const interval = setInterval(() => {
  timeLeft--;
  if (timeLeft <= 0) {
    clearInterval(interval);
    location.reload();
    return;
  }
  const m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
  const s = String(timeLeft % 60).padStart(2, '0');
  timer.textContent = m + ':' + s;
}, 1000);
<?php endif; ?>
</script>
</body>
</html>
