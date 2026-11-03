<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevOps Infrastructure - Airmedia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            margin: auto;
            text-align: center;
        }
        .badge {
            display: inline-block;
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 6px 18px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 24px;
        }
        h1 {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        p.subtitle {
            font-size: 1.125rem;
            color: #94a3b8;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }
        .card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            backdrop-filter: blur(10px);
        }
        .card h3 {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .card p {
            font-size: 1.05rem;
            font-weight: 600;
            color: #f1f5f9;
        }
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            margin-right: 6px;
            box-shadow: 0 0 10px #22c55e;
        }
        footer {
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">🚀 GitOps CI/CD Pipeline Active</div>
        <h1>Automated Web Deployment</h1>
        <p class="subtitle">
            Halaman ini di-deploy secara otomatis dari GitHub melalui alur CI/CD Webhook Portainer di atas server Debian VirtualBox.
        </p>

        <div class="grid">
            <div class="card">
                <h3>Status Container</h3>
                <p><span class="status-dot"></span> Active</p>
            </div>
            <div class="card">
                <h3>PHP Engine</h3>
                <p>v<?php echo phpversion(); ?></p>
            </div>
            <div class="card">
                <h3>Waktu Server</h3>
                <p><?php echo date('H:i:s'); ?> WIB</p>
            </div>
            <div class="card">
                <h3>Tanggal</h3>
                <p><?php echo date('d M Y'); ?></p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Airmedia Infrastructure Lab</p>
    </footer>
</body>
</html>
