<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Certificat: {{ $certificate->formation_title }}</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1A1A2E;
            width: 297mm;
            height: 210mm;
        }
        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            background: #F8F5F0;
        }
        .border-outer {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 3px solid #2D6A4F;
        }
        .border-inner {
            position: absolute;
            top: 14mm;
            left: 14mm;
            right: 14mm;
            bottom: 14mm;
            border: 1px solid #F4C842;
        }
        .accent-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 18mm;
            background: linear-gradient(90deg, #2D6A4F 0%, #40916C 45%, #52B788 100%);
        }
        .accent-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8mm;
            background: linear-gradient(90deg, #F4C842 0%, #E85D04 50%, #D4A017 100%);
        }
        .corner {
            position: absolute;
            width: 28mm;
            height: 28mm;
            opacity: 0.12;
        }
        .corner-tl { top: 18mm; left: 18mm; border-top: 6px solid #2D6A4F; border-left: 6px solid #2D6A4F; }
        .corner-br { bottom: 18mm; right: 18mm; border-bottom: 6px solid #2D6A4F; border-right: 6px solid #2D6A4F; }
        .content {
            position: absolute;
            top: 22mm;
            left: 20mm;
            right: 20mm;
            bottom: 16mm;
            text-align: center;
        }
        .logo-wrap {
            margin-bottom: 4mm;
        }
        .logo-wrap img {
            height: 22mm;
            width: auto;
        }
        .logo-text {
            font-size: 22pt;
            font-weight: bold;
            color: #2D6A4F;
            letter-spacing: 2px;
        }
        .org-line {
            font-size: 8pt;
            color: #40916C;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 1mm;
        }
        .cert-label {
            margin-top: 5mm;
            font-size: 11pt;
            color: #E85D04;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: bold;
        }
        .cert-title {
            margin-top: 2mm;
            font-size: 20pt;
            color: #2D6A4F;
            font-weight: bold;
            line-height: 1.25;
        }
        .awarded {
            margin-top: 5mm;
            font-size: 10pt;
            color: #555;
        }
        .learner-name {
            margin-top: 3mm;
            font-size: 26pt;
            font-weight: bold;
            color: #1A1A2E;
            border-bottom: 2px solid #F4C842;
            display: inline-block;
            padding: 0 8mm 2mm;
            min-width: 80mm;
        }
        .learner-org {
            margin-top: 3mm;
            font-size: 10pt;
            color: #666;
            font-style: italic;
        }
        .formation-block {
            margin-top: 6mm;
            padding: 4mm 10mm;
            background: rgba(45, 106, 79, 0.08);
            border-left: 4px solid #2D6A4F;
            border-right: 4px solid #2D6A4F;
            display: inline-block;
            max-width: 220mm;
        }
        .formation-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #40916C;
        }
        .formation-title {
            margin-top: 2mm;
            font-size: 14pt;
            font-weight: bold;
            color: #2D6A4F;
            line-height: 1.3;
        }
        .meta-row {
            margin-top: 8mm;
            width: 100%;
        }
        .meta-row table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-row td {
            width: 33.33%;
            vertical-align: top;
            padding: 0 3mm;
        }
        .meta-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
        }
        .meta-value {
            margin-top: 1mm;
            font-size: 9pt;
            font-weight: bold;
            color: #1A1A2E;
        }
        .signature-line {
            margin-top: 6mm;
            font-size: 8pt;
            color: #666;
        }
        .tagline {
            margin-top: 2mm;
            font-size: 7pt;
            color: #40916C;
            font-style: italic;
        }
        .leaf {
            position: absolute;
            font-size: 36pt;
            color: #52B788;
            opacity: 0.15;
        }
        .leaf-1 { top: 45mm; left: 25mm; transform: rotate(-25deg); }
        .leaf-2 { top: 50mm; right: 28mm; transform: rotate(20deg); }
    </style>
</head>
<body>
<div class="page">
    <div class="accent-top"></div>
    <div class="accent-bottom"></div>
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="corner corner-tl"></div>
    <div class="corner corner-br"></div>
    <span class="leaf leaf-1">&#127807;</span>
    <span class="leaf leaf-2">&#127807;</span>

    <div class="content">
        <div class="logo-wrap">
            @if($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="{{ config('brand.logo_alt') }}">
            @else
                <div class="logo-text">{{ config('brand.name', '3AO') }}</div>
            @endif
            <div class="org-line">{{ config('brand.tagline', 'Alliance pour l\'Agroécologie en Afrique de l\'Ouest') }}</div>
        </div>

        <div class="cert-label">Certificat de réussite</div>
        <div class="cert-title">Parcours de formation en ligne</div>

        <p class="awarded">Ce certificat atteste que</p>
        <div class="learner-name">{{ $certificate->learner_name }}</div>
        @if($certificate->learner_organization)
            <p class="learner-org">{{ $certificate->learner_organization }}</p>
        @endif
        <p class="awarded" style="margin-top: 4mm;">a complété avec succès la formation</p>

        <div class="formation-block">
            <div class="formation-label">Formation</div>
            <div class="formation-title">{{ $certificate->formation_title }}</div>
        </div>

        <div class="meta-row">
            <table>
                <tr>
                    <td>
                        <div class="meta-label">Date de délivrance</div>
                        <div class="meta-value">{{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                    </td>
                    <td>
                        <div class="meta-label">N° de certificat</div>
                        <div class="meta-value">{{ $certificate->certificate_number }}</div>
                    </td>
                    <td>
                        <div class="meta-label">Apprenant</div>
                        <div class="meta-value">{{ $certificate->learner_email }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <p class="signature-line">Plateforme collaborative 3AO: Transition agroécologique en Afrique de l'Ouest</p>
        <p class="tagline">{{ config('brand.tagline') }}</p>
    </div>
</div>
</body>
</html>
