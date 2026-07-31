<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyCampus Prestasi - Download APK</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a1a;
            color: #e1e1e1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 520px;
            width: 100%;
            padding: 20px 0;
        }
        .card {
            background: #161b22;
            border-radius: 16px;
            border: 1px solid rgba(56, 189, 248, 0.1);
            padding: 36px;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .logo {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(6,182,212,0.05);
            border: 2px solid rgba(6,182,212,0.4);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 0 24px rgba(6,182,212,0.25), 0 0 60px rgba(6,182,212,0.1);
        }
        .logo svg { width: 38px; height: 38px; fill: #06b6d4; filter: drop-shadow(0 0 6px rgba(6,182,212,0.6)); }
        h1 {
            text-align: center;
            font-size: 24px; font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .subtitle {
            text-align: center;
            font-size: 13px; font-weight: 500;
            color: rgba(225,225,225,0.5);
            margin-bottom: 28px;
        }
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56,189,248,0.2), transparent);
            margin: 24px 0;
        }
        .section-title {
            font-size: 13px; font-weight: 700;
            color: rgba(225,225,225,0.4);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 16px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .feature-item {
            background: rgba(6,182,212,0.06);
            border: 1px solid rgba(6,182,212,0.1);
            border-radius: 10px;
            padding: 14px;
            text-align: center;
            font-size: 13px; font-weight: 600;
            color: #cbd5e1;
        }
        .feature-icon { font-size: 22px; margin-bottom: 6px; display: block; }
        .url-box {
            background: rgba(6,182,212,0.06);
            border: 1px solid rgba(6,182,212,0.15);
            border-radius: 10px;
            padding: 14px 16px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 13px;
            color: #06b6d4;
            word-break: break-all;
            text-align: center;
        }
        .btn-download {
            display: block;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border: none;
            border-radius: 12px;
            color: #0a0a1a;
            font-size: 16px; font-weight: 700;
            text-align: center;
            text-decoration: none;
            margin-bottom: 4px;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(6,182,212,0.3);
        }
        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(6,182,212,0.4);
        }
        .btn-sub {
            display: block;
            text-align: center;
            font-size: 12px; color: rgba(225,225,225,0.3);
            margin-top: 8px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: rgba(225,225,225,0.2);
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <svg viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
            </div>
            <h1>MyCampus Prestasi</h1>
            <p class="subtitle">Sistem Informasi Prestasi Mahasiswa</p>

            <a href="MyCampus.apk" class="btn-download">
                📥 Download APK v1.0
            </a>
            <span class="btn-sub">File: MyCampus.apk (debug build)</span>

            <div class="divider"></div>

            <p class="section-title">✨ Fitur Aplikasi</p>
            <div class="feature-grid">
                <div class="feature-item"><span class="feature-icon">📊</span>Dashboard & Statistik</div>
                <div class="feature-item"><span class="feature-icon">👨‍🎓</span>Data Mahasiswa</div>
                <div class="feature-item"><span class="feature-icon">🏆</span>Prestasi & Verifikasi</div>
                <div class="feature-item"><span class="feature-icon">🧮</span>Kualitas Fuzzy</div>
                <div class="feature-item"><span class="feature-icon">📋</span>Activity Log</div>
                <div class="feature-item"><span class="feature-icon">👤</span>Profil & Hak Akses</div>
            </div>

            <div class="divider"></div>

            <p class="section-title">🔗 URL Server</p>
            <div class="url-box">https://vessel-swivel-acid.ngrok-free.dev/api</div>
        </div>

        <div class="footer">
            MyCampus Prestasi &copy; 2026
        </div>
    </div>
</body>
</html>
