LICENCE
========================================
GPL See LICENCE.TXT for details

IMPORTANT CHANGES 2008050100+
========================================
You are now recommended to set the following config variable to false:
$smb_cfg->cfgInlineFiles = false;


LIMITATIONS
========================================
You will need a Linux server with SMBClient caps to use this block
NOTE: This version will only work with Moodle 1.8+

PRE-INSTALL PREPARATION
========================================
Manually test SMBClient from your linux console and make sure you can access your window shares.


INSTALLATION
========================================

1) **** BACKUP EVERYTHING FIRST - data directory, SQL, moodle dir, config files and anything else you can think of! ***

2) Make sure no other administrators are using the system. Otherwise they could click the 'Notifications' button and install the block.

3) Unzip 'smb_web_client' to moodle\blocks\

4) Edit the config file to include smb replacement strings for home dir shares - $smb_cfg->cfgWinShareToSmb - e.g.  the windows share '\\netserv1\guysfolder' might need to be 'studentdomain/netserv1/guysfolder' for smbclient to read it. Therefore you would need a config entry as follows:

$smb_cfg->cfgWinShareToSmb=array(
    '\\netserv1'=>'studentdomain/netserv1'
);

5) Log in to Moodle as an admin From Moodle, select 'Notifications' under the Site Administration block. This should set your db tables up for this block.


SITE USAGE
========================================

1) Add the block to your moodle front page (site)

ADDING SPECIFIC SHARES (NON HOME DIR)
========================================

From version 2008050700 it is possible to add network shares to this block.
A new config variable enables you to do this as follows:

$smb_cfg->cfgWinShares=array(
    'share1'=>array(
        'share'=>'admindomain/server1/staff', // windows share
        'title'=>'Staff Shared Area', // title of share in block
        'courses'=>array(5) // applicable courses (ids)
    ),
    'share2'=>array(
        'share'=>'studentdomain/srv1/studentshared', // windows share
        'title'=>'Student Public Area', // title of share in block
        'courses'=>array(1, 5) // applicable courses (ids)
    )    
)

NOTE: The convention for specifying your share is domain/server/share - NO LEADING OR TRAILING SLASHES!
NOTE: Your share key must be unique (e.g. in the example above your next share key should be share3, etc..)

The courses component of the cfgWinShares array specifies which courses user must be enrolled on to see the share (they can be enrolled on any 1 of the courses in the courses array).

E.g.  'courses'=>array(4,5) means that students enrolled on course id 4 or course id 5 will see this share - they don't have to be enrolled on both.

If you want everyone to be able to see the share then simply make sure course id 1 (site) is within the courses array - e.g. 'courses'=>array(1)

You can make all shares appear on the front page smb web client share by setting the following variable to true:

$smb_cfg->cfgAllSharesSite=true;


*** UBUNTU users please note ***
Some Ubuntu users reported issues getting the block to work with Ubuntu 8.10.
A new config parameter has been added which addresses this issue:- $smb_cfg->cfgSkipNoPwdParam
You must set this parameter to true in the config file if you are using Ubuntu 8.10.
Also note- there are other problems with Ubuntu 8.10 that affect smbclient, the biggest being that out of the box it won't work with shares using domain names, it will only work with IPs. To fix this see the following url:
http://ubuntuforums.org/archive/index.php/t-909020.html