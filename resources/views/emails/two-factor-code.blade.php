<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; background: #f5f0e8; margin: 0; padding: 2rem; }
        .container { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .logo { font-size: 1.6rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; }
        .logo span { color: #e8d44d; }
        h2 { color: #1a1a1a; font-size: 1.2rem; margin-bottom: 0.5rem; }
        p { color: #555; font-size: 0.9rem; line-height: 1.6; }
        .code-box { background: #1a1a1a; color: #e8d44d; font-size: 2.5rem; font-weight: 700; letter-spacing: 0.5rem; text-align: center; padding: 1.2rem; border-radius: 12px; margin: 1.5rem 0; }
        .footer { margin-top: 1.5rem; font-size: 0.78rem; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">dash<span>board</span></div>

        <h2>Código de verificación</h2>
        <p>Hola <strong>{{ $user->name }}</strong>, usa el siguiente código para completar tu inicio de sesión:</p>

        <div class="code-box">{{ $user->two_factor_code }}</div>

        <p>Este código es válido por <strong>10 minutos</strong>. Si no solicitaste esto, ignora este correo.</p>

        <div class="footer">Dashboard Laravel — No respondas a este correo.</div>
    </div>
</body>
</html>