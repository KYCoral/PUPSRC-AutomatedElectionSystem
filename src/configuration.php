<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/Path.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/page-head-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/user.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/session-handler.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/page-router.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/page-secondary-nav.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/db-config.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/db-connector.php');

$user = new User(1, 'admin', 'Doe', 'John', 'Michael', 'Jr.', '12', 'A', 'john.doe@example.com', 'Active', 'Voted');

$org_name = $_SESSION['organization'] ?? '';

if (!isset($org_name)) {
    die;
}

echo "
<style>
    :root{
        --primary-color: var(--{$org_name});
    }
</style>
";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    define("TITLE", "Configuration");
    define("DESCRIPTION", "Change election configuration.");

    $pageHead = new PageHeadUtils(TITLE, DESCRIPTION, true);
    ?>


    <base href="<?php echo $pageHead->getBaseURL(); ?>/">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageHead->getTitle(); ?></title>

    <meta name="google" content="nositelinkssearchbox">

    <meta name="theme-color" content="#4285f4">

    <meta name="twitter:card" content="summary_large_image">

    <meta property="og:title" content="<?php echo $pageHead->getTitle(); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo $pageHead->getUrl(); ?>">
    <meta property="og:image" content="http://example.com/image.jpg">
    <meta property="og:description" content="<?php echo $pageHead->getDescription(); ?>">
    <meta name="description" content="<?php echo $pageHead->getDescription(); ?>">

    <meta name="robots" content="noindex" />

    <!-- Montserrat Font -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="src/styles/font-montserrat.css">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="vendor/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Main Style -->
    <link rel="stylesheet" href="src/styles/core.css">
    <link rel="stylesheet" href="src/styles/style.css" />
    <link rel="stylesheet" href="src/styles/orgs/<?php echo $org_name; ?>.css">
    <link rel="icon" type="image/x-icon" href="src/images/resc/ivote-favicon.png">
    <!-- Page Style -->
    <link rel="stylesheet" href="src/styles/configuration.css">

</head>

<body>

    <?php // include_once FileUtils::normalizeFilePath(Path::COMPONENTS_PATH . '/sidebar.php')
    ?>

    <!-- Modify Sidebar relative paths affected by routing page requests -->
    <?php
    // Capture the output of including the sidebar file
    ob_start();
    include_once FileUtils::normalizeFilePath(Path::COMPONENTS_PATH . '/sidebar.php');
    $sidebar_content = ob_get_clean();

    $temporary_html = '<!DOCTYPE html>
                   <html lang="en">
                   <head>
                       <meta charset="UTF-8">
                       <title>Temporary Sidebar Content</title>
                   </head>
                   <body>' . $sidebar_content . '</body>
                   </html>';


    $img_src_prefix = 'src/'; // Prefix to prepend to img src attributes

    // Use DOMDocument to manipulate the HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($temporary_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    // Find all img elements in the sidebar content
    $images = $dom->getElementsByTagName('img');
    foreach ($images as $img) {
        // Get the current src attribute value
        $current_src = $img->getAttribute('src');

        // Prepend the prefix to the current src attribute value
        $new_src = $img_src_prefix . $current_src;

        // Update the src attribute of the img element
        $img->setAttribute('src', $new_src);
    }

    // Get the updated HTML content
    $updated_sidebar_content = '';
    foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
        $updated_sidebar_content .= $dom->saveHTML($node);
    }


    // Output the modified sidebar content
    // Output the modified sidebar content (extracted from wrapped HTML)
    echo $updated_sidebar_content;

    ?>



    <?php
    global $configuration_pages;
    $configuration_pages = [
        'ballot-form',
        'schedule',
        'election-year',
        'vote-guidelines',
        'positions'
    ];

    global $link_name;
    $link_name = [
        'Ballot Form',
        'Schedule',
        'Election Year',
        'Voting Guidelines',
        'Candidate Positions'
    ];

    // Create an instance of PageRouter with the sub_pages array
    $page_router = new PageRouter($configuration_pages);
    $page_router->handleRequest();

    ?>


    <?php include_once FileUtils::normalizeFilePath(Path::COMPONENTS_PATH . '/footer.php') ?>

    <!-- Vendor Scripts -->
    <script src="vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Main Scripts -->
    <script src="src/scripts/script.js"></script>
    <script src="vendor/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="src/scripts/feather.js"></script>
    <!-- Page Scripts -->
    <script type="module" src="src/scripts/configuration.js" defer></script>
    <?php if (isset($page_scripts)) {
        echo $page_scripts;
    }
    ?>

</body>

</html>