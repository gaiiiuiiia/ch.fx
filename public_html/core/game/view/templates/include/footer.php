
        <footer class="footer">
            <div class="container">
                <div class="footer__inner">
                <h1>footer</h1>
                </div>
            </div>
        </footer>

        <?php if (isset($_SESSION['name'])): ?>
            <script>
                const PLAYER_NAME = "<?= $_SESSION['name']?>";
            </script>
        <?php endif; ?>

        <?php $this->getScripts()?>

    </body>
</html>
