<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8"/>
<title>Nova Inscripció a Taller</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:DejaVu Sans,sans-serif;
    font-size:13px;
    color:#1e293b;
    background:#ffffff;
    padding:40px 48px;
}

.header{
    background-color:#0f5f7f;
    color:#ffffff;
    padding:20px 24px;
    border-radius:10px;
    margin-bottom:24px;
}

.header-inner{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header-text .subtitle{
    font-size:11px;
    opacity:0.8;
}

.badge-confirmed{
    background-color:#dcfce7;
    color:#166534;
    font-size:11px;
    font-weight:bold;
    padding:5px 14px;
    border-radius:20px;
    white-space:nowrap;
}

.section-label{
    font-size:10px;
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing:1.2px;
    color:#64748b;
    margin-bottom:8px;
    padding-bottom:5px;
    border-bottom:1px solid #e2e8f0;
}

.card{
    background-color:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:8px;
    padding:14px 18px;
    margin-bottom:18px;
}

.row{
    display:flex;
    justify-content:space-between;
    gap:10px;
}

.lbl{
    color:#64748b;
    font-size:12px;
}

.val{
    font-weight:bold;
    color:#0f172a;
    font-size:12px;
}

.row-highlight .val{
    color:#0f5f7f;
}

.notice{
    background-color:#fefce8;
    border:1px solid #fde68a;
    border-left:4px solid #f59e0b;
    border-radius:6px;
    padding:12px 16px;
    margin-bottom:20px;
    font-size:11px;
    color:#92400e;
    line-height:1.7;
}

.notice strong{
    color:#78350f;
}

.footer{
    margin-top:28px;
    text-align:center;
    font-size:10px;
    color:#94a3b8;
    border-top:1px solid #e2e8f0;
    padding-top:14px;
    line-height:1.6;
}
</style>
</head>

<body>

<div class="header">

    <div class="header-inner">

        <div class="header-text">
            <div class="subtitle">
                {{ $data['address'] }} · Tel. {{ $data['phone_pharmacy'] }}
            </div>
        </div>

        <div class="badge-confirmed">
            Nova Inscripció
        </div>

    </div>

</div>

<div class="section-label">
Resum de la notificació
</div>

<div class="card">

    <div class="row row-highlight">
        <span class="lbl">Estat</span>
        <span class="val">Nova inscripció rebuda correctament</span>
    </div>

    <div class="row">
        <span class="lbl">Nom</span>
        <span class="val">{{ $data['name'] }}</span>
    </div>

    <div class="row">
        <span class="lbl">Email</span>
        <span class="val">{{ $data['email'] }}</span>
    </div>

    <div class="row">
        <span class="lbl">Telèfon</span>
        <span class="val">{{ $data['phone'] }}</span>
    </div>

    <div class="row">
        <span class="lbl">Taller</span>
        <span class="val">{{ $data['workshop_name'] }}</span>
    </div>

    <div class="row">
        <span class="lbl">Data taller</span>
        <span class="val">{{ $data['workshop_date'] }}</span>
    </div>

    <div class="row">
        <span class="lbl">Horari</span>
        <span class="val">
            {{ $data['start_time'] }} - {{ $data['end_time'] }}
        </span>
    </div>

    <div class="row">
        <span class="lbl">Data notificació</span>
        <span class="val">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

</div>

<div class="notice">
<strong>Acció recomanada:</strong>
Reviseu la nova inscripció al taller des del panell administratiu.
Comproveu la disponibilitat de places i gestioneu la participació si és necessari.
</div>

<div class="footer">
Notificació interna generada automàticament per
<strong>{{ $data['pharmacy'] }}</strong>
<br/>
Aquest correu està destinat a l'equip administratiu.
</div>

</body>
</html>