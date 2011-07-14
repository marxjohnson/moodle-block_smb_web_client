<?php

/**
 *==============================================================
 * Author: Guy Thomas
 * (c) Moodle Block - Guy Thomas Ossett School 2007, 2008, 2009, 2010
 * (c) Original Smb Web Client - Victor M. Varela 2005
 * Title: Block Smb Web Client
 * Description:
 * This block enables users to login in to their windows shares (corresponding to their
 * ldap home dir field)
 * This block is basically a wrapper for smbwebclient developed by Victor M. Varela
 * see http://smbwebclient.sourceforge.net/
 * NOTE:  * * * Since version 2008121900 there are some custom improvements to the core
 * smbwebclient class - e.g. file extension icons by css
 *==============================================================
 *
 * Change log:
 * Version 2011021900 - now works with windows servers! (see instructions at www.citricity.com)
 *
 * Version 2010090700
 * Bug fix by Jon Witts
 *     Removed all deprecated ereg and split functions with preg equivalents
 * Version 2010042100
 * Bug fix by Roy Gore
 *     Fixed issue with files of 0 length in smbclient version 3.4+
 *    
 * Version 2009100500
 *    Modified logging to log to Moodle log instead of syslog
 * 
 * Version 2009080300 
 * Bug fix by Guy Thomas
 *    On downloading an entire folder as a zip file, some reverse-proxy servers would handle the request incorrectly and a zip file would be downloaded with a file size of 0KB. This has been fixed by forcing the mime type to application/x-forcedownload for a folder being downloaded as a zip and by enabling the max folder size to be set or simply by removing the size header (if cfgMaxFolderZipSizeMB=0)
 *    Added config var max folder zip size for downloading folders as zips - $smb_cfg->cfgMaxFolderZipSizeMB
 *
 * Version 2009060900
 * Enhancement suggested by Jon witts
 *    If smb_cfg->cfgssl not explicitly set then set it according to $CFG->loginhttps
 *
 * Version 2009032700
 * Bug fixes suggested by Jon Witts
 *    Alt tag contents not enclosed in double quotes - breaking validation.
 *    Added brackets as banned characters for IE7 / IE8 popup window titles.
 * Fixed Ubuntu 8.10 smbclient bug - requires config variable $smb_cfg->cfgSkipNoPwdParam set to true
 *
 * Version 2009031700
 * - Added ssl config option as suggested by various members of edugeek.net
 * Thanks to duncane from edugeek for his ideas on this.
 * If $smb_cfg->cfgssl is set to true it will operate over ssl.

 * Version 2009021600
 * - Added fix for attempt to open invalid share - now reports error.
 * - Added configurable home directory field $smb_cfg->cfgHomeDirField - necessary to work with open ldap, etc..
 * - Added config variable $smb_cfg->cfgIEProtectMsOffice - stops IE from opening office documents instead of saving them

 * Version 2009010900
 * - Added file size to conent-length header. You now get a progress bar whilst downloading a file!

 * Version 2008121900
 * - Modified missing config file error to show differently for incorrect config files

 * Version 2008091500
 * - Do not show home directory link for manual accounts

 * Version 2008081200
 * - Now uses pixpath/f/folder.gif for folder icons - better theme compatibility

 * Version 2008080500
 * - Modified to work with ldapcapture auth plugin - smb web client will take the users login
 *   credentials and automatically open network shares.
 * - Added ability to prefix user name (e.g. you want all your user names to get prefixed with
 *   a domain)
 * - Added _RetryParse function to class_smbwebclient.php - basically, smb web client will
 *   retry to parse an smbclient command using the username prefixed with the shares workgroup

 * Version 2008071400
 * - Parent class constructor of smbwebclient_moodle was being called too early - bug reported
 *   and fixed by Harald Winkelmann

 * Version 2008062300
 * - Updated mime types to include office 2007 files.

 * Version 2008061900
 * - Enhanced block to enable files to be forced to download (suggested by Nick Shutters)
 *   (see new config variable forceDownloads)

 * Version 2008053000
 * - Added mod by Eddie Mclafferty to not show block unless logged in

 * Version 2008052900
 * - Fixed IE popup window bug in nice_popup_title funciton

 * Version 2008050700
 * - Can now display course specific shares on front page thanks to new config variable
 *   ($smb_cfg->cfgAllSharesSite=true)

 * Version 2008050100
 * - Access shares by course mod contributed by John Williams Tonbridge Grammar School
 * - File links now single anchors with extensions handled via css. File types now show correct
 *   images for extensions instead of generic file icon.
 * - Included files that were inline files - some users reported problems with inline files.

 * Version 2008012200
 * - Modified version to work with moodle 1.8 (no longer works with 1.7) - changed ldap
 *   functions to use ldap authentication plugin class.
 *
 *
 * Version 2007082900
 *  First version (moodle 1.7)
 *
 *==============================================================
 */

global $smb_cfg, $CFG, $USER, $site, $share;
@include('config_smb_web_client.php'); // config for this block only

class block_smb_web_client extends block_base { 

    var $blockwww;
        var $blockdir;

    function init() {
        global $CFG, $smb_cfg;
        
        // Set title and version
        $this->title = get_string('blockmenutitle', 'block_smb_web_client');
        $this->title = $this->title == "[[blockmenutitle]]" ? "Windows Share Web Client" : $this->title;
        
		// set block dir
		$this->blockdir=$CFG->dirroot.'/blocks/smb_web_client';
        // set block www
        $this->blockwww=$CFG->wwwroot.'/blocks/smb_web_client';
    }

    function get_content() {
        global $CFG, $USER, $COURSE, $smb_cfg;

        // return content if allready set
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';
        
        //No config warning
        if (!isset($smb_cfg)){
            // GT mod 2008/12/19 - report different error if file exists but config variable not set
            if (file_exists($this->blockdir.'/config_smb_web_client.php')){
                $this->content->text=notify('Configuration for this block has errors!','notifyproblem','center',true);
            } else {
                $this->content->text=notify('Configuration for this block has not been completed!','notifyproblem','center',true);
            }
            return;
        }
		
		// Jon Witts Mod 2009060900: if ssl has not been explicitly set, then check Moodle config for loginhttps and set accordingly
		if (!isset($smb_cfg->cfgssl) && (isset($CFG->loginhttps) && $CFG->loginhttps)){
			$smb_cfg->cfgssl=true;
		}		
		

        //EMC mod to only show for logged in users!
        if (!isloggedin() || isguest()) {
            return false;
        }
        //END EMC mod

                
        // GT Mod - 2008091500
        if ($USER->auth!='manual'){
            // get home directory string from language file
            $homedirstr=get_string('homedir', 'block_smb_web_client');
            $homedirstr=$homedirstr=="[[homedir]]" ? "My Home Directory" : $this->title;
            $shareurl=$this->blockwww.'/smbwebclient_moodle.php?sesskey='.$USER->sesskey.'&amp;share=__home__';
			
			// modify shareurl to use ssl if necessary
			if (isset($smb_cfg->cfgssl) && $smb_cfg->cfgssl){
				$shareurl=str_ireplace('http://', 'https://', $shareurl);
			}			
            
            // add home directory link to block content
            $this->content->text = '<a href="#" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($homedirstr).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;"><img src="'.$this->blockwww.'/pix/folder_home.png" alt="Home Folder"/> '.$homedirstr.'</a>';
        }
        // END GT Mod
        
        // new code block for shares - JWI
        if (isset($smb_cfg->cfgWinShares) && !empty($smb_cfg->cfgWinShares)){
            foreach ($smb_cfg->cfgWinShares as $share_key=>$share_arr) {
            
                // If the share is not intended for specific courses then display
                // else make sure current user can access course
    			if (!isset ($share_arr['courses']) || empty($share_arr['courses'])){            
                    // Add share to content
                    $this->addshare($share_key, $share_arr);
                } else {
                
                    // Check user can access course or course is currently open
                    if (isset($smb_cfg->cfgAllSharesSite) && $smb_cfg->cfgAllSharesSite){
                    
                        // If user has access to any of the applicable course then display share
                        foreach ($share_arr['courses'] as $courseid){
                            if (is_int($courseid)){                                                   
                            
                                // Get course
                                $course=get_record('course', 'id', $courseid);
                            
                                $ci=false;
                                if ($course){
                                
                                    // Get context instance
                                    $ci=get_context_instance(CONTEXT_COURSE, $courseid);                                    
                                }
                                
                                // Check capabilities
                                if ($ci && ($COURSE->id==$courseid || has_capability('moodle/course:view', $ci))){
                                
                                    // Add share to content
                                    $this->addshare($share_key, $share_arr);
                                    
                                    // Don't bother checking other applicable courses
                                    break;
                                }
                            }
                        }
                    } else {
                       
                        // Check course open
                        if (in_array($COURSE->id, $share_arr['courses'])){
                        
                            // Add share to content
                            $this->addshare($share_key, $share_arr);
                        }
                    }
                }
            }
        }
        // end new code block for shares - JWI
        return $this->content;
    }
    
    /**
    * Add share to block
    */
    private function addshare($share_key, $share_arr){
        global $CFG, $USER, $smb_cfg;
        $shareurl=$this->blockwww.'/smbwebclient_moodle.php?sesskey='.$USER->sesskey.'&amp;share='.$share_key;
		
		// GT Mod 2009031700 force https protocol if necessary
		if (isset($smb_cfg->cfgssl) && $smb_cfg->cfgssl){
			$shareurl=str_ireplace('http://', 'https://', $shareurl);
		}
		
        $this->content->text.=$this->content->text!='' ? '<br />' : '';
        $this->content->text.='<a href="#" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($share_arr['title']).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;"><img src="'.$CFG->pixpath.'/f/folder.gif" alt="'.$share_arr['title'].'" /> '.$share_arr['title'].'</a>';     
    }
    
    /**
    * Converts a string to a ie popup title friendly string
    * @param required $str - the title you want to make friendly to ie
    */    
    private function nice_popup_title($str){    	
    	$bannedChars=array(" ", "*", "{", "}", "(", ")", "<", ">", "[", "]", "=", "+", "\"", "\\", "/", ",",".",":",";");
    					
    	foreach ($bannedChars as $banned){
    		$str=str_replace($banned,"_", $str);
    	}
    	
    	return ($str);
	
    }    
    
    /**
    * Get ldap entry by user name
    * @param required $username
    */
    private function _getldap_entry($username){
        $ap_ldap=new auth_plugin_ldap();
        $ldapconnection=$ap_ldap->ldap_connect();
        $user_dn = $ap_ldap->ldap_find_userdn($ldapconnection, $username);
        $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', array());	
        if ($sr)  {
            $info=$ap_ldap->ldap_get_entries($ldapconnection, $sr);
        } else {
            return (false);
        }
        return ($info);
    }    
    
}

?>
