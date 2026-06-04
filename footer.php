<footer class="footer">
    <span class="logo"><span class="logo-dot"></span> MBO Cinemas</span>
    <p>&copy; <?= date('Y') ?> MBO Cinemas — Alle rechten voorbehouden</p>
    <p>
        <a href="#">Privacy</a> &nbsp;·&nbsp;
        <a href="#">Voorwaarden</a> &nbsp;·&nbsp;
        <a href="#">Contact</a>
    </p>
</footer>

<script src="frontend/js/main.js"></script>
<?php if (isset($huidigePagina) && $huidigePagina === 'login.php'): ?>
<script src="frontend/js/pages/login.js"></script>
<?php endif; ?>
</body>
</html>