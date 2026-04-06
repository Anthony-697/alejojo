<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ALEJOJO V2</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            background: #1e293b;
            padding: 2rem;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #22c55e;
        }
        label {
            display: block;
            margin-bottom: 0.25rem;
            color: #94a3b8;
        }
        input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #334155;
            border-radius: 8px;
            background: #0f172a;
            color: #f1f5f9;
            margin-bottom: 1rem;
        }
        input:focus {
            outline: none;
            border-color: #22c55e;
        }
        button {
            width: 100%;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
            padding: 0.6rem;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
        }
        button:hover {
            transform: scale(1.02);
        }
        .alert {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .alert-error {
            background: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .demo-info {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🔐 ALEJOJO V2</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <label>Usuario</label>
        <input type="text" name="username" required autofocus>
        
        <label>Contraseña</label>
        <input type="password" name="password" required>
        
        <button type="submit">Ingresar</button>
    </form>
    
    <p class="demo-info">
        Demo: admin / admin123
    </p>
</div>

</body>
</html>