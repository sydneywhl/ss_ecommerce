<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body class="login-body">
    <main>
        <section>   

        <div class="logo-container">
            <h1>SS E-commerce</h1>
        </div>

        <div class="form-box">
            <form action="create_account_process.php" method="post">
                <h2>Create Account</h2>

                <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Incorrect User ID or Password.</p>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required>
                <a href="log_in.php">Already have an account? Log in now!</a>
                <div class="button-container">
                    <button type="submit" name="signup">Signup</button>
                </div>

            </form>
        </div>
        </section>
    </main>
</body>
</html>