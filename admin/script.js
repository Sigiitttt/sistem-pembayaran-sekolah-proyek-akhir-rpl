document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;

            // Fungsi untuk menerapkan tema
            const applyTheme = (theme) => {
                if (theme === 'light') {
                    body.classList.add('light-mode');
                    themeToggle.checked = true;
                } else {
                    body.classList.remove('light-mode');
                    themeToggle.checked = false;
                }
            };

            // Cek tema yang tersimpan di Local Storage saat halaman dimuat
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                applyTheme(savedTheme);
            }

            // Tambahkan event listener untuk tombol toggle
            themeToggle.addEventListener('change', function () {
                if (this.checked) {
                    localStorage.setItem('theme', 'light');
                    applyTheme('light');
                } else {
                    localStorage.setItem('theme', 'dark');
                    applyTheme('dark');
                }
            });
        });