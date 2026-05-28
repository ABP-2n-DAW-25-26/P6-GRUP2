<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8" />
    <title>Confirmació d'Encàrrec</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #ffffff;
            padding: 40px 48px;
        }

        .header {
            background-color: #0f5f7f;
            color: #ffffff;
            padding: 20px 24px;
            border-radius: 10px;
            margin-bottom: 24px;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-text .subtitle {
            font-size: 11px;
            margin-top: 3px;
            opacity: 0.8;
        }

        .badge {
            background-color: #fef9c3;
            color: #854d0e;
            font-size: 11px;
            font-weight: bold;
            padding: 5px 14px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .section-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 7px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .row:first-child {
            padding-top: 0;
        }

        .row .lbl {
            color: #64748b;
            font-size: 12px;
            white-space: nowrap;
            padding-right: 16px;
        }

        .row .val {
            font-weight: bold;
            color: #0f172a;
            font-size: 12px;
            text-align: right;
        }

        .description-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }

        .description-text {
            font-size: 12px;
            color: #1e293b;
            line-height: 1.7;
            white-space: pre-wrap;
        }

        .notice {
            background-color: #fefce8;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #92400e;
            line-height: 1.7;
        }

        .notice strong {
            color: #78350f;
        }

        .footer {
            margin-top: 28px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <img
                    src="{{ storage_path('app/public/FarmaciaSolerLogoTallDark.png') }}"
                    alt="Logo Farmàcia Soler"
                    style="height: 52px; width: auto; object-fit: contain;" />
                <div class="header-text">
                    <div class="subtitle">{{ $pharmacy_address }} &nbsp;·&nbsp; Tel. {{ $phone }}</div>
                </div>
            </div>
            <div class="badge">Encàrrec Pendent</div>
        </div>
    </div>

    <div class="section-label">Dades del Client</div>
    <div class="card">
        <div class="row">
            <span class="lbl">Nom</span>
            <span class="val">{{ $name }}</span>
        </div>
        <div class="row">
            <span class="lbl">Correu electrònic</span>
            <span class="val">{{ $address }}</span>
        </div>
        <div class="row">
            <span class="lbl">Telèfon</span>
            <span class="val">{{ $phone_number }}</span>
        </div>
    </div>

    <div class="section-label">Descripció de l'encàrrec</div>
    <div class="description-card">
        <p class="description-text">{{ $description }}</p>
    </div>

    <div class="notice">
        <strong>Recordeu:</strong> L'encàrrec està pendent de confirmació per part de la farmàcia.
        Us contactarem per correu electrònic o telèfon quan estigui llest per recollir.
        Per a qualsevol consulta, podeu trucar al <strong>{{ $phone }}</strong> o visitar la farmàcia en persona.
    </div>

    <div class="footer">
        Document generat automàticament per <strong>{{ $pharmacy }}</strong>
        &nbsp;·&nbsp;
        {{ now()->format('d/m/Y \a \l\e\s H:i') }} h
        <br />
        Aquest document és vàlid com a comprovant de sol·licitud d'encàrrec.
    </div>

</body>

</html>
