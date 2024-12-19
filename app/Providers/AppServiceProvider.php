<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Looker;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if(isset(auth()->user()->id)){
            $user_id = auth()->user()->id;
            $role = auth()->user()->role;

            // if($role == 1)
            // {
            //     $lookerSetting = \DB::table('client_folder_mapping')
            //     ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
            //     ->distinct()
            //     ->where(['client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.type' => 'Client','client_folder_mapping.is_active' => 1])
            //     ->get();
            // }
            // else
            // {
                $lookerSetting = \DB::table('client_folder_mapping')
                ->select('client_folder_mapping.id','client_folder_mapping.folder_id','client_folder_mapping.folder_name','client_folder_mapping.logo')
                ->join('users_folder_access','client_folder_mapping.id','=','users_folder_access.folder_primary_id')
                ->distinct()
                ->where(['users_folder_access.user_id' => $user_id,'client_folder_mapping.entity_id' => env('env_entity_id'),'client_folder_mapping.is_active' => 1])
                ->orderBy('client_folder_mapping.folder_name')
                ->get();
            // }
            
            
            $userAccess = \DB::table('grp_role_usr_mapping')
            ->select('*')
            ->where(['grp_role_usr_mapping.user_id' => $user_id])
            ->get();
            $a=[];
            if(empty($userAccess[0]))
            {
                $userAccess = \DB::table('roles')
                ->select('*')
                ->where(['roles.role_id' => $role])
                ->get();
            }
            
          
           // print_r($userAccess);
           // exit();
            // $clientCount = \DB::table('users_folder_access')
            //            ->select(\DB::raw('count(*) as cnt'))           
            //            ->groupBy('user_id')
            //            ->where(['users_folder_access.user_id' => $user_id])
            //            ->get();
            // if($clientCount[0]->cnt == 1)
            // {
            //     if(!empty($lookerSetting[0]->logo) && $lookerSetting[0]->logo != Null)
            //         {
            //         $s3 = \Storage::disk('s3');
            //         $client = $s3->getDriver()->getAdapter()->getClient();
            //         $expiry = "+360 minutes";

            //         $command = $client->getCommand('GetObject', [
            //           'Bucket' => 'kairos-app-storage', // bucket name
            //           'Key'    => $lookerSetting[0]->logo
            //         ]);

            //         $request = $client->createPresignedRequest($command, $expiry);
            //         $logopath =  (string) $request->getUri(); // it will return signed URL
            //         }
            //     $logo = $logopath;
            // }
            // else
            // {
                if(env('env_entity_id') == 1){
                    $logo = "dist/img/kairos_resize_logo.png";
                }
                if(env('env_entity_id') == 3){
                    $logo = "dist/img/mg.png";
                }     
                else
                {
                    $logo = "dist/img/kairos_resize_logo.png";                    
                }
            // }
                $profile_pic = "";
                if(!empty(auth()->user()->profile_pic) && auth()->user()->profile_pic != Null)
                {
                $s3 = \Storage::disk('s3');
                $client = $s3->getDriver()->getAdapter()->getClient();
                $expiry = "+1 minutes";

                $command = $client->getCommand('GetObject', [
                  'Bucket' => 'kairos-app-storage', // bucket name
                  'Key'    => auth()->user()->profile_pic
                ]);

                $request = $client->createPresignedRequest($command, $expiry);
                $profile_pic =  (string) $request->getUri(); // it will return signed URL
                }

            $userDtl = \DB::table('users')
            ->select('groups.group_name','roles.role')
            ->join('groups','users.user_group_id','=','groups.group_id')
            ->join('roles','users.role','=','roles.role_id')
            ->where(['users.id' => $user_id])
            ->get();
       
            //$lookerSetting = Looker::find('1');
            $view->headerData = $lookerSetting;
            $view->accessData = $userAccess;
            $view->logo = $logo;
            $view->profile_pic = $profile_pic;
            $view->user_details = $userDtl;
            // $view->cnt = $clientCount[0]->cnt;
            }
        });
    }
}
