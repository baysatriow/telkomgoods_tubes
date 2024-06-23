'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    // Fungsi untuk menangani perubahan gambar
    function handleImageChange(imageElement, fileInput, resetButton) {
      if (imageElement) {
        const resetImage = imageElement.src;
        fileInput.onchange = () => {
          if (fileInput.files && fileInput.files[0]) {
            imageElement.src = window.URL.createObjectURL(fileInput.files[0]);
          }
        };
        resetButton.onclick = () => {
          fileInput.value = '';
          imageElement.src = resetImage;
        };
      }
    }

    // Mengatur untuk user
    const userImage = document.getElementById('uploadeduser');
    const userFileInput = document.querySelector('.user-image-input');
    const userResetButton = document.querySelector('.user-image-reset');
    handleImageChange(userImage, userFileInput, userResetButton);

    // Mengatur untuk logo
    const logoImage = document.getElementById('uploadedLogo');
    const logoFileInput = document.querySelector('.logo-image-input');
    const logoResetButton = document.querySelector('.logo-image-reset');
    handleImageChange(logoImage, logoFileInput, logoResetButton);

    // Mengatur untuk favicon
    const faviconImage = document.getElementById('uploadedFavicon');
    const faviconFileInput = document.querySelector('.favicon-image-input');
    const faviconResetButton = document.querySelector('.favicon-image-reset');
    handleImageChange(faviconImage, faviconFileInput, faviconResetButton);
    
    // Mengatur untuk akun
    const akunImage = document.getElementById('uploadedAkun');
    const akunFileInput = document.querySelector('.akun-file-input');
    const akunResetButton = document.querySelector('.akun-image-reset');
    handleImageChange(akunImage, akunFileInput, akunResetButton);
  })();
});
