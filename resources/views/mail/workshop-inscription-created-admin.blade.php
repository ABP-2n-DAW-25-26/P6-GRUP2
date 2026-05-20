<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova inscripció al taller</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">
<div style="width:100%;padding:30px 0;">
    <div style="width:600px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden;">
        <div style="background:#0f5f7f;color:#fff;padding:24px;text-align:center;">
            <span style="display:inline-block;background:#fef3c7;color:#92400e;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:bold;">Nova inscripció</span>
            <h1 style="margin:8px 0 4px 0;font-size:20px;">Inscripció a un taller</h1>
            <p style="margin:0;font-size:12px;opacity:0.9;">{{ $data['pharmacy'] }}</p>
        </div>
        <div style="padding:24px;color:#0f172a;">
            <p style="font-size:13px;color:#555;">
                Acaba d'arribar una nova inscripció al taller <strong>{{ $data['workshop_name'] }}</strong>.
            </p>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:16px;font-size:13px;">
                <div style="padding:4px 0;"><span style="color:#64748b;">Taller:</span> <strong style="color:#0f5f7f;">{{ $data['workshop_name'] }}</strong></div>
                <div style="padding:4px 0;"><span style="color:#64748b;">Data:</span> <strong style="color:#0f5f7f;">{{ $data['workshop_date'] }}</strong></div>
                <div style="padding:4px 0;"><span style="color:#64748b;">Horari:</span> <strong style="color:#0f5f7f;">{{ $data['start_time'] }} – {{ $data['end_time'] }}</strong></div>
            </div>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;font-size:13px;">
                <div style="padding:4px 0;"><span style="color:#64748b;">Nom:</span> <strong>{{ $data['name'] }}</strong></div>
                <div style="padding:4px 0;"><span style="color:#64748b;">Correu:</span> {{ $data['email'] }}</div>
                <div style="padding:4px 0;"><span style="color:#64748b;">Telèfon:</span> {{ $data['phone'] }}</div>
            </div>
        </div>
        <div style="background:#f1f5f9;color:#64748b;padding:16px 24px;font-size:11px;text-align:center;">
            {{ $data['pharmacy'] }} — Notificació interna.
        </div>
    </div>
</div>
</body>
</html>
