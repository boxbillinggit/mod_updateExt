<?php
/**
 * Example module API
 * This api can be access only for admins
 */
class Box_Mod_UpdateExt_Api_Admin extends Api_Abstract
{

	public function deleteFiles($path, $match, $delSubdirFiles = false){
		
		if (!file_exists($path)) {

		} else {
		static $deleted = 0;
		$dirs = glob($path."*",GLOB_NOSORT);		// GLOB_NOSORT to make it quicker
		$files = glob($path.$match, GLOB_NOSORT);
	
		foreach ($files as $file){
			if(is_file($file)){
				unlink($file);
				$deleted++;
			}
		}
		if ($delSubdirFiles) {
			foreach ($dirs as $dir){
				if (is_dir($dir)){
					$dir = basename($dir) . "/";
					Box_Mod_UpdateExt_Api_Admin::deleteFiles($path.$dir,$match);
				}
			}
		}
		rmdir($path);
		return $deleted;
		}
	}

	public function update() {
		set_time_limit(0);
 
 		$get = $_POST;
 
		//File to save the contents to
		$fp = fopen ('bb-uploads/'.$get['filename'].'.zip', 'w+');
		 //https://codeload.github.com/zaba12/mod_autoticket/legacy.zip/stable
		$url = str_replace("https://","https://codeload.",$get['url']).'/legacy.zip/master';
		 
		//Here is the file we are downloading, replace spaces with %20
		$ch = curl_init(str_replace(" ","%20",$url));
		 
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		 
		//give curl the file pointer so that it can write to it
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		 
		$data = curl_exec($ch);//get curl response
		 
		//done
		curl_close($ch);
		
		$open = zip_open('bb-uploads/'.$get['filename'].'.zip');
		$file = array();
        while($zip = zip_read($open)) {
        $file[]= zip_entry_name($zip);
        }
		zip_close($open);
		
		Box_Mod_UpdateExt_Api_Admin::deleteFiles('bb-modules/'.$get['filename'].'/','*',true);
		
		$zipArchive = new ZipArchive();
		$result = $zipArchive->open('bb-uploads/'.$get['filename'].'.zip');
		
		if ($result === TRUE) {				
			$zipArchive->renameName('bb-modules/'.$get['filename'],'modules/'.$file[0]);
			$zipArchive ->extractTo("bb-modules/");
			$zipArchive ->close();
			rename($_SERVER['DOCUMENT_ROOT'].'/bb-modules/'.$file[0],$_SERVER['DOCUMENT_ROOT'].'/bb-modules/'.$get['filename']);
			unlink('bb-uploads/'.$get['filename'].'.zip');
		} else {
			// Do something on error
		}
			
		return 'OK';
	}
	
	public function install() {
		set_time_limit(0);
 
 		$get = $_POST;
 
		//File to save the contents to
		$fp = fopen ('bb-uploads/'.$get['filename'].'.zip', 'w+');
		 //https://codeload.github.com/zaba12/mod_autoticket/legacy.zip/stable
		$url = str_replace("https://","https://codeload.",$get['url']).'/legacy.zip/master';
		 
		//Here is the file we are downloading, replace spaces with %20
		$ch = curl_init(str_replace(" ","%20",$url));
		 
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		 
		//give curl the file pointer so that it can write to it
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		 
		$data = curl_exec($ch);//get curl response
		 
		//done
		curl_close($ch);
		
		$open = zip_open('bb-uploads/'.$get['filename'].'.zip');
		$file = array();
        while($zip = zip_read($open)) {
        $file[]= zip_entry_name($zip);
        }
		zip_close($open);
		
		Box_Mod_UpdateExt_Api_Admin::deleteFiles('bb-modules/'.$get['filename'].'/','*',true);
		
		$zipArchive = new ZipArchive();
		$result = $zipArchive->open('bb-uploads/'.$get['filename'].'.zip');
		
		if ($result === TRUE) {				
			$zipArchive->renameName('bb-modules/'.$get['filename'],'modules/'.$file[0]);
			$zipArchive ->extractTo("bb-modules/");
			$zipArchive ->close();
			rename($_SERVER['DOCUMENT_ROOT'].'/bb-modules/'.$file[0],$_SERVER['DOCUMENT_ROOT'].'/bb-modules/'.$get['filename']);
			unlink('bb-uploads/'.$get['filename'].'.zip');
		} else {
			// Do something on error
		}
			
		return 'OK';
	}
	
}