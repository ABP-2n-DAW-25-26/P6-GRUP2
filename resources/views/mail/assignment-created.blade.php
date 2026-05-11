<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8"/>
    <title>Confirmació del Encarrec</title>
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

     
        .header-text h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .header-text .subtitle {
            font-size: 11px;
            margin-top: 3px;
            opacity: 0.8;
        }
        .badge-confirmed {
            background-color: #dcfce7;
            color: #166534;
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
            gap: 10px;
        }
        .row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .row:first-child {
            padding-top: 0;
        }
        .row .lbl {
            flex: 0 0 110px;
            color: #64748b;
            font-size: 12px;
        }
        .row .val {
            font-weight: bold;
            color: #0f172a;
            font-size: 12px;
            text-align: left;
            white-space: pre-line;
            overflow-wrap: anywhere;       
            word-break: break-word;
            line-height: 1.45;
        }
        .row-highlight .val {
            color: #0f5f7f;
            font-size: 12px;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .two-col td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .two-col td:first-child {
            padding-right: 8px;
        }
        .two-col td:last-child {
            padding-left: 8px;
        }

        .captcha-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 18px;
            text-align: center;
            margin-bottom: 18px;
        }
        .captcha-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .captcha-inner {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #ffffff;
            height: 56px;
            line-height: 56px;
            text-align: center;
        }
        .captcha-text {
            color: #cbd5e1;
            font-size: 11px;
        }
        .captcha-note {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 6px;
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

            

                <div class="header-text">
                    <div class="subtitle">Carrer Nou, 22, 17600 Figueres, Girona, Spain</div>
                </div>
            </div>

            <div class="badge-confirmed"> Encarrec Confirmada</div>
        </div>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <div class="section-label">Detalls del encarrec</div>
                <div class="card">
                    <div class="row row-highlight">
                        <span class="lbl">Encarrec: </span>
                        <span class="val">{{ $assignment['description'] }}</span>
                    </div>
                </div>
            </td>

            <td>
                <div class="section-label">Dades del Client</div>
                <div class="card">
                    <div class="row">
                        <span class="lbl">Nom</span>
                        <span class="val">{{ $assignment['name'] }}</span>
                    </div>
                    <div class="row">
                        <span class="lbl">Telefón</span>
                        <span class="val">{{ $assignment['phone_number'] }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="notice">
        <strong>Recordeu:</strong> Si no podeu assistir a recolir l'encarrec, si us plau cancel·leu-la amb
        almenys <strong>24 hores</strong> d'antelació trucant al <strong>972 50 02 99</strong>
        o visitant la farmàcia en persona. Gràcies per la vostra col·laboració.
    </div>

    <div class="footer">
        Document generat automàticament per <strong>PHARMACY NAME</strong>
        <br/>
        Aquest document és vàlid com a comprovant d'Encarrec. Porteu-lo el dia de la recollida.
    </div>

</body>
</html>