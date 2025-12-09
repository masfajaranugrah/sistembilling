(function(){const u=`
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
  </div>`,a=document.querySelector("#dropzone-basic");if(a){Dropzone.autoDiscover=!1;const s=new Dropzone(a,{url:"#",uploadMultiple:!1,addRemoveLinks:!0,maxFiles:1,maxFilesize:50,acceptedFiles:".xlsx,.xls",previewTemplate:u});s.on("addedfile",t=>{console.log("File siap:",t.name)}),document.querySelector("#submit-pelanggan").addEventListener("click",async t=>{t.preventDefault();const n=s.getAcceptedFiles()[0];if(!n){alert("Silakan pilih file Excel terlebih dahulu!");return}const r=a.getAttribute("action"),d=document.querySelector('input[name="_token"]'),c=d?d.value:"";console.log("Mengirim ke:",r);const o=new FormData;o.append("_token",c),o.append("file",n),console.log(r);try{const e=await fetch(r,{method:"POST",body:o,headers:{"X-CSRF-TOKEN":c},credentials:"same-origin"}),i=await e.text();console.log("Response raw:",i);let l;try{l=JSON.parse(i)}catch{l={message:i}}if(e.ok)window.toastr?(toastr.options={closeButton:!0,progressBar:!0,positionClass:"toast-bottom-right",timeOut:"5000"},toastr.success("Data Pelanggan berhasil diimport!","Berhasil 🚀")):alert("✅ File berhasil diimport!"),s.removeAllFiles();else{const p=l.message||"Terjadi kesalahan di server.";window.toastr?(toastr.options={closeButton:!0,progressBar:!0,positionClass:"toast-bottom-right",timeOut:"5000"},toastr.error(p,"Gagal ❌")):alert("⚠️ Gagal import: "+p)}}catch(e){console.error("❌ Fetch error:",e),window.toastr?(toastr.options={closeButton:!0,progressBar:!0,positionClass:"toast-bottom-right",timeOut:"5000"},toastr.error("Terjadi kesalahan saat upload file!","Error")):alert("❌ Terjadi kesalahan saat upload file!")}})}})();
