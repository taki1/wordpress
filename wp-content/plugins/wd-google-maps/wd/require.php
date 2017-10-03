<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	// configuration should be loaded first.
	require_once dirname( __FILE__ ) . '/config.php';
    
    // load other files
    require_once WD_DIR . '/wd.php';
    // notices
    require_once WD_DIR_INCLUDES . '/api.php';
    require_once WD_DIR_INCLUDES . '/notices.php';
    

