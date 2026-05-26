@php
    $builder = app(\App\Services\NewsletterContentBuilder::class);
    $mailMessage = $message ?? null;
    $logoUrl = $builder->publicAssetUrl(config('brand.logo'), $mailMessage);
    $bannerUrl = $builder->publicAssetUrl(config('brand.newsletter_banner', config('brand.logo')), $mailMessage);
    $siteUrl = $siteUrl ?? url('/');
    $tagline = config('brand.tagline');
    $green = config('brand.colors.green', '#2D6A4F');
    $orange = config('brand.colors.orange', '#E85D04');
    $bodyHtml = $builder->buildHtml($campaign, $mailMessage);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f5;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6f5;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(26,26,46,0.12);">

    {{-- Bandeau visuel (inspiré banner 3AO) --}}
    <tr>
        <td style="padding:0;line-height:0;">
            <a href="{{ $siteUrl }}" style="text-decoration:none;display:block;">
                <img src="{{ $bannerUrl }}" alt="{{ config('brand.logo_alt') }}" width="600"
                     style="display:block;width:100%;max-width:600px;height:auto;border:0;">
            </a>
        </td>
    </tr>

    {{-- Motif kente --}}
    <tr>
        <td style="padding:0;height:6px;line-height:6px;font-size:0;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="25%" style="background:#D12027;height:6px;">&nbsp;</td>
                    <td width="25%" style="background:#F9B233;height:6px;">&nbsp;</td>
                    <td width="25%" style="background:#009245;height:6px;">&nbsp;</td>
                    <td width="25%" style="background:#1A1A2E;height:6px;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- En-tête logo + slogan --}}
    <tr>
        <td style="padding:24px 28px 8px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="80" valign="top" style="padding-right:16px;">
                        <img src="{{ $logoUrl }}" alt="3AO" width="72" height="72"
                             style="display:block;border-radius:8px;border:2px solid {{ $green }};">
                    </td>
                    <td valign="middle">
                        <p style="margin:0 0 4px;font-size:22px;font-weight:800;color:#1A1A2E;letter-spacing:-0.02em;">
                            Newsletter 3AO
                        </p>
                        <p style="margin:0;font-size:13px;line-height:1.5;color:{{ $orange }};font-weight:600;">
                            {{ $tagline }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Objet / intro --}}
    <tr>
        <td style="padding:8px 28px 20px;">
            <h1 style="margin:0 0 12px;font-size:20px;line-height:1.35;color:{{ $green }};font-weight:700;">
                {{ $campaign->subject }}
            </h1>
            @if($campaign->intro_html)
                <p style="margin:0;font-size:15px;line-height:1.65;color:#444444;">
                    {!! nl2br(e($campaign->intro_html)) !!}
                </p>
            @endif
        </td>
    </tr>

    {{-- Contenu dynamique --}}
    <tr>
        <td style="padding:0 28px 28px;">
            {!! $bodyHtml !!}
        </td>
    </tr>

    {{-- CTA plateforme --}}
    <tr>
        <td align="center" style="padding:0 28px 28px;">
            <a href="{{ $siteUrl }}"
               style="display:inline-block;padding:14px 32px;background:{{ $orange }};color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:999px;">
                Visiter la plateforme 3AO
            </a>
        </td>
    </tr>

    {{-- Pied de page --}}
    <tr>
        <td style="background:#1A1A2E;padding:20px 28px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding-bottom:12px;">
                        <table role="presentation" width="120" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30" style="background:#D12027;height:4px;">&nbsp;</td>
                                <td width="30" style="background:#F9B233;height:4px;">&nbsp;</td>
                                <td width="30" style="background:#009245;height:4px;">&nbsp;</td>
                                <td width="30" style="background:#F4C842;height:4px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="margin:0 0 8px;font-size:12px;line-height:1.5;color:rgba(255,255,255,0.85);">
                            <strong style="color:#fff;">3AO</strong> — Alliance pour l'Agroécologie en Afrique de l'Ouest
                        </p>
                        <p style="margin:0;font-size:11px;line-height:1.5;color:rgba(255,255,255,0.55);">
                            Vous recevez cet e-mail car vous êtes inscrit(e) à notre newsletter.
                            <a href="{{ $unsubscribeUrl }}" style="color:#F4C842;text-decoration:underline;">Se désabonner</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
