<?php

$smb_cfg=new stdClass;

$smb_cfg->cfgWebserverType='windows'; // linux or windows - os on your webserver, not your file server!

###################################################################
# String to prefix username - this is useful if you have your
# shares on a different server to your domain controller and your
# usernames need to be passed as domain/username

# $smb_cfg->cfgUserPrefix='domain/'; // try this commented out first!


###################################################################
# Array of windows share strings to convert to smb strings
# Windows share is the key and value is the corresponding smb format
# i.e. '\\myserver' => 'mydom/myserver' would replace '\\myserver'
# in a share string with mydom/myserver

$smb_cfg->cfgWinShareToSmb=array(
    //'\\10.0.0.50'=>'GUY-Q430/10.0.0.50'
	'\\10.0.0.61'=>'GUY-LAPTOP/10.0.0.61'
);



// new code block for shares - JWI
###################################################################
# Arrays of shares ( other than home directory ) and the courses 
# they can be shown in.  This is to allow administrators to put 
# access to certain shares in certain courses only
# e.g.
# 
#$smb_cfg->cfgWinShares=array(
#    'share1'=>array(
#        'share'=>'admindomain/server1/staff', // windows share
#        'title'=>'Staff Shared Area', // title of share in block
#        'courses'=>array(5) // applicable courses (ids)
#    ),
#    'share2'=>array(
#        'share'=>'studentdomain/srv1/studentshared', // windows share
#        'title'=>'Student Public Area', // title of share in block
#        'courses'=>array(1, 5) // applicable courses (ids)
#    )    
#);
#
# NOTE: The convention for specifying your share is domain/server/share - NO LEADING OR TRAILING SLASHES!
# NOTE: Your share key must be unique (e.g. in the example above your next share key should be share3, etc..)

// populate this as in example above
$smb_cfg->cfgWinShares=array(
    'share1'=>array(
        'share'=>'GUY-LAPTOP/10.0.0.61/shared', // windows share
        'title'=>'Staff Shared Area' // title of share in block
    ),
	'share2'=>array(
        'share'=>'GUY-Q430/10.0.0.50/Documents', // windows share
        'title'=>'Documents' // title of share in block
    )	
);

// if set to true then display all shares on site page providing user has access to any course specific shares
$smb_cfg->cfgAllSharesSite=true;


// end new code block for shares - JWI
###################################################################
# Anonymoys login is disallowed by default.
# If you have public shares in your network (ie you want absolutely everyone (the whole world) to access it) then turn on this flag
# i.e. $smb_cfg->cfgAnonymous = true;

$smb_cfg->cfgAnonymous = false;

###################################################################
# GT MOD 2008-06-19
# Force all files to download instead of trying to open in browser, etc.
# NOTE: This variable used to be $smb_cfg->forceDownloads
$smb_cfg->cfgForceDownloads = true;

###################################################################
# GT MOD 2009-08-03
# Maximum zip size in MBs for downloading folders as zips
$smb_cfg->cfgMaxFolderZipSizeMB=0; // set this to 0 for no size limit!

###################################################################
# Path at web server to store downloaded files. This script will
# check when it need to update the cached file. This path must be
# writable to the user that runs your web server.
# If you set this value to '' cache will be disabled.
# Note: this feature is a security risk.

$smb_cfg->cfgCachePath = false;


###################################################################
# This script try to set language from web browser. If browser
# language is not supported you can set a default language.

$smb_cfg->cfgDefaultLanguage = 'en';
 

###################################################################
# Default charset (as suggested by Norbert Malecki)

$smb_cfg->cfgDefaultCharset = 'ISO-8859-1';


###################################################################
# Default browse server for your network. A browse server is where
# you run smbclient -L subcommand to read available domains and/or
# workgroups. Set to 'localhost' if you are running SAMBA server
# in your web server. Maybe you will need cfgDefaultUser and
# cfgDefaultPassword if no anonymous browsing is allowed.

$smb_cfg->cfgDefaultServer = 'localhost';


###################################################################
# Path to smbclient program.
# i.e. $smb_cfg->cfgSmbClient = '/usr/bin/smbclient';

// windows servers - set as below
 $smb_cfg->cfgSmbClient = 'C:\cygwin\bin\smbclient';

// linux servers - set as below
// $smb_cfg->cfgSmbClient = 'smbclient';


###################################################################
# Authentication method with smbclient
# 'SMB_AUTH_ENV' USER environment variable (more secure)
# 'SMB_AUTH_ARG' smbclient -U param
# 'SMB_AUTH_ARG_WIN' - as above but for running on a windows web server server (not file server!)

$smb_cfg->cfgAuthMode = 'SMB_AUTH_ARG_WIN';


###################################################################
# If you have Apache mod_rewrite installed you can put this
# .htaccess file in same path of smbwebclient.php:
#
#  <IfModule mod_rewrite.c>
#   RewriteEngine on
#   RewriteCond    %{REQUEST_FILENAME}  -d
#   RewriteRule ^(.*/[^\./]*[^/])$ $1/
#   RewriteRule ^(.*)$ smbwebclient.php?path=$1 [QSA,L]
#  </IfModule>
#
# Then you will be able to access to use "pretty" URLs
# i.e: http://server/windows-network/DOMAIN/SERVER/SHARE/PATH
#
# To do this, all you have to set is cfgBaseUrl (*GT Mod- BaseUrl is set automatically by class_smbwebclient_moodle.php) and set
# cfgModRewrite = true
# (i.e. http://server/windows-network/)
#
# Note - Change this if you want to use mod_rewrite

$smb_cfg->cfgModRewrite = false;


###################################################################
# Do not show dot files (like .cshrc)
#

$smb_cfg->cfgHideDotFiles = true;


###################################################################
# Do not show system shared resources (like admin$ or C$)
#

$smb_cfg->cfgHideSystemShares = true;


###################################################################
# Do not show printer resources
#

$smb_cfg->cfgHidePrinterShares = false;


###################################################################
# Log level
# -1 = no messages
#  0 = log actions performed
#  1 = smbclient calls
# >1 = smbclient output
#

$smb_cfg->cfgLogLevel = 2;


###################################################################
# Log facility (User authentication: BasicAuth or FormAuth)
#

$smb_cfg->cfgFacility = LOG_DAEMON;


###################################################################
# User authentication (BasicAuth or FormAuth)
#

$smb_cfg->cfgUserAuth = 'BasicAuth';


###################################################################
# Change PHP session name ('' to use default session name)
#

$smb_cfg->cfgSessionName = 'SMBWebClientID';


###################################################################
# Virus scanner to upload files -- suggested by Bill R <wjries@hotmail.com>
# Only ClamAV is available in this revision, set to false to
# disable virus scanning.
#
# $smb_cfg->cfgAntivirus = 'ClamAV';

$smb_cfg->cfgAntivirus = false;


###################################################################
# Format to upload compressed folders: tar, tgz or zip 
#
# $smb_cfg->cfgArchiver = 'tgz';

$smb_cfg->cfgArchiver = 'zip';

###################################################################
# INTERFACE CLASS
###################################################################

# inline files (included using base64_encode PHP function)

$smb_cfg->cfgInlineFiles = false;

###################################################################
# Enable specific field to be used to retreive home directory
# Default = homeDirectory (standard for AD)
$smb_cfg->cfgHomeDirField = '';

###################################################################
# IE users have a problem when selecting 'open' for office files
# It basically will open the file in 'temporary internet files'
# and the user will possibly then save the file to this folder.
# If you set the following config variable to true then IE users
# will be forced to right hand click and save target for office
# documents.
$smb_cfg->cfgIEProtectMsOffice=true;

###################################################################
# Forces https protocol if set to true
$smb_cfg->cfgssl=false;

###################################################################
# Skips -N command line parameter (don't prompt for password)
# IMPORTANT - if you are using smb client version 3.4 or above then
# you need to set this parameter to true.
# to find out your version, simply type "smbclient --version" in a
# terminal (command prompt)
# Ubuntu 8.10 issues with smbclient:
# out of the box it won't work with shares using domain names,
# it will only work with IPs
# To fix this see the following url:
# http://ubuntuforums.org/archive/index.php/t-909020.html
# OR - use Ubuntu 8.4 instead (works OK out of the box)
#
$smb_cfg->cfgSkipNoPwdParam=false;
?>