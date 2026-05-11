        </div><!-- end #main -->
    </div><!-- end .container -->

    <footer class="footer mt-5 py-4 bg-light text-center">
        <div class="container">
            <div class="text-body-secondary">
                <?php echo $domain==$DOMAIN?$OJ_NAME:ucwords($OJ_NAME)."'s OJ"?> is powered by
                <a target="_blank" rel="noreferrer noopener" href="https://github.com/zhblue/hustoj">HUSTOJ</a>,
                Theme by Bootstrap 5
            </div>
            <?php if ($OJ_BEIAN) { ?>
            <div class="mt-2">
                <img src="image/icp.png" alt="ICP">
                <a href="https://beian.miit.gov.cn/" target="_blank" class="text-decoration-none text-body-secondary"><?php echo $OJ_BEIAN; ?></a>
            </div>
            <?php } ?>
        </div>
    </footer>

    <?php include(dirname(__FILE__)."/js.php");?>

<?php if (isset($_SESSION[$OJ_NAME.'_user_id'])){ ?>
    <iframe id="sk" src="session.php" height="0" width="0" style="display:none;"></iframe>
    <script>
    $(document).ready(function(){
        window.setTimeout("$('#sk').attr('src','session.php');", 1200000);
    });
    </script>
<?php } ?>

</body>
</html>
