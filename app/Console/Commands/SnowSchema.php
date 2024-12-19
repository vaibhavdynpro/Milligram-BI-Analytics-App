<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Looker;
use App\Snowflake_schema;
use App\Libraries\Helpers;

class SnowSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SnowSchema:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->helper = new Helpers;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $Sqlquery = "SHOW SCHEMAS";
        $Schema_nameS = "SCH_KAIROS_ARKANSAS_MUNICIPAL_LEAGUE";
        $schema_name = json_decode($this->helper->SnowFlack_Call($Sqlquery,$Schema_nameS));
        $arr = [];
        foreach($schema_name as $val)
                        {
                             $arr[]['schema_name']  = $val;               
                        }
                        
        Snowflake_schema::truncate();
        Snowflake_schema::insert($arr);
    }
    public function curlCall($url, $method, $query=null){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            // CURLOPT_HTTPHEADER => array(
            //  "Content-Type: application/x-www-form-urlencoded"
            // ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //echo $responseData['access_token'];
        return $response;
    }
}
