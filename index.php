<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "invest_db");

$login_success = false;
$user_data = null;
$message = "";
$msg_color = "red";

// 1. LOGOUT
if (isset($_GET['logout'])) {
    setcookie("user_id", "", time() - 3600);
    header("Location: index.php");
    exit();
}

// 2. QEYDİYYAT PROSESİ
if (isset($_POST['register'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    
    // İstifadəçinin bazada olub-olmadığını yoxlayaq
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Bu istifadəçi adı artıq götürülüb!";
    } else {
        $ins = mysqli_query($conn, "INSERT INTO users (username, password, balance) VALUES ('$user', '$pass', 10000.0)");
        if ($ins) {
            $message = "Qeydiyyat uğurludur! İndi daxil olun.";
            $msg_color = "#2ecc71";
        }
    }
}

// 3. LOGİN PROSESİ
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $login_success = true;
        $user_data = mysqli_fetch_assoc($result);
        setcookie("user_id", $user_data['id'], time() + 3600);
    } else {
        $message = "İstifadəçi adı və ya şifrə yanlışdır!";
    }
}

// COOKIE İLƏ GİRİŞİN QALMASI
if (isset($_COOKIE['user_id']) && !$login_success) {
    $c_id = $_COOKIE['user_id'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id = $c_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $login_success = true;
        $user_data = mysqli_fetch_assoc($res);
    }
}

// 4. COIN ALIŞI
if (isset($_POST['buy_coin'])) {
    $login_success = true; 
    $id = $_POST['u_id'];
    $amt = floatval($_POST['amount']);
    mysqli_query($conn, "UPDATE users SET balance = balance - $amt WHERE id = $id");
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <title>GOLD CRYPTO | VIP HUB</title>
    <style>
        :root { --gold: #f1c40f; --dark: #0a0a0a; --card: #161616; --green: #2ecc71; --red: #e74c3c; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--dark); color: white; }
        
        /* LOGIN & REGISTER SCREEN */
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--dark); display: flex; justify-content: center; align-items: center; z-index: 1000; }
        .login-box { background: var(--card); padding: 40px; border-radius: 15px; border: 1px solid var(--gold); text-align: center; width: 350px; }
        
        .tab-btns { display: flex; margin-bottom: 20px; gap: 10px; }
        .tab-btn { flex: 1; padding: 10px; background: #222; color: #666; border: none; cursor: pointer; border-radius: 5px; }
        .tab-btn.active { background: var(--gold); color: black; font-weight: bold; }

        .form-content { display: none; }
        .form-content.active { display: block; }

        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; padding: 20px 0; margin-bottom: 30px; }
        .balance-display { background: var(--card); padding: 10px 20px; border-radius: 10px; border-left: 5px solid var(--gold); display: flex; align-items: center; gap: 20px; }
        
        input { width: 100%; padding: 12px; margin: 10px 0; background: #000; border: 1px solid #333; color: white; border-radius: 5px; box-sizing: border-box; }
        .btn-main { width: 100%; padding: 12px; background: var(--gold); border: none; cursor: pointer; font-weight: bold; border-radius: 5px; margin-top: 10px; }
        
        .coin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .coin-card { background: var(--card); border: 1px solid #222; padding: 20px; border-radius: 15px; }
        .buy-btn { width: 35%; padding: 8px; background: var(--gold); border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .logout-btn { background: var(--red); color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<?php if (!$login_success): ?>
    <div class="overlay">
        <div class="login-box">
            <h2 style="color:var(--gold); margin-bottom: 25px;">GOLD CRYPTO </h2>
            <div class="tab-btns">
                <button class="tab-btn active" onclick="showForm('login-form', this)">DAXİL OL</button>
                <button class="tab-btn" onclick="showForm('register-form', this)">QEYDİYYAT</button>
            </div>

            <p style="color:<?php echo $msg_color; ?>; font-size:12px;"><?php echo $message; ?></p>

            <div id="login-form" class="form-content active">
                <form method="POST">
                    <input type="text" name="username" placeholder="İstifadəçi Adı" required>
                    <input type="password" name="password" placeholder="Şifrə" required>
                    <button type="submit" name="login" class="btn-main">GİRİŞ ET</button>
                </form>
            </div>

            <div id="register-form" class="form-content">
                <form method="POST">
                    <input type="text" name="username" placeholder="Yeni İstifadəçi Adı" required>
                    <input type="password" name="password" placeholder="Yeni Şifrə" required>
                    <button type="submit" name="register" class="btn-main" style="background:#2ecc71; color:white;">HESAB YARAT</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="header">
        <h1>👑 GOLD CRYPTO </h1>
        <div class="balance-display">
            <div style="text-align: left; margin-right: 20px;">
                <small style="color:#666">XOŞ GƏLDİNİZ, <?php echo strtoupper($user_data['username'] ?? ''); ?></small>
                <div style="font-size:22px; font-weight:bold; color:var(--gold)">$ <?php echo number_format($user_data['balance'] ?? 0, 2); ?></div>
            </div>
            <a href="?logout=1" class="logout-btn">ÇIXIŞ</a>
        </div>
    </div>

    <div class="coin-grid">
        <?php
        $coins = [['BTC', '64,120'], ['ETH', '3,450'], ['SOL', '145'], ['BNB', '580'], ['ADA', '0.45'], ['XRP', '0.62'], ['DOT', '7.20'], ['DOGE', '0.16']];
        foreach ($coins as $coin) {
            echo "
            <div class='coin-card'>
                <div style='font-weight:bold; color:var(--gold)'>$coin[0] / USD</div>
                <div style='font-size:22px; font-weight:bold; margin:10px 0;'>$ $coin[1]</div>
                <form method='POST'>
                    <input type='hidden' name='u_id' value='".$user_data['id']."'>
                    <input type='number' name='amount' placeholder='Məbləğ $' min='1' max='".($user_data['balance'] ?? 0)."' style='width:60%; padding:8px; background:#000; border:1px solid #333; color:white;'>
                    <button type='submit' name='buy_coin' class='buy-btn'>AL</button>
                </form>
            </div>";
        }
        ?>
    </div>
</div>

<script>
    function showForm(id, btn) {
        document.querySelectorAll('.form-content').forEach(f => f.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        btn.classList.add('active');
    }
</script>

</body>
</html>