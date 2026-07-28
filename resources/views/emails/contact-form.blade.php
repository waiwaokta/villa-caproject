<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f2; padding:32px; margin:0;">
    <div style="max-width:520px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; border:1px solid #e8e8e4;">
        <div style="background:#0C447C; padding:20px 28px;">
            <h2 style="color:#fff; margin:0; font-size:16px;">Pesan Baru dari Form Kontak Wisma PLN</h2>
        </div>
        <div style="padding:28px;">
            <p style="margin:0 0 6px; font-size:12px; color:#6b6b68; text-transform:uppercase; letter-spacing:0.04em;">Nama</p>
            <p style="margin:0 0 18px; font-size:14px; color:#1a1a18;">{{ $name }}</p>

            <p style="margin:0 0 6px; font-size:12px; color:#6b6b68; text-transform:uppercase; letter-spacing:0.04em;">Email</p>
            <p style="margin:0 0 18px; font-size:14px; color:#1a1a18;">{{ $email }}</p>

            <p style="margin:0 0 6px; font-size:12px; color:#6b6b68; text-transform:uppercase; letter-spacing:0.04em;">Pesan</p>
            <p style="margin:0; font-size:14px; color:#1a1a18; line-height:1.7; white-space:pre-line;">{{ $description }}</p>
        </div>
    </div>
</body>
</html>