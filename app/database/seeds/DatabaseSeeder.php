<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('VotingCentersTableSeeder');
		
		$this->command->info('Voting centers table seeded!');
	}

}

class VotingCentersTableSeeder extends Seeder {

    public function run()
    {
        DB::table('voting_centers')->delete();
        $i = 0;
        if (($handle = fopen('../voting_centers.csv', "r")) !== FALSE) {
        	while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        		if ($i == 0) {
        			
        		} else {
        			Center::create(array(
        				'center_code' => $data[1],
        				'ward_code' => $data[2],
        				'constituency_code' => $data[3],
        				'county_code' => $data[4],
        				'center_name' => $data[5],
        				'ward_name' => $data[6],
        				'constituency_name' => $data[7],
        				'county_name' => $data[8],
        				'region' => $data[9]
        			));
        		}
        		$i++;
        		
        	}
        	fclose($handle);
        }

        
    }

}