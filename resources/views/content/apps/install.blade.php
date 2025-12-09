<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
<link rel="manifest" href="/manifest.json">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Install Aplikasi</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">

    <h2>Install Aplikasi</h2>
    <p>Klik tombol di bawah untuk memasang aplikasi ke perangkat Anda.</p>

    <button id="installBtn" 
            style="padding: 12px 20px; background: #4f46e5; color: white; 
                   border: none; border-radius: 8px;">
        Install Aplikasi
    </button>

    <script>
        let deferredPrompt;

        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById("installBtn").style.display = "block";
        });

        document.getElementById("installBtn").addEventListener("click", async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            document.getElementById("installBtn").style.display = "none";
        });
    </script>

</body>
</html>
