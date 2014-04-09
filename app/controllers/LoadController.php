<?php

class LoadController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default API Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::post('/load', 'LoadController@csvToCache');
	|
	*/
	
	public function csvToCache()
	{
		if (($handle = fopen(Config::get('app.load_csv.path'), "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				Cache::forever('vr_'.$data[0], $data);
			}
			fclose($handle);
		}
		echo "Cache complete!";
	}
	
	public function testCsvToCache() {
		$i = 0;
		$missing = "<br />";
		if (($handle = fopen(Config::get('app.load_csv.path'), "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				if (Cache::has('vr_'.$data[0]))
				{
					// Exists in cache
				} else {
					$i++;
					$missing = $missing.$data[0].'<br />';
				}
			}
			fclose($handle);
		}
		
		echo $i.' missing.';
		echo $missing;
	}

}