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
		
		$i=1;
		foreach($result as $kly => $val) {
			if(empty($val['download_url'])) { 
				$result[$kly] += array("version_git" => NULL);
			} else {
				$urls = $val['download_url']."/master/manifest.json";
				$repl = str_replace("https://","https://raw.",$urls);
				
				$decode = json_decode($this->__curl($repl));
				
				$results['result'][$val['id']] = array("version_git" => $decode->version);	
			}
		$i++;
		}

		$dates = json_decode($this->__curl($url.'mod_autoticket/commits?per_page=100'));

		return $app->render('mod_updateExt_index', $results);

    }
	
}