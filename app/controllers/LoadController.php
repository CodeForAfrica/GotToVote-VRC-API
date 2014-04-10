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
	
	// CSV row limit. Set to 0 for unlimited
	protected $limit = 100;
	
	public function csvToCache()
	{
		$row = 1;
		$csvs = Config::get('app.load_csv.path');
		foreach ($csvs as $csv) {
			$row = 1;
			if (($handle = fopen($csv, "r")) !== FALSE  ) {
				while (($data = fgetcsv($handle, 0, ",")) !== FALSE && $row != $this->limit ) {
					Cache::forever('vr_'.$data[0], $data);
					$row ++;
				}
				fclose($handle);
			}
		}
		
		echo "Cache complete! - ".$row;
		
	}
	
	public function csvToDb() {
		$csv = iconv(file_get_contents('http://storage.googleapis.com/mec/smstest.csv')); 
		
		//ofcourse you have to modify that with proper table and field names
		$query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE voters FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`voter_id`, `center_code`, `surname`, `firstname`, `gender`, `dob`)", $csv );
		
		return DB::connection()->getpdo()->exec($query);
	}
	
	public function testCsvToCache() {
		$i = 0;
		$row = 1;
		$missing = "<br />";
		$csvs = Config::get('app.load_csv.path');
		foreach ($csvs as $csv) {
			$row = 1;
			if (($handle = fopen($csv, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 0, ",")) !== FALSE && $row != $this->limit ) {
					if (Cache::has('vr_'.$data[0]))
					{
						// Exists in cache
					} else {
						$i++;
						$missing = $missing.$data[0].'<br />';
					}
					$row ++;
				}
				fclose($handle);
			}
		}
		
		echo $i.' missing.';
		echo $missing;
	}

}