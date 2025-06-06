</main>
<footer class="footer">
    <p>SMK Negeri Trucuk - Sistem Informasi Tagihan Sekolah</p>
    <p>© <?php echo date('Y'); ?> All Rights Reserved</p>

    <div class="contact-info">
        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Jl. Raya Trucuk No. 123, Bojonegoro</span>
        </div>
        <div class="contact-item">
            <i class="fas fa-phone"></i>
            <span>(0353) 1234567</span>
        </div>
        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <span>info@smkntrucuk.sch.id</span>
        </div>
    </div>
</footer>
</div>

<script>
    // Script ini akan berlaku untuk semua halaman
    // Animasi tombol
    document.querySelectorAll('.btn-action').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/68430a84f0b7f719116e88f0/1it2u26hh';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->
</body>

</html>