</div> <!-- Fecho de .d-flex principal ou .main-content -->
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const sidebar = id => document.getElementById('appSidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar().classList.toggle('show');
            });
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                sidebar().classList.remove('show');
            });
        }
    });
</script>
</body>
</html>
