<?php
session_start();

// Admin Password (set as requested)
$password = "200002";
$configFile = 'config.json';

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid Password!";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Check Login
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle Form Submission
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_config'])) {
    $newUrl = trim($_POST['download_url']);
    
    $configData = ['download_url' => $newUrl];
    if (file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT))) {
        $success = "Download link updated successfully! Ab yeh naya link sabhi users ko dikhega.";
    } else {
        $error = "Failed to update config.json. Please check file permissions on your hosting.";
    }
}

// Read Current Config
$currentUrl = "HSBC INDIA.apk"; // default
if (file_exists($configFile)) {
    $configData = json_decode(file_get_contents($configFile), true);
    if (isset($configData['download_url'])) {
        $currentUrl = $configData['download_url'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Update Links</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0056b3;
            --primary-hover: #004494;
            --bg: #f4f7f6;
            --white: #ffffff;
            --text: #333333;
            --border: #dddddd;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: var(--text);
        }
        .admin-container { 
            background: var(--white); 
            padding: 2.5rem 2rem; 
            border-radius: 12px; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 450px; 
            text-align: center;
        }
        .admin-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        h1 { 
            margin-top: 0; 
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .form-group { 
            margin-bottom: 1.5rem; 
            text-align: left;
        }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: 500;
            font-size: 0.95rem;
        }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 0.85rem; 
            border: 1px solid var(--border); 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: var(--primary);
            outline: none;
        }
        button { 
            width: 100%; 
            padding: 0.85rem; 
            background-color: var(--primary); 
            color: var(--white); 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 1.05rem; 
            font-weight: 600;
            transition: background-color 0.3s;
        }
        button:hover { 
            background-color: var(--primary-hover); 
        }
        .alert { 
            padding: 0.85rem; 
            margin-bottom: 1.5rem; 
            border-radius: 6px; 
            font-size: 0.95rem;
            text-align: left;
        }
        .alert-danger { 
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .alert-success { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .logout-link { 
            display: inline-block; 
            margin-top: 1.5rem; 
            color: #dc3545; 
            text-decoration: none; 
            font-weight: 500;
            transition: color 0.3s;
        }
        .logout-link:hover { 
            color: #a71d2a; 
            text-decoration: underline; 
        }
        .help-text {
            font-size: 0.85rem;
            color: #777;
            margin-top: 0.4rem;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="admin-icon">
        <i class="fa-solid fa-user-shield"></i>
    </div>
    <h1>Website Admin Panel</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!$isLoggedIn): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="password">Enter Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter admin password">
            </div>
            <button type="submit" name="login"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login</button>
        </form>
    <?php else: ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="download_url">Download Button Link</label>
                <input type="text" id="download_url" name="download_url" value="<?php echo htmlspecialchars($currentUrl); ?>" required placeholder="e.g. app.apk or https://google.com">
                <div class="help-text">Enter an APK filename or any web URL (http://...)</div>
            </div>
            <button type="submit" name="update_config"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </form>
        <a href="?logout=1" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
    <?php endif; ?>
</div>

</body>
</html>
