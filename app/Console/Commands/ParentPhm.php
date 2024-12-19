<?php

namespace App\Console\Commands;
use App\Looker;
use App\Looker_parent_phm;
use Illuminate\Console\Command;


class ParentPhm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ParentPhm:cron';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lookerSetting = Looker::find('1');
        $api_url = $lookerSetting->api_url;
        $client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;

        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);

        $method1 = 'GET';
        $folderChildUrl = $api_url . "folders/2433/children";
        $childData= $this->curlCall($folderChildUrl, $method1,$query);
        $childData= json_decode($childData, true);

        $folderChildUrl1 = $api_url . "folders/88/children";
        $childData1= $this->curlCall($folderChildUrl1, $method1,$query);
        $childData1= json_decode($childData1, true);


        $folderChild = array();
        $folderChildArr = array();
        foreach ($childData as $fldr){
            $folderChild['id']= $fldr['id'];
            $folderChild['name']= $fldr['name'];
            //$folderChild['embed_url']= $fldr['embed_url'];
            $folderChildArr[] = $folderChild;
        }

        foreach ($childData1 as $fldr1){
            $folderChild1['id']= $fldr1['id'];
            $folderChild1['name']= $fldr1['name'];
            //$folderChild['embed_url']= $fldr['embed_url'];
            $folderChildArr[] = $folderChild1;
        }
        Looker_parent_phm::truncate();
        Looker_parent_phm::insert($folderChildArr);
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
