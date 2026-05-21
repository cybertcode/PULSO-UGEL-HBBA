/**
 * Profile Account Settings
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Update/reset user image
  const accountUserImage = document.getElementById('uploadedAvatar');
  if (accountUserImage) {
    const fileInput = document.querySelector('.account-file-input');
    const resetFileInput = document.querySelector('.account-image-reset');
    const resetImage = accountUserImage.src;

    if (fileInput) {
      fileInput.onchange = () => {
        if (fileInput.files[0]) {
          accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
        }
      };
    }
    if (resetFileInput) {
      resetFileInput.onclick = () => {
        if (fileInput) fileInput.value = '';
        accountUserImage.src = resetImage;
      };
    }
  }
});
