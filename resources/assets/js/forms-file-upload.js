'use strict';

(function () {
  const previewTemplate = `
  <div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-primary" role="progressbar"
               aria-valuemin="0" aria-valuemax="100"
               data-dz-uploadprogress></div>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>`;

  const dropzoneBasic = document.querySelector('#dropzone-basic');

  if (dropzoneBasic) {
    // Matikan auto discover
    Dropzone.autoDiscover = false;

    // Inisialisasi Dropzone manual
    const myDropzone = new Dropzone(dropzoneBasic, {
      url: '#', // kita gunakan fetch manual
      uploadMultiple: false,
      addRemoveLinks: true,
      maxFiles: 1,
      maxFilesize: 10, // MB
      acceptedFiles: '.xlsx,.xls',
      previewTemplate: previewTemplate // <- pastikan ada koma
    });

    // Saat file ditambahkan
    myDropzone.on('addedfile', file => {
      console.log('File siap:', file.name);
    });

    // Tombol Upload Manual
    document.querySelector('#submit-all').addEventListener('click', async e => {
      e.preventDefault();

      const file = myDropzone.getAcceptedFiles()[0];
      if (!file) {
        // alert sederhana (bisa ganti toastr jika mau)
        alert('Silakan pilih file Excel terlebih dahulu!');
        return;
      }

      const url = dropzoneBasic.getAttribute('action');
      const tokenInput = document.querySelector('input[name="_token"]');
      const token = tokenInput ? tokenInput.value : '';

      console.log('Mengirim ke:', url);

      const formData = new FormData();
      formData.append('_token', token);
      formData.append('file', file);

      try {
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': token
          },
          credentials: 'same-origin'
        });

        const text = await response.text();
        console.log('Response raw:', text);

        let result;
        try {
          result = JSON.parse(text);
        } catch {
          result = { message: text };
        }

        if (response.ok) {
          // Panggil toastr langsung — jangan pake myDropzone.on('success', ...) di sini
          if (window.toastr) {
            toastr.options = {
              closeButton: true,
              progressBar: true,
              positionClass: 'toast-bottom-right',
              timeOut: '5000'
            };
            toastr.success('Data karyawan berhasil diimport!', 'Berhasil 🚀');
          } else {
            alert('✅ File berhasil diimport!');
          }

          myDropzone.removeAllFiles();

          // window.location.href = 'dashboard/admin/employees';
        } else {
          // tampilkan error (pakai toastr kalau ada)
          const message = result.message || 'Terjadi kesalahan di server.';
          if (window.toastr) {
            toastr.options = {
              closeButton: true,
              progressBar: true,
              positionClass: 'toast-bottom-right',
              timeOut: '5000'
            };
            toastr.error(message, 'Gagal ❌');
          } else {
            alert('⚠️ Gagal import: ' + message);
          }
        }
      } catch (err) {
        console.error('❌ Fetch error:', err);
        if (window.toastr) {
          toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-bottom-right',
            timeOut: '5000'
          };
          toastr.error('Terjadi kesalahan saat upload file!', 'Error');
        } else {
          alert('❌ Terjadi kesalahan saat upload file!');
        }
      }
    });
  }
})();
