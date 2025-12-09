@php
    $user = auth('customer')->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Test Modal</title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<h1>Halo, {{ $user->nama_lengkap ?? $user->name }}</h1>

<script>
$(document).ready(function() {

    const userNomerId = "{{ $user->nomer_id }}";
    let isModalShown = false;

    function checkForNewNotifications() {
        console.log('Polling dijalankan untuk user:', userNomerId);

        // Simulasi response API, nanti ganti dengan $.get('/api/check-pending-notifications/'+userNomerId)
        const response = { has_notification: true }; // force trigger modal

        if (response.has_notification && !isModalShown) {
            console.log('Ada notifikasi, tampilkan modal!');
            isModalShown = true;

            Swal.fire({
                icon: 'info',
                title: '?? Hi Halo Bro!',
                html: '<p style="font-size:16px;">Bayar dulu yuk ??</p>',
                confirmButtonText: 'Oke, lihat tagihan',
                confirmButtonColor: '#3b82f6',
                allowOutsideClick: false
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = '/dashboard/customer/tagihan';
                }
                setTimeout(()=> isModalShown = false, 5000);
            });
        }
    }

    // Cek 2 detik setelah page load
    setTimeout(checkForNewNotifications, 2000);
});
</script>

</body>
</html>
