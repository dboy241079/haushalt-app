<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Einladung zu FamilyHelper</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; overflow:hidden;">
        <div style="padding:24px; background:linear-gradient(135deg, #10b981, #14b8a6); color:#ffffff;">
            <p style="margin:0; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">
                FamilyHelper
            </p>
            <h1 style="margin:10px 0 0; font-size:24px; line-height:1.3;">
                Einladung zu einem Haushalt
            </h1>
        </div>

        <div style="padding:24px;">
            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                Hallo,
            </p>

            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                <strong>{{ $inviterName }}</strong> hat dich zum Haushalt
                <strong>{{ $householdName }}</strong> eingeladen.
            </p>

            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                Deine Rolle: <strong>{{ $invitation->role === 'admin' ? 'Admin' : 'Mitglied' }}</strong>
            </p>

            <p style="margin:0 0 24px; font-size:15px; line-height:1.6;">
                Klicke auf den Button, um die Einladung anzunehmen:
            </p>

            <p style="margin:0 0 24px;">
                <a href="{{ $acceptUrl }}"
                   style="display:inline-block; padding:14px 22px; background:#10b981; color:#ffffff; text-decoration:none; border-radius:12px; font-weight:bold;">
                    Einladung annehmen
                </a>
            </p>

            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#475569;">
                Oder kopiere diesen Link in deinen Browser:
            </p>

            <p style="margin:0 0 24px; font-size:13px; line-height:1.6; word-break:break-all; color:#334155;">
                {{ $acceptUrl }}
            </p>

            <p style="margin:0; font-size:13px; line-height:1.6; color:#64748b;">
                Diese Einladung läuft am
                <strong>{{ optional($invitation->expires_at)->format('d.m.Y H:i') }}</strong>
                ab.
            </p>
        </div>
    </div>
</body>
</html>