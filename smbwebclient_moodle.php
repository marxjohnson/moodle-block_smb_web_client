<?php



ini_set('display_errors', 'stdout');

$SMBWEBCLIENT_CLASS = 'smbwebclient_moodle';

global $smb_cfg, $CFG, $USER, $site;

require_once('../../config.php');
require_once('../../auth/ldap/auth.php');
include('config_smb_web_client.php'); // config for this block only
include('class_smbwebclient.php');
include('class_smbwebclient_moodle.php');

require_login();

if (!confirm_sesskey()) {
    error("Error - No session key");
    die();
}

if (!$site = get_site()) {
    error("Could not find site-level course");
    die();
}


$swc = new smbwebclient_moodle;
if ($swc->criticalError){
    print_header("$site->shortname: Error", $site->fullname,
     "Nework Home Directory");
    echo ("<h1>Sorry, you cannot view your homedirectory online</h1>");
    echo ("<p>Error Message: $swc->criticalError</p>");
    return;
} else {
    $swc->Run();
}

?>
