    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Highlight current page in navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === location.pathname.split('/').pop()) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
