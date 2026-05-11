<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Confirmació de Cita</title>

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

/* CONTENT */
.content {
    padding: 20px;
}

.greeting {
    margin-bottom: 10px;
    font-size: 14px;
}

.intro {
    margin-bottom: 20px;
    font-size: 13px;
    color: #555;
}

/* COLUMNS */
.col {
    width: 50%;
    vertical-align: top;
}

.col-left { padding-right: 8px; }
.col-right { padding-left: 8px; }

.section-title {
    font-size: 11px;
    color: #64748b;
    font-weight: bold;
    text-transform: uppercase;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 5px;
}

/* CARD */
.card {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px;
    font-size: 12px;
}

.label {
    color: #64748b;
}

.value {
    text-align: right;
}

.highlight {
    font-weight: bold;
    color: #0f5f7f;
}

/* NOTICE */
.notice {
    margin-top: 20px;
    background: #fefce8;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    padding: 12px;
    font-size: 12px;
    color: #92400e;
}

/* FOOTER */
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

<!-- HEADER -->
<tr>
<td class="header">
    <table width="100%">
        <tr>
            <td>
                <img src="{{ storage_path('app/public/FarmaciaSolerLogoTallDark.png') }}" class="logo">
            </td>
            <td align="right">
                <span class="badge">Reserva Confirmada</span>
            </td>
        </tr>
    </table>

    <p class="header-text">
        {{ $data['address'] }} · Tel. {{ $data['phone'] }}
    </p>
</td>
</tr>

<!-- BODY -->
<tr>
<td class="content">

<p class="greeting">Hola <strong>{{ $data['name'] }}</strong>,</p>

<p class="intro">La teva cita ha estat confirmada correctament.</p>

<table width="100%">
<tr>

<td class="col col-left">
    <p class="section-title">Detalls de la Cita</p>

    <table class="card">
        <tr>
            <td class="label">Servei</td>
            <td class="value highlight">{{ $data['service_name'] }}</td>
        </tr>
        <tr>
            <td class="label">Durada</td>
            <td class="value">{{ $data['duration'] }}</td>
        </tr>
        <tr>
            <td class="label">Data</td>
            <td class="value">{{ $data['date'] }}</td>
        </tr>
        <tr>
            <td class="label">Hora</td>
            <td class="value">{{ $data['time'] }}</td>
        </tr>
    </table>
</td>

<td class="col col-right">
    <p class="section-title">Dades del Client</p>

    <table class="card">
        <tr>
            <td class="label">Nom</td>
            <td class="value"><strong>{{ $data['name'] }}</strong></td>
        </tr>
        <tr>
            <td class="label">Correu</td>
            <td class="value">{{ $data['email'] }}</td>
        </tr>
    </table>
</td>

</tr>
</table>

<div class="notice">
<strong>Recordeu:</strong> Si no podeu assistir, cancel·leu amb almenys 24 hores d'antelació.
</div>

</td>
</tr>

<!-- FOOTER -->
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