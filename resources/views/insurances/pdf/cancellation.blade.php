<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Kündigung Versicherung</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.55;
            margin: 0;
            padding: 0;
        }

        .page {
            padding: 48px 56px;
        }

        .date-line {
            text-align: right;
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 28px;
        }

        .sender {
            font-size: 11px;
            color: #374151;
            margin-bottom: 28px;
        }

        .sender-line {
            margin-bottom: 2px;
        }

        .recipient {
            margin-bottom: 28px;
        }

        .recipient strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            color: #111827;
        }

        .subject {
            margin: 26px 0 18px;
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .meta-box {
            margin: 0 0 20px;
            padding: 10px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .meta-row {
            margin-bottom: 4px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        .body-text {
            margin-bottom: 14px;
        }

        .signature {
            margin-top: 34px;
        }
    </style>
</head>
<body>
    @php
        $insuredFullName = trim(($insurance->insured_first_name ?? '') . ' ' . ($insurance->insured_last_name ?? ''));
        $providerName = $insurance->provider ?: 'Versicherungsgesellschaft';
        $policyNumber = $insurance->policy_number ?: 'Versicherungsnummer fehlt';
        $insuranceType = $insurance->insurance_type ?: '-';
        $endDate = optional($insurance->ends_at)->format('d.m.Y') ?: '-';
    @endphp

    <div class="page">
        <div class="date-line">
            {{ $today->format('d.m.Y') }}
        </div>

        <div class="sender">
            <div class="sender-line">{{ $insuredFullName ?: 'Name fehlt' }}</div>
            <div class="sender-line">{{ $insurance->insured_street ?: 'Straße / Hausnummer fehlt' }}</div>
            <div class="sender-line">{{ $insurance->insured_zip ?: 'PLZ fehlt' }} {{ $insurance->insured_city ?: 'Ort fehlt' }}</div>

            @if($insurance->insured_email)
                <div class="sender-line">E-Mail: {{ $insurance->insured_email }}</div>
            @endif

            @if($insurance->insured_phone)
                <div class="sender-line">Telefon: {{ $insurance->insured_phone }}</div>
            @endif
        </div>

        <div class="recipient">
            <strong>{{ $providerName }}</strong>
            {{ $insurance->provider_street ?: 'Straße / Hausnummer fehlt' }}<br>
            {{ $insurance->provider_zip ?: 'PLZ fehlt' }} {{ $insurance->provider_city ?: 'Ort fehlt' }}<br>

            @if($insurance->provider_email)
                E-Mail: {{ $insurance->provider_email }}
            @endif
        </div>

        <div class="subject">
            Kündigung meiner Versicherung – {{ $policyNumber }}
        </div>

        <div class="meta-box">
            <div class="meta-row"><strong>Anbieter:</strong> {{ $providerName }}</div>
            <div class="meta-row"><strong>Versicherungsnummer:</strong> {{ $policyNumber }}</div>
            <div class="meta-row"><strong>Versicherungsart:</strong> {{ $insuranceType }}</div>
            <div class="meta-row"><strong>Versicherte Person:</strong> {{ $insuredFullName ?: '-' }}</div>
        </div>

        <div class="body-text">
            Sehr geehrte Damen und Herren,
        </div>

        <div class="body-text">
            hiermit kündige ich meine oben genannte Versicherung fristgerecht zum nächstmöglichen Zeitpunkt,
            hilfsweise zum Vertragsende am {{ $endDate }}.
        </div>

        <div class="body-text">
            Bitte bestätigen Sie mir die Kündigung schriftlich unter Angabe des Beendigungszeitpunkts.
        </div>

        @if(!empty($insurance->notes))
            <div class="body-text">
                Zusätzliche Vertragsnotiz: {{ $insurance->notes }}
            </div>
        @endif

        <div class="signature">
            Mit freundlichen Grüßen
            <br><br>
            {{ $insuredFullName ?: 'Name fehlt' }}
        </div>
    </div>
</body>
</html>