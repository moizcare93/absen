document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-role="mobile-toast-close"]').forEach(function (button) {
        button.addEventListener('click', function () {
            var target = document.getElementById(button.getAttribute('data-target'));
            if (target) {
                target.remove();
            }
        });
    });
});
