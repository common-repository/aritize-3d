<?php
const GENERAL_TAB = 'general';
const REST_API_TAB = 'rest-api';
const CUSTOM_CSS_TAB = 'custom-css';

$tab = isset($_GET['tab']) ? $_GET['tab'] : GENERAL_TAB;
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <nav class="nav-tab-wrapper">
        <a href="?page=aritize-3d-settings&tab=general" class="nav-tab <?php if($tab===GENERAL_TAB):?>nav-tab-active<?php endif; ?>">
            <?php esc_html_e('General', 'aritize-3d')?>
        </a>
        <a href="?page=aritize-3d-settings&tab=rest-api" class="nav-tab <?php if($tab===REST_API_TAB):?>nav-tab-active<?php endif; ?>">
            <?php esc_html_e('REST API', 'aritize-3d')?>
        </a>
        <a href="?page=aritize-3d-settings&tab=custom-css" class="nav-tab <?php if($tab===CUSTOM_CSS_TAB):?>nav-tab-active<?php endif; ?>">
            <?php esc_html_e('Customize CSS', 'aritize-3d')?>
        </a>
    </nav>

    <div class="tab-content">
        <?php switch($tab) :
            case GENERAL_TAB:
                require_once __DIR__ . '/sections/general-tab.php';
                break;
            case REST_API_TAB:
                require_once __DIR__ . '/sections/api-tab.php';
                break;
            case CUSTOM_CSS_TAB:
                require_once __DIR__ . '/sections/custom-css.php';
                break;
            default:
                esc_html_e('Default tab', 'aritize-3d');
                break;
        endswitch; ?>
    </div>
</div>
