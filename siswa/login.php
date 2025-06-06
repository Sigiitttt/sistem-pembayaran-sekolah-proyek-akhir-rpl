<?php
session_start();
if (isset($_SESSION['siswa_id'])) {
    header("Location: ../halaman/home.php");
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/admin.php");
    exit();
}

$error = '';
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = '<div class="error-message">Nama/NISN atau Username/Password salah!</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login Page</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
  * {
    box-sizing: border-box;
  }
  body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(180deg, #0D5DC7 0%, #8B00B9 100%);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .container {
    background: #fff;
    width: 90vw;
    max-width: 900px;
    height: 400px;
    display: flex;
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    border-radius: 4px;
    overflow: hidden;
  }
  .left-panel {
    flex: 1;
    background: linear-gradient(135deg, #5A8DD9 0%, #C55BFF 100%);
    color: white;
    padding: 20px 20px 20px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 4px 0 0 4px;
    position: relative;
  }
  .error-message {
    color: #ff3333;
    font-size: 12px;
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
  }
  .logo {
    display: flex;
    align-items: center;
    text-align: center;
    gap: 10px;
    font-weight: 400;
    margin-top: 10px;
    font-size: 14px;
  }
  .logo-circle {
    width: 60%;
    height: 60%;
  }
  .welcome-text {
    font-size: 24px;
    font-weight: 700;
    margin-top: 40px;
    text-align: center;
  }
  .sign-in-text {
    /* font-size: 10px;
    font-weight: 700; */
    margin-top: 10px;
    line-height: 1.2;
    opacity: 0.8;
    text-align: center;
  }
  .company-text {
    font-size: 12px;
    font-weight: 400;
    align-self: flex-end;
    opacity: 0.8;
  }
  .right-panel {
    flex: 1;
    padding: 40px 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .login-title {
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 30px;
  }
  .input-group {
    position: relative;
    margin-bottom: 25px;
  }
  .input-group input {
    width: 100%;
    border: none;
    border-bottom: 1px solid #999;
    padding: 8px 30px 8px 25px;
    font-size: 12px;
    color: #333;
    outline: none;
    font-family: 'Roboto', sans-serif;
  }
  .input-group input::placeholder {
    color: #999;
    font-size: 12px;
  }
  .input-group .fa {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    color: #999;
  }
  .btn-login {
    width: 100%;
    background: linear-gradient(90deg, #2CB1C9 0%, #FF00FF 100%);
    border: none;
    border-radius: 20px;
    color: white;
    font-weight: 700;
    font-size: 12px;
    padding: 10px 0;
    cursor: pointer;
    letter-spacing: 1px;
    transition: all 0.3s ease;
  }
  .btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }
  .login-type-toggle {
    text-align: center;
    margin-top: 15px;
    font-size: 12px;
  }
  .login-type-toggle a {
    color: #2CB1C9;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
  }
  .login-type-toggle a:hover {
    text-decoration: underline;
  }
  
  @media (max-width: 600px) {
    .container {
      flex-direction: column;
      height: auto;
      max-width: 400px;
    }
    .left-panel, .right-panel {
      border-radius: 4px 4px 0 0;
      padding: 30px 20px;
      flex: none;
      width: 100%;
    }
    .right-panel {
      padding-top: 20px;
      padding-bottom: 40px;
    }
    .left-panel {
      border-radius: 4px 4px 0 0;
    }
  }
</style>
</head>
<body>
  <div class="container" role="main" aria-label="Login form container">
    <section class="left-panel" aria-label="Welcome panel with logo and company info">
        <div class="logo" aria-label="Company logo">
            <div ><img class="logo-circle" aria-hidden="true" src="https://smkntrucukbjn.sch.id/wp-content/uploads/2021/03/cropped-TRUCUK-EVO-2-min.png" alt=""></div>
            <span></span>
        </div>
        <div>
            <h1 class="welcome-text">Welcome to Page</h1>
            <h4 class="sign-in-text">Sign in to continue access</h4>
        </div>
        <!-- <a class="company-text" href="https://smkntrucukbjn.sch.id/" target="_blank" rel="noopener noreferrer">
          www.SMKNtrucuk.com
        </a> -->
    </section>
    <section class="right-panel" aria-label="Login form">
      <h2 class="login-title">Login Siswa</h2>
      <?php echo $error; ?>
      <form action="../proses/proses_login.php" method="POST" id="loginForm">
        <input type="hidden" name="login_type" value="siswa" id="loginType">
        <div class="input-group" id="siswaNameGroup">
          <i class="fa fa-user" aria-hidden="true"></i>
          <input type="text" name="nama" placeholder="Masukkan nama lengkap" aria-label="Nama" required />
        </div>
        <div class="input-group" id="siswaNisnGroup">
          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" name="nisn" placeholder="Masukkan NISN" aria-label="NISN" required />
        </div>
        <div class="input-group" id="adminUserGroup" style="display: none;">
          <i class="fa fa-user" aria-hidden="true"></i>
          <input type="text" name="username" placeholder="Masukkan username" aria-label="Username" />
        </div>
        <div class="input-group" id="adminPassGroup" style="display: none;">
          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" name="password" placeholder="Masukkan password" aria-label="Password" />
        </div>
        <button type="submit" class="btn-login" aria-label="Login button">LOGIN</button>
      </form>
      <div class="login-type-toggle">
        <a id="toggleLoginType" onclick="toggleLoginType()">Login sebagai Admin</a>
      </div>
    </section>
  </div>

  <script>
    function toggleLoginType() {
      const loginType = document.getElementById('loginType');
      const toggleLink = document.getElementById('toggleLoginType');
      
      if (loginType.value === 'siswa') {
        // Switch to admin login
        loginType.value = 'admin';
        toggleLink.textContent = 'Login sebagai Siswa';
        document.querySelector('.login-title').textContent = 'Login Admin';
        document.getElementById('siswaNameGroup').style.display = 'none';
        document.getElementById('siswaNisnGroup').style.display = 'none';
        document.getElementById('adminUserGroup').style.display = 'block';
        document.getElementById('adminPassGroup').style.display = 'block';
        
        // Make admin fields required
        document.querySelector('input[name="username"]').required = true;
        document.querySelector('input[name="password"]').required = true;
        // Make student fields not required
        document.querySelector('input[name="nama"]').required = false;
        document.querySelector('input[name="nisn"]').required = false;
      } else {
        // Switch to student login
        loginType.value = 'siswa';
        toggleLink.textContent = 'Login sebagai Admin';
        document.querySelector('.login-title').textContent = 'Login Siswa';
        document.getElementById('siswaNameGroup').style.display = 'block';
        document.getElementById('siswaNisnGroup').style.display = 'block';
        document.getElementById('adminUserGroup').style.display = 'none';
        document.getElementById('adminPassGroup').style.display = 'none';
        
        // Make student fields required
        document.querySelector('input[name="nama"]').required = true;
        document.querySelector('input[name="nisn"]').required = true;
        // Make admin fields not required
        document.querySelector('input[name="username"]').required = false;
        document.querySelector('input[name="password"]').required = false;
      }
    }
  </script>
</body>
</html>