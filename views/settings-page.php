<div class="wrap">
    <h2><?php echo get_admin_page_title() ?></h2>

    <form action="options.php" method="POST">
        <?php
            settings_fields( 'wcd_settings' );
            do_settings_sections( 'wcd_settings' );
            submit_button();
        ?>
    </form>
</div>
