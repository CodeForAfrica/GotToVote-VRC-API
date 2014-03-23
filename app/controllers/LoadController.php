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
	|	Route::post('/sms', 'SmsController@receiveSMS');
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

}