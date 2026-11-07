</main>
<footer class="site-footer">
  <div class="wrap">
    <p><strong><?= e(APP_NAME) ?></strong> · <?= e(APP_TAGLINE) ?> · Proyek mata kuliah Data Warehouse</p>
    <p>Seluruh angka dan nama pada situs ini adalah data contoh hasil simulasi untuk keperluan tugas, bukan data transaksi nyata.</p>
  </div>
</footer>

<script>
  (function () {
    const modal = document.getElementById('confirmModal');
    const message = document.getElementById('confirmMessage');
    const cancelBtn = document.getElementById('cancelConfirm');
    const confirmBtn = document.getElementById('confirmAction');
    const accountMenu = document.querySelector('.account-menu');
    const accountButton = document.querySelector('.account-button');
    const themeToggle = document.getElementById('themeToggle');
    const mascot = document.querySelector('.mascot');
    const mascotWrap = document.querySelector('.mascot-wrap');
    const mascotScene = document.querySelector('.mascot-scene');

    function triggerPageTransition() {
      document.body.classList.remove('page-transitioning');
      void document.body.offsetWidth;
      document.body.classList.add('page-transitioning');
      window.setTimeout(function () {
        document.body.classList.remove('page-transitioning');
      }, 700);
    }

    function applyTheme(isDark) {
      document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');

      if (themeToggle) {
        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.title = isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap';
      }
    }

    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = savedTheme ? savedTheme === 'dark' : prefersDark;
    applyTheme(initialTheme);

    if (mascot && mascotWrap) {
      let targetX = 0;
      let targetY = 0;
      let x = 0;
      let y = 0;
      let moodTimer = null;

      const moodSequence = ['senang', 'sedih', 'marah', 'ngambek'];
      let moodIndex = 0;

      function setMascotMood(mood) {
        mascot.dataset.mood = mood;
      }

      function setMoodWithTimer(mood, delay) {
        if (moodTimer) {
          clearTimeout(moodTimer);
        }
        setMascotMood(mood);
        if (delay) {
          moodTimer = setTimeout(function () {
            setMascotMood('idle');
          }, delay);
        }
      }

      function animateMascot() {
        x += (targetX - x) * 0.14;
        y += (targetY - y) * 0.14;
        mascotWrap.style.setProperty('--mascot-x', x.toFixed(2) + 'px');
        mascotWrap.style.setProperty('--mascot-y', y.toFixed(2) + 'px');
        requestAnimationFrame(animateMascot);
      }

      requestAnimationFrame(animateMascot);
      setMascotMood('idle');

      const greetingKey = 'passgoMascotGreetingShown';
      const isGreetingShown = sessionStorage.getItem(greetingKey) === '1';

      if (mascotScene && !isGreetingShown) {
        mascotScene.classList.add('is-speaking');
        sessionStorage.setItem(greetingKey, '1');
        window.setTimeout(function () {
          mascotScene.classList.remove('is-speaking');
        }, 2600);
      }

      if (window.__mascotMoodTimer) {
        clearInterval(window.__mascotMoodTimer);
      }
      window.__mascotMoodTimer = setInterval(function () {
        if (document.hidden) {
          return;
        }
        if (mascotScene && mascotScene.classList.contains('is-speaking')) {
          return;
        }
        const isHovered = mascot && mascot.matches(':hover');
        if (document.body.contains(mascot) && !isHovered) {
          setMascotMood('idle');
        }
      }, 6000);

      window.addEventListener('pointermove', function (event) {
        const rect = mascotWrap.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        const dx = event.clientX - cx;
        const dy = event.clientY - cy;

        targetX = Math.max(-18, Math.min(18, dx * 0.18));
        targetY = Math.max(-14, Math.min(14, dy * 0.18));
      });

      mascot.addEventListener('pointerenter', function () {
        setMascotMood('senang');
      });

      mascot.addEventListener('pointermove', function () {
        setMascotMood('senang');
      });

      mascot.addEventListener('pointerleave', function () {
        setMascotMood('idle');
      });

      mascot.addEventListener('pointerdown', function (event) {
        event.preventDefault();
        setMoodWithTimer('marah', 700);
      });

      mascot.addEventListener('pointerup', function () {
        setMascotMood('senang');
      });

      mascot.addEventListener('dblclick', function () {
        setMoodWithTimer('ngambek', 900);
      });
    }

    triggerPageTransition();

    if (themeToggle) {
      themeToggle.addEventListener('click', function () {
        const isDark = document.documentElement.getAttribute('data-theme') !== 'dark';
        applyTheme(isDark);
      });
    }

    document.querySelectorAll('a[href]').forEach(function (link) {
      link.addEventListener('click', function (event) {
        const isLocalLink = link.href && link.href.indexOf(window.location.origin) === 0;
        const modifierPressed = event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;

        if (isLocalLink && !modifierPressed) {
          triggerPageTransition();
        }
      });
    });

    let targetUrl = '';
    let pendingForm = null;

    if (accountButton && accountMenu) {
      accountButton.addEventListener('click', function () {
        const open = accountMenu.classList.toggle('open');
        accountButton.setAttribute('aria-expanded', String(open));
      });

      document.addEventListener('click', function (event) {
        if (!accountMenu.contains(event.target)) {
          accountMenu.classList.remove('open');
          accountButton.setAttribute('aria-expanded', 'false');
        }
      });
    }

    document.querySelectorAll('.dropdown-link[data-confirm]').forEach(function (link) {
      link.addEventListener('click', function (event) {
        event.preventDefault();
        const form = link.closest('form');
        pendingForm = form;
        targetUrl = form ? form.action : '';
        message.textContent = link.dataset.confirm || 'Apakah Anda yakin?';
        modal.hidden = false;
        accountMenu && accountMenu.classList.remove('open');
      });
    });

    cancelBtn.addEventListener('click', function () {
      modal.hidden = true;
      targetUrl = '';
      pendingForm = null;
    });

    confirmBtn.addEventListener('click', function () {
      if (pendingForm) {
        pendingForm.submit();
        return;
      }

      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });
  })();
</script>
</body>
</html>
