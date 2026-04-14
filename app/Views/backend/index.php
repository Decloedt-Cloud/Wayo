<?php

if (isset($page_data) && is_array($page_data)) {
    extract($page_data);
}

$session_userdata = session()->get();
$user_type = session()->get('user_type');
if (empty($user_type)) {
    $user_type = session()->get('user_type');
}

// Prefer the backend area indicated by current URL to avoid legacy session drift
// (e.g. teacher routes rendering student views when user_type is stale).
$router = service('router');
$routeControllerRaw = $router->controllerName();
$routeController = is_string($routeControllerRaw) ? $routeControllerRaw : '';
if ($routeController === '' && method_exists($router, 'fetchClass')) {
    $fetchClassRaw = $router->fetchClass();
    $routeController = is_string($fetchClassRaw) ? $fetchClassRaw : '';
}
$routeController = str_replace('/', '\\', $routeController);
if (strpos($routeController, '\\') !== false) {
    $parts = explode('\\', $routeController);
    $routeController = end($parts);
}
$routeController = strtolower(trim($routeController));
$currentPath = trim((string) service('request')->getUri()->getPath(), '/');
$currentSegments = $currentPath === '' ? [] : explode('/', $currentPath);
$firstSegment = strtolower((string) ($currentSegments[0] ?? ''));
$allowedUserTypes = ['admin', 'teacher', 'student', 'superadmin', 'accountant', 'librarian', 'parents'];
if (in_array($routeController, $allowedUserTypes, true)) {
    $user_type = $routeController;
} elseif (in_array($firstSegment, $allowedUserTypes, true)) {
    $user_type = $firstSegment;
}

if (empty($user_type)) {
    $user_type = 'admin';
}

$user_id   = session()->get('user_id');
$logged_in_user_details = $this->user_model->get_user_details($user_id);
if (empty($logged_in_user_details)) {
    $user_name = session()->get('name') ? session()->get('name') : 'Unknown User';
} else {
    $user_name = $logged_in_user_details['name'];
}
$school_id = school_id();

if (!isset($page_name)) {
    $page_name = 'index';
}
if (!isset($folder_name)) {
    $folder_name = 'dashboard';
}

?>
<!DOCTYPE html>
<html>

<head>

    <!-- all the meta tags -->
    <?php include 'metas.php'; ?>

    <!-- all the css files -->
    <?php include 'includes_top.php'; ?>

    <style>
        body[dir="rtl"] {
            font-family: 'Shayan', 'Cairo', 'Tajawal', 'Arial', sans-serif !important;
            font-size: 1.1rem !important;
        }

        body[data-layout="detached"] .content-page {
            margin-left: 0;
            overflow: hidden;
            padding: 0 55px 5px 30px;
            position: relative;
            /* margin-right: -15px;
        width: 100%; */
            padding-bottom: 60px;
            margin-right: 0px;
            width: 108%;
        }

        body[dir="rtl"] .card-body .float-end {
            float: left !important;
            right: auto !important;
            left: 0 !important;
        }

        body[dir="rtl"] .card-body h5 {
            font-size: 1.1rem !important;
        }

        body[dir="ltr"] .title_icon {
            margin-right: 10px;
        }

        body[dir="rtl"] .title_icon {
            margin-left: 10px;
        }
    </style>
</head>


<body class="loading" data-layout="detached" data-layout-config='{"leftSidebarCondensed":false,"darkMode":false, "showRightSidebarOnStart": false}' <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <div class="">
        <div class="wrapper">
            <!-- BEGIN CONTENT -->
            <!-- SIDEBAR -->
            <?php include 'navigation.php'; ?>

            <!-- PAGE CONTAINER-->
            <div class="content-page" id="content-page-calendar">
                <div class="content" style="padding-top: 10px;">
                    <div class="loadings hidden"></div>
                    <!-- BEGIN PlACE PAGE CONTENT HERE -->
                    <?php

                    if (!isset($page_name)) {
                        $page_name = "index.php";
                    } else {
                        $page_name = $page_name . '.php';
                    }

                    $include_path = '';
                    if ($folder_name == 'academy' || $folder_name == 'chat' || $folder_name == 'wall') {
                        $include_path = __DIR__ . '/' . $folder_name . '/' . $page_name;
                    } else {
                        $include_path = __DIR__ . '/' . $user_type . '/' . $folder_name . '/' . $page_name;
                    }
                    
                    if (!file_exists($include_path)) {
                        $fallback_path = __DIR__ . '/admin/' . $folder_name . '/' . $page_name;
                        if (file_exists($fallback_path)) {
                            $include_path = $fallback_path;
                        }
                    }
                    
                    if (file_exists($include_path)) {
                        include $include_path;
                    } else {
                        echo '<div class="alert alert-danger">Page not found: ' . htmlspecialchars($include_path) . '</div>';
                    }
                    ?>
                    <!-- END PLACE PAGE CONTENT HERE -->
                </div>
                <!-- Footer -->
                <?php include 'footer.php'; ?>
            </div>
            <!-- END CONTENT -->
        </div>
    </div>
    <!-- all the js files -->
    <?php include 'includes_bottom.php'; ?>
    <?php include 'notify.php'; ?>
    <?php include 'modal.php'; ?>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const isRTL = document.body.getAttribute('dir') === 'rtl';
    document.querySelectorAll('.mdi-arrow-right, .mdi-arrow-left').forEach(icon => {
        if (isRTL && icon.classList.contains('mdi-arrow-right')) {
            icon.classList.replace('mdi-arrow-right', 'mdi-arrow-left');
        } else if (!isRTL && icon.classList.contains('mdi-arrow-left')) {
            icon.classList.replace('mdi-arrow-left', 'mdi-arrow-right');
        }
    });
});
function adjustBodyAttributes() {
  if (window.matchMedia("(min-width: 591px) and (max-width: 1042px)").matches) {
    document.body.classList.add("sidebar-enable");
    document.body.removeAttribute("data-leftbar-compact-mode");
  } else {
    document.body.classList.remove("sidebar-enable");
    // tu peux remettre l'attribut si besoin :
    // document.body.setAttribute("data-leftbar-compact-mode", "condensed");
  }
}

// Exécuter au chargement
adjustBodyAttributes();

// Exécuter quand on redimensionne
window.addEventListener("resize", adjustBodyAttributes);

</script>
</body>

</html>