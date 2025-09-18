document.addEventListener('DOMContentLoaded', function() {
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editProfileForm = document.getElementById('editProfileForm');

    if (editProfileBtn && cancelEditBtn && editProfileForm) {
        editProfileBtn.addEventListener('click', function() {
            editProfileForm.style.display = 'block';
            editProfileBtn.style.display = 'none'; // Hide the edit button when form is shown
        });

        cancelEditBtn.addEventListener('click', function() {
            editProfileForm.style.display = 'none';
            editProfileBtn.style.display = 'block'; // Show the edit button when form is hidden
        });
    }
});
