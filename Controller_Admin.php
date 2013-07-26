<?php
/**
 * Example BoxBilling module
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * http://www.boxbilling.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@boxbilling.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2012 BoxBilling (http://www.boxbilling.com)
 * @license   http://www.boxbilling.com/LICENSE.txt
 * @version   $Id$
 */

/**
 * This file connects BoxBilling amin area interface and API
 */
class Box_Mod_UpdateExt_Controller_Admin
{
    /**
     * This method registers menu items in admin area navigation block
     * This navigation is cached in bb-data/cache/{hash}. To see changes please
     * remove the file
     * 
     * @return array
     */
	 
	 public $key_github = '?client_id=6d4f7804e5892ce692c1&client_secret=ca471d8524cc1d015b79c17c20dc63d2fb7b7150';
	 
    public function fetchNavigation()
    {
        return array(
            'group'  =>  array(
                'index'     => 1510,                // menu sort order
                'location'  =>  'updateext',          // menu group identificator for subitems
                'label'     => 'Update Extensions',    // menu group title
                'class'     => 'support',           // used for css styling menu item
            ),
            'subpages'=> array(
                array(
                    'location'  => 'updateext', // place this module in extensions group
                    'label'     => 'Update Extensions',
                    'index'     => 1510,
                    'uri'       => 'updateExt',
                    'class'     => '',
                ),
            ),
        );
    }

    /**
     * Method to install module
     *
     * @return bool
     */
    public function install()
    {
        // execute sql script if needed
        $pdo = Box_Db::getPdo();
        $query="SELECT NOW()";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        throw new Box_Exception("Throw exception to terminate module installation process with a message", array(), 123);
        return true;
    }
    
    /**
     * Method to uninstall module
     * 
     * @return bool
     */
    public function uninstall()
    {
        //throw new Box_Exception("Throw exception to terminate module uninstallation process with a message", array(), 124);
        return true;
    }

    /**
     * Methods maps admin areas urls to corresponding methods
     * Always use your module prefix to avoid conflicts with other modules
     * in future
     *
     *
     * @example $app->get('/example/test',      'get_test', null, get_class($this)); // calls get_test method on this class
     * @example $app->get('/example/:id',        'get_index', array('id'=>'[0-9]+'), get_class($this));
     * @param Box_App $app
     */
    public function register(Box_App &$app)
    {
        $app->get('/updateExt',             'get_index', array(), get_class($this));
    }
	
	public function _config(Box_App $app) {
		$api = $app->getApiAdmin();
		
		$respo_url = 'https://api.github.com/repos/zaba12/';
		
		return $respo_url;
	}
	
	public function __curl($url) {
		  $curl = curl_init();
		  curl_setopt($curl, CURLOPT_URL, $url);
		 // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		  curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
		  $json = curl_exec($curl);
		  curl_close($curl);
		return $json;	
	}
	
    public function get_index(Box_App $app)
    {
        // always call this method to validate if admin is logged in
        $api = $app->getApiAdmin();
		
		$url = $this->_config($app);
		
		$result = $api->extension_get_list($params);
		
		$files = json_decode($this->__curl('https://api.github.com/users/zaba12/repos?client_id=6d4f7804e5892ce692c1&client_secret=ca471d8524cc1d015b79c17c20dc63d2fb7b7150'));
	
		$i=1;
		$a=1;
		$results=array();
		
		unset($files[0]); //wywalam bb-library
		
		foreach($files as $klucz => $valss) {
			
		$pdo = Box_Db::getPdo();
        $query="SELECT `name`,`version` FROM `extension` WHERE `name` ='".str_replace("mod_","",$valss->name)."'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
		$checkIfExits = $stmt->fetchAll();
			
				$urls = $valss->svn_url."/master/manifest.json";
				$repl = str_replace("https://","https://raw.",$urls);
				$decode = json_decode($this->__curl($repl.$this->key_github));

			$results['json'][$valss->name]['name'] = ucfirst(str_replace("mod_","",$valss->name));
			$results['json'][$valss->name]['namefull'] = $valss->name;
			$results['json'][$valss->name]['full_name'] = $valss->full_name;
			$results['json'][$valss->name]['svn_url'] = $valss->svn_url;

			$results['json'][$valss->name]['git_version'] = "".$decode->version."";
			if(empty($checkIfExits[0]['version'])){
			$results['json'][$valss->name]['aktualna_wersja'] = "Nie Zainstalowane";
			} else {
			$results['json'][$valss->name]['aktualna_wersja'] = "".$checkIfExits[0]['version']."";
			}

		    $a++;
		    }

		$dates = json_decode($this->__curl($url.'mod_autoticket/commits?per_page=100'));
		
	
		return $app->render('mod_updateExt_index', $results);

    }
	
}