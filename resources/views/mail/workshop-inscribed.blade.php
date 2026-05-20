<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Confirmació d'Inscripció</title>

<style>
body {
    margin: 0;
    padding: 0;
    background: #f3f4f6;
    font-family: Arial, sans-serif;
}

.wrapper {
    width: 100%;
    padding: 30px 0;
}

.container {
    width: 600px;
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
}

/* HEADER */
.header {
    background: #0f5f7f;
    color: #ffffff;
    padding: 20px;
}

.logo {
    height: 52px;
    width: auto;
    object-fit: contain;
}

.badge {
    background: #dcfce7;
    color: #166534;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.header-text {
    margin-top: 10px;
    font-size: 12px;
    opacity: 0.9;
}

.content {
    padding: 30px 20px;
}

.greeting {
    margin-bottom: 12px;
    font-size: 14px;
}

.intro {
    margin-bottom: 20px;
    font-size: 13px;
    color: #555;
    line-height: 1.7;
}

.card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 18px;
    font-size: 13px;
    color: #334155;
    line-height: 1.7;
}

.notice {
    margin-top: 20px;
    background: #fefce8;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    padding: 12px;
    font-size: 12px;
    color: #92400e;
}

.footer {
    text-align: center;
    padding: 15px;
    font-size: 11px;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
}
</style>

</head>

<body>

<table class="wrapper">
<tr>
<td align="center">

<table class="container">

<tr>
<td class="header">

<table width="100%">
<tr>
<td>
<img src="{{ storage_path('app/public/FarmaciaSolerLogoTallDark.png') }}" class="logo">
</td>

<td align="right">
<span class="badge">Inscripció Confirmada</span>
</td>
</tr>
</table>

<p class="header-text">
{{ $data['address'] }} · Tel. {{ $data['phone_pharmacy'] }}
</p>
</td>
</tr>

<tr>
<td class="content">

<p class="greeting">
Hola <strong>{{ $data['name'] }}</strong>,
</p>

<p class="intro">
La teva inscripció al taller s'ha registrat correctament.
</p>

<div class="card">
    <strong>Taller:</strong> {{ $data['workshop_name'] }}<br>
    <strong>Data:</strong> {{ $data['workshop_date'] }}<br>
    <strong>Hora:</strong> {{ $data['start_time'] }} - {{ $data['end_time'] }}<br><br>

    Gràcies per inscriure't al nostre taller.

    Hem rebut correctament la teva sol·licitud i la teva plaça ha estat confirmada.
</div>

<div class="notice">
<strong>Important:</strong> Conserva aquest correu com a comprovant de la teva inscripció.
</div>

</td>
</tr>

<tr>
<td class="footer">
Document generat automàticament per {{ $data['pharmacy'] }}<br>
{{ now()->format('d/m/Y H:i') }}
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>