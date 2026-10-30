    </div><!-- #main -->
</main>
<footer class="lg-footer">
    <div class="lg-footer-inner">
        <div class="lg-footer-line">
            <?php echo $domain == $DOMAIN ? $OJ_NAME : ucwords(str_replace('_', ' ', $OJ_NAME))."'s OJ" ?>
            · Powered by <a href="https://github.com/zhblue/hustoj" target="_blank" rel="noreferrer noopener">HUSTOJ</a>
            · Theme <span class="lg-footer-theme">Luogu-style</span>
        </div>
        <?php if ($OJ_BEIAN) { ?>
        <div class="lg-footer-line">
            <img src="image/icp.png" alt="">
            <a href="https://beian.miit.gov.cn/" target="_blank" rel="noreferrer noopener"><?php echo $OJ_BEIAN ?></a>
        </div>
        <?php } ?>
    </div>
</footer>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/semantic.min.js"></script>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/Chart.min.js"></script>
<?php include(dirname(__FILE__)."/js.php");?>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/theme.js"></script>
<?php if (isset($_SESSION[$OJ_NAME.'_user_id'])) { ?>
    <iframe id="sk" src="session.php" height="0" width="0" style="display:none"></iframe>
    <script>
        $(function () {
            window.setTimeout(function () { $('#sk').attr('src', 'session.php'); }, 1200000);
        });
    </script>
<?php } ?>
</body>
</html>
