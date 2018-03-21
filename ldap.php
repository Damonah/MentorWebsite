<?php
	// basic sequence with LDAP is connect, bind, search, interpret search
	// result, close connection
	function ldap_find_user_by_email($email){
		//Set to false in production
		$debug = true;
		
		$host = "ldaps://ldap.mtsu.edu";
		$port = 636;
		
		if ($debug) {
			//putenv('LDAPTLS_REQCERT=never');
			ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
			error_reporting(E_ALL);
			ini_set('display_errors', true); 
		}
		$ds = ldap_connect($host, $port);  // must be a valid LDAP server!
		//echo "connect result is " . $ds . "<br />";
		if ($ds) { 
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			/* 2 second timeout */
			ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 2); 
			
		
			$r=ldap_bind($ds);     // this is an "anonymous" bind, typically read-only access
	
			// Search email entry
			$sr=ldap_search($ds, "o=Middle Tn State University,st=Tennessee,c=US", "mail=$email");  
			$info = ldap_get_entries($ds, $sr);
		
			//Close connection
			ldap_close($ds);

		} else {
			//Unable to connect to LDAP server
		}
		if (isset ($info)){
			if ($info["count"]  == 1){
				return $info[0];
			}
		}
		return false;
	}
?>