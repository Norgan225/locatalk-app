<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LocaTalk')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        .email-logo {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .email-content {
            padding: 40px 30px;
        }
        .email-content h2 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .email-content p {
            margin-bottom: 15px;
            font-size: 16px;
            color: #555;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 15px;
        }
        .info-box strong {
            color: #333;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            font-size: 14px;
            color: #777;
            margin: 5px 0;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 30px 0;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert p {
            color: #856404;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="email-logo">📍</div>
            <h1>LocaTalk</h1>
        </div>

        <!-- Content -->
        <div class="email-content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>LocaTalk</strong></p>
            <p>Plateforme de communication et collaboration d'entreprise</p>
            <div class="divider" style="margin: 15px auto; width: 50%;"></div>
            <p style="font-size: 12px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
            <p style="font-size: 12px; color: #999;">
                © {{ date('Y') }} LocaTalk. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
