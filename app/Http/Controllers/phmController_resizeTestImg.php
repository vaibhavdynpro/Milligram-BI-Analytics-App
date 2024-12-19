<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Phm;
use App\Sections;
use App\SubSections;
use App\Looker;
use App\Client_folder_mapping;
use App\users_folder_access;
use PDF;
use App\Libraries\HTMLtoOpenXML;

//require_once 'bootstrap.php';
class phmController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData= \DB::table('phm')
        ->select('phm.*','client_folder_mapping.folder_name')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
       // ->where(['id' => 'something', 'otherThing' => 'otherThing'])
        ->get();

        return view('phm.index',compact('activeMenu', 'activeSubMenu','phmData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
		$client_secret = $lookerSetting->client_secret;

		//$url = "https://dynpro.cloud.looker.com:443/api/3.1/login?client_id=rgYfZRZcBcVjrjBXzCrn%20&client_secret=7MVjpFvhP52KfJhM7Rx376B9";
		$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		$method = "POST";
		$resp= $this->curlCall($url, $method);
		$responseData = json_decode($resp,true);
		//call to lookers folder api
		$query = array('access_token' => $responseData['access_token']);

        $method1 = 'GET';
        $folderChildUrl = $api_url . "folders/88/children";
		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);
		$folderChild = array();
		$folderChildArr = array();
		foreach ($childData as $fldr){
			$folderChild['id']= $fldr['id'];
			$folderChild['name']= $fldr['name'];
			//$folderChild['embed_url']= $fldr['embed_url'];
			$folderChildArr[] = $folderChild;
		}


        return view('phm.create',compact('activeMenu', 'activeSubMenu','folderChildArr'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = Phm::create([
            'name' =>  $request->name,
            'client_id' =>  $request->client_id,
            'start_date' => date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d'),
            'end_date' => date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d'),
         ])->id;

         $cnt = $request->countsArray;
         $cnt_arr = json_decode($cnt);
        // print_r($cnt_arr);
        foreach($cnt_arr as $counter){
             $var_section_heading = "section_heading_".$counter->section_id."_1";
             $var_section_text = "section_text_".$counter->section_id."_1";
             $section_id = Sections::create([
                'section_title' =>  $request->$var_section_heading,
                'section_text' =>  $request->$var_section_text,
                'section_no' =>$counter->section_id,
                'phm_id' => $id,
            ])->id;
            //echo $counter->subSectCount;
            for($i=1; $i<=$counter->subSectCount; $i++){
                $var_sub_section_heading = "sub_section_heading_".$counter->section_id."_".$i;
                $var_sub_section_text = "sub_section_text_".$counter->section_id."_".$i;
                $var_chart = "chart_".$counter->section_id."_".$i;
                $var_chart_id = "chart_id_".$counter->section_id."_".$i;
                $var_chart_name = "chart_name_".$counter->section_id."_".$i;
                
                $sub_section_id = SubSections::insert([
                    'sub_section_title' =>  $request->$var_sub_section_heading,
                    'sub_section_text' =>  $request->$var_sub_section_text,
                    'sub_section_no' =>$i,
                    'phm_id' => $id,
                    'section_id' => $section_id,
                    'look_img_url' => $request->$var_chart,
                    'look_id' => $request->$var_chart_id,
                    'look_name' => $request->$var_chart_name,
                ]
                
                );
            }
         }
         

        return redirect('phm')->with('success', 'PHM has been successfully created!!');
    }

    public function downloadPDF1($id)
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
       // $phmData = Phm::find($id);
        $phmData= \DB::table('phm')
        ->select('phm.*','client_folder_mapping.folder_name')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
        ->where('phm.id' , $id)
        ->first();


        //print_r($phmData);exit;
        $data = [
            'phmData' => $phmData,
            'activeMenu' => $activeMenu,
            'id' => $id,
            'activeSubMenu' => $activeSubMenu,
        ];
        $pdf = PDF::loadView('phm.edit1', $data);
        //$pdf = PDF::loadHTML('<p>This gets tested!</p>');
        return $pdf->stream($phmData->name.'.pdf');
       
    }

    public function downloadPDF($id)
    {

        $phmData= \DB::table('phm')
                    ->select('phm.*','client_folder_mapping.folder_name')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where('phm.id' , $id)
                    ->first();

        

        $phmSectionData= \DB::table('sections')
                    ->where('phm_id' , $id)
                    ->get();

        // foreach($phmSectionData as $phmSection){
        //         //echo $phmSection->section_title;
        //         $phmSubSectionData= \DB::table('sub_sections')
        //             ->where('section_id' ,$phmSection->id)
        //             ->get()->toArray();
        //         $phmSectionData1['section'] = $phmSection;
        //         $phmSectionData1['subSection'] = $phmSubSectionData;
        //         $phmSectionData2[]=$phmSectionData1;
        // }

        // echo "<PRE>";
        // print_r($phmSectionData2);
        // exit;

        

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // Add Password Protection For editing 
        // $documentProtection = $phpWord->getSettings()->getDocumentProtection();
        // $documentProtection->setEditing(PhpOffice\PhpWord\SimpleType\DocProtect::READ_ONLY);
        // $documentProtection->setPassword('123456');
       // include_once 'Sample_Header.php';

        // New Word document
       // echo date('H:i:s'), ' Create new PhpWord object', EOL;
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->getSettings()->setUpdateFields(true);

        //cover page start
        $imgUrl = $_SERVER['DOCUMENT_ROOT']."/dist/img/kairos_logo_doc.png";
        $coverPage = $phpWord->addSection();
            $coverPage->addImage(
                $imgUrl,
               //$phmData->chart1,
                array(
                    'width'         => 450,
                    'height'        => 150,
                    'marginTop'     => -1,
                    'marginLeft'    => -1,
                    'wrappingStyle' => 'behind'
                )
            ); 

        $startDate1 = date_create($phmData->start_date);
        $startDate = date_format($startDate1,"F d, Y");
        $endDate1 = date_create($phmData->end_date);
        $endDate = date_format($endDate1,"F d, Y");
        $startyr = date_format($startDate1,"Y");
        $endyr = date_format($endDate1,"Y");
        $coverPage->addText('Population Health Management Report',  array('size' => 26,'Bold' => true, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addTextBreak(1);
        $coverPage->addText('Time Period:',  array('size' => 18,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText('Medical Data: '.$startDate.' to '.$endDate,  array('size' => 14,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText('Pharmacy Data: '.$startDate.' to '.$endDate,  array('size' => 14,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText($startyr.'-'.$endyr.': '.$startDate.' to '.$endDate,  array('size' => 10,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addTextBreak(2);
        $coverPage->addText('Prepared for:',  array('size' => 18,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        
        $coverPage->addText($phmData->name,  array('size' => 26,'Bold' => true, 'color' => 'Black'),array('align'=>'center'));

        //cover page end

        $section = $phpWord->addSection();
        
        // Define styles
        $fontStyle12 = array('spaceAfter' => 60, 'size' => 12);
        $fontStyle10 = array('size' => 10);
        $phpWord->addTitleStyle(null, array('size' => 22, 'bold' => true));
        $phpWord->addTitleStyle(1, array('size' => 20, 'color' => '333333', 'bold' => true));
        $phpWord->addTitleStyle(2, array('size' => 16, 'color' => '333333'));
        $phpWord->addTitleStyle(3, array('size' => 14, 'italic' => true));
        $phpWord->addTitleStyle(4, array('size' => 12));
        
        // Table of contents Start
            $section->addTitle('Table of contents', 0);
            $section->addTextBreak(2);
         
            // Add TOC #1
            $toc = $section->addTOC($fontStyle12);
            $section->addTextBreak(2);
        // Table of contents end
       // $section->addPageBreak();
        
        foreach($phmSectionData as $phmSection){
            //echo $phmSection->section_title;
            $phmSubSectionData= \DB::table('sub_sections')
                ->where('section_id' ,$phmSection->id)
                ->get()->toArray();
            
            
            
            if($phmSection->section_title !='' && $phmSection->section_title != null){
                $section->addPageBreak();
                $section->addTitle($phmSection->section_title, 1);
            }
           if($phmSection->section_text !='' && $phmSection->section_text != null){
                // $section_text_bold = str_replace("<strong>", "<b>", $phmSection->section_text);
                // $section_text_bold1 = str_replace("</strong>", "</b>", $section_text_bold);
                // $section_text_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($section_text_bold1);
                // $section->addText($section_text_toOpenXML);
                \PhpOffice\PhpWord\Shared\Html::addHtml($section,  $phmSection->section_text, false, false);
                $section->addTextBreak(1);
           }
            foreach($phmSubSectionData as $phmSubSection){
               // $sub_section_title_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($phmSubSection->sub_section_title);
               if($phmSubSection->sub_section_title !='' && $phmSubSection->sub_section_title != null){
                    $section->addPageBreak();
                    $section->addTitle($phmSubSection->sub_section_title, 2);
               }
                if($phmSubSection->sub_section_text !='' && $phmSubSection->sub_section_text != null){
                    // $sub_section_text_bold = str_replace("<strong>", "<b>", $phmSubSection->sub_section_text);
                    // $sub_section_text_bold1 = str_replace("</strong>", "</b>", $sub_section_text_bold);
                    // $sub_section_text_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($sub_section_text_bold1);
                    // $section->addText($sub_section_text_toOpenXML);
                    \PhpOffice\PhpWord\Shared\Html::addHtml($section,  $phmSubSection->sub_section_text, false, false);
                }
                $section->addTextBreak(1);
                if($phmSubSection->look_img_url !='' && $phmSubSection->look_img_url != null){
                    /**********************************************************************************/ 
                        $lookID=$phmSubSection->look_id;

                        //$folder_id= $_POST['folder_id'];
                        $url = "https://dynpro.cloud.looker.com:443/api/3.1/login?client_id=rgYfZRZcBcVjrjBXzCrn%20&client_secret=7MVjpFvhP52KfJhM7Rx376B9";
                        //$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
                        $method = "POST";
                        $resp= $this->curlCall($url, $method);
                        $responseData = json_decode($resp,true);

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://dynpro.cloud.looker.com:443/api/3.1/looks/'.$lookID.'/run/png',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => array('access_token' => $responseData['access_token']),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);

                        $section->addImage(
                            $response,
                            array(
                                'width'         => 500,
                                'height'        => 375,
                                'marginTop'     => -1,
                                'marginLeft'    => -1,
                                
                            )
                        ); 
                        /****************************************/




                    // $section->addImage(
                    //     $phmSubSection->look_img_url,
                    //     array(
                    //         'width'         => 400,
                    //         'height'        => 400,
                    //         'marginTop'     => -1,
                    //         'marginLeft'    => -1,
                    //         'wrappingStyle' => 'behind'
                    //     )
                    // ); 
                }

            }

        }
       
        $footer = $section->addFooter();
        $footer->addPreserveText('Page {PAGE} of {NUMPAGES}.', null, array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
        
        
        $phpWord->getSettings()->setUpdateFields(true);  
       


        

        $file = $phmData->name.'.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        // Saving the document as OOXML file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("php://output");

        //$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        //$objWriter->save($phmData->name.'.docx');

        //$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($phmData->name.'.docx');
        //$templateProcessor->saveAs('PBHSample_07_TemplateCloneRow.docx');

        //echo '<a href="http://127.0.0.1:8000/PBHSample_07_TemplateCloneRow.docx" >testrr</a>';
        //$file=fopen($phmData->name.".docx","r");
   }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData = Phm::find($id);

        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
		$client_secret = $lookerSetting->client_secret;

		//$url = "https://dynpro.cloud.looker.com:443/api/3.1/login?client_id=rgYfZRZcBcVjrjBXzCrn%20&client_secret=7MVjpFvhP52KfJhM7Rx376B9";
		$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		$method = "POST";
		$resp= $this->curlCall($url, $method);
		$responseData = json_decode($resp,true);
		//call to lookers folder api
		$query = array('access_token' => $responseData['access_token']);

        $method1 = 'GET';
        $folderChildUrl = $api_url . "folders/88/children";
		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);
		$folderChild = array();
		$folderChildArr = array();
		foreach ($childData as $fldr){
			$folderChild['id']= $fldr['id'];
			$folderChild['name']= $fldr['name'];
			//$folderChild['embed_url']= $fldr['embed_url'];
			$folderChildArr[] = $folderChild;
        }
        
        $phmData= \DB::table('phm')
                    ->select('phm.*','client_folder_mapping.folder_name')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where('phm.id' , $id)
                    ->first();

        $phmSectionData= \DB::table('sections')
                    ->where('phm_id' , $id)
                    ->get();
        $phmSectionDataCount = count($phmSectionData)+1;

        foreach($phmSectionData as $phmSection){
                //echo $phmSection->section_title;
                $phmSubSectionData= \DB::table('sub_sections')
                    ->where('section_id' ,$phmSection->id)
                    ->get()->toArray();
              // echo $subsectionCount= count($phmSubSectionData);

               $phmSectioncount['section_id'] = $phmSection->section_no;
               $phmSectioncount['subSectCount'] = count($phmSubSectionData);
               $phmSectionCountArr[]=$phmSectioncount;
               

                $phmSectionData1['section'] = $phmSection;
                $phmSectionData1['subSection'] = $phmSubSectionData;
                $phmSectionDataArr[]=$phmSectionData1;
        }
        $phmSectionCountArr = json_encode($phmSectionCountArr);
       //exit;

       // $folderAccess = users_folder_access::select('folder_id')->where("user_id","=",$id)->get()->toArray();
        return view('phm.edit',compact('activeMenu', 'id','activeSubMenu','phmData','folderChildArr','phmSectionDataArr','phmSectionCountArr','phmSectionDataCount'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData = Phm::find($id);

        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
		$client_secret = $lookerSetting->client_secret;

		//$url = "https://dynpro.cloud.looker.com:443/api/3.1/login?client_id=rgYfZRZcBcVjrjBXzCrn%20&client_secret=7MVjpFvhP52KfJhM7Rx376B9";
		$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		$method = "POST";
		$resp= $this->curlCall($url, $method);
		$responseData = json_decode($resp,true);
		//call to lookers folder api
		$query = array('access_token' => $responseData['access_token']);

        $method1 = 'GET';
        $folderChildUrl = $api_url . "folders/88/children";
		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);
		$folderChild = array();
		$folderChildArr = array();
		foreach ($childData as $fldr){
			$folderChild['id']= $fldr['id'];
			$folderChild['name']= $fldr['name'];
			//$folderChild['embed_url']= $fldr['embed_url'];
			$folderChildArr[] = $folderChild;
        }
        
        $phmData= \DB::table('phm')
                    ->select('phm.*','client_folder_mapping.folder_name')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where('phm.id' , $id)
                    ->first();

        $phmSectionData= \DB::table('sections')
                    ->where('phm_id' , $id)
                    ->get();
        $phmSectionDataCount = count($phmSectionData)+1;

        foreach($phmSectionData as $phmSection){
                //echo $phmSection->section_title;
                $phmSubSectionData= \DB::table('sub_sections')
                    ->where('section_id' ,$phmSection->id)
                    ->get()->toArray();
              // echo $subsectionCount= count($phmSubSectionData);

               $phmSectioncount['section_id'] = $phmSection->section_no;
               $phmSectioncount['subSectCount'] = count($phmSubSectionData);
               $phmSectionCountArr[]=$phmSectioncount;
               

                $phmSectionData1['section'] = $phmSection;
                $phmSectionData1['subSection'] = $phmSubSectionData;
                $phmSectionDataArr[]=$phmSectionData1;
        }
        $phmSectionCountArr = json_encode($phmSectionCountArr);
       //exit;

       // $folderAccess = users_folder_access::select('folder_id')->where("user_id","=",$id)->get()->toArray();
        return view('phm.copy',compact('activeMenu', 'id','activeSubMenu','phmData','folderChildArr','phmSectionDataArr','phmSectionCountArr','phmSectionDataCount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $phm = Phm::find($id);
        $phm->name = $request->name;
        $phm->client_id =  $request->client_id;
        $phm->start_date =date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d');
        $phm->end_date = date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d');
        $phm->save();

        $cnt = $request->countsArray;
        $cnt_arr = json_decode($cnt);
        // print_r($cnt_arr); exit;
        foreach($cnt_arr as $counter){
             $var_section_heading = "section_heading_".$counter->section_id."_1";
             $var_section_text = "section_text_".$counter->section_id."_1";
             $section_id = Sections::updateOrCreate(
                 ['phm_id' => $id,'section_no' =>$counter->section_id,],
                [
                'section_title' =>  $request->$var_section_heading,
                'section_text' =>  $request->$var_section_text,
                'section_no' =>$counter->section_id,
                'phm_id' => $id,
            ])->id;
            //echo $counter->subSectCount;
            for($i=1; $i<=$counter->subSectCount; $i++){
                $var_sub_section_heading = "sub_section_heading_".$counter->section_id."_".$i;
                $var_sub_section_text = "sub_section_text_".$counter->section_id."_".$i;
                $var_chart = "chart_".$counter->section_id."_".$i;
                $var_chart_id = "chart_id_".$counter->section_id."_".$i;
                $var_chart_name = "chart_name_".$counter->section_id."_".$i;
                
                $sub_section_id = SubSections::updateOrCreate(
                    [ 
                        'sub_section_no' =>$i,
                        'phm_id' => $id,
                        'section_id' => $section_id
                    ],
                    [
                        'sub_section_title' =>  $request->$var_sub_section_heading,
                        'sub_section_text' =>  $request->$var_sub_section_text,
                        'look_img_url' => $request->$var_chart,
                        'look_id' => $request->$var_chart_id,
                        'look_name' => $request->$var_chart_name,
                    ]
                
                );
            }
         }




        

        return redirect('phm')->with('success', 'PHM has been successfully created!!');
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveCopy(Request $request, $id)
    {
        $id = Phm::create([
            'name' =>  $request->name,
            'client_id' =>  $request->client_id,
            'start_date' => date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d'),
            'end_date' => date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d'),
         ])->id;

         $cnt = $request->countsArray;
         $cnt_arr = json_decode($cnt);
        // print_r($cnt_arr);
        foreach($cnt_arr as $counter){
             $var_section_heading = "section_heading_".$counter->section_id."_1";
             $var_section_text = "section_text_".$counter->section_id."_1";
             $section_id = Sections::create([
                'section_title' =>  $request->$var_section_heading,
                'section_text' =>  $request->$var_section_text,
                'section_no' =>$counter->section_id,
                'phm_id' => $id,
            ])->id;
            //echo $counter->subSectCount;
            for($i=1; $i<=$counter->subSectCount; $i++){
                $var_sub_section_heading = "sub_section_heading_".$counter->section_id."_".$i;
                $var_sub_section_text = "sub_section_text_".$counter->section_id."_".$i;
                $var_chart = "chart_".$counter->section_id."_".$i;
                $var_chart_id = "chart_id_".$counter->section_id."_".$i;
                $var_chart_name = "chart_name_".$counter->section_id."_".$i;
                
                $sub_section_id = SubSections::insert([
                    'sub_section_title' =>  $request->$var_sub_section_heading,
                    'sub_section_text' =>  $request->$var_sub_section_text,
                    'sub_section_no' =>$i,
                    'phm_id' => $id,
                    'section_id' => $section_id,
                    'look_img_url' => $request->$var_chart,
                    'look_id' => $request->$var_chart_id,
                    'look_name' => $request->$var_chart_name,
                ]
                
                );
            }
         }
        

        return redirect('phm')->with('success', 'PHM has been successfully Copied!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Phm::find($id);
        $user->delete();
        return redirect('phm')->with('success','PHM Report has been deleted successfully!!');
    }

    public function getFolder()
    {
        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        
        $folder_id= $_POST['folder_id'];
       // $url = "https://dynpro.cloud.looker.com:443/api/3.1/login?client_id=rgYfZRZcBcVjrjBXzCrn%20&client_secret=7MVjpFvhP52KfJhM7Rx376B9";
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
       //$url1 = "https://dynpro.cloud.looker.com:443/api/3.1/folders/50";
        $url1 = $api_url . "folders/".$folder_id;
        $method1 = "GET";
        $lookerData= $this->curlCall($url1, $method1,$query);
        $lookerData= json_decode($lookerData, true);
        //echo "<pre>";
        //print_r($lookerData);
        $lookerDashboards = array();
        $lookerDashboardsArr = array();
        foreach ($lookerData['dashboards'] as $dash){
            $lookerDashboards['id']= $dash['id'];
            $lookerDashboards['name']= $dash['title'];
            $lookerDashboardsArr[] = $lookerDashboards;
        }
        $lookerLooks = array();
        $lookerLooksArr = array();
        foreach ($lookerData['looks'] as $look){
            $lookerLooks['id']= $look['id'];
            $lookerLooks['name']= $look['title'];
            $lookerLooks['embed_url']= $look['embed_url'];
            $lookerLooks['image_embed_url']= $look['image_embed_url'];
            $lookerLooksArr[] = $lookerLooks;
        }
            //print_r($lookerLooksArr);
            //print_r($lookerDashboardsArr);
            
            
        $structure = array (
        
        array (
            'name' => 'Looks',
            'open' => false,
            //'type'=> 'Tree.FOLDER',
            'selected' => true,
            'children' => $lookerLooksArr,
            
        ),
        
        array (
            'name' => 'Dashboards',
            'open' => false,
            //'type'=> 'Tree.FOLDER',
            'selected' => true,
            'children' => $lookerDashboardsArr,
            
        ),
        
        // array (
        //     'name' => 'folder 2 (asynced)',
        //     //'type'=> 'Tree.FOLDER',
        //     'asynced' => true,
        // ),
        );

        //print_r(json_decode($structure,true));		
                
                
                
            //echo json_encode(array("looks"=>$lookerLooksArr,"dashboard"=>$lookerDashboardsArr));
            echo json_encode($structure);
    }

    function curlCall($url, $method, $query=null){
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
			// 	"Content-Type: application/x-www-form-urlencoded"
			// ),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		//echo $responseData['access_token'];
		return $response;
	}

    public function removeSection(Request $request)
    {

       $sectionId = $request->sectionId;
       $phm_id = $request->phm_id;
       \DB::table('sections')->where('phm_id', $phm_id)->where('section_no', $sectionId)->delete();
        echo json_encode("successfull");
       
    }

    public function removeSubSection(Request $request)
    {

       $sectionId = $request->sectionId;
       $subSectionId = $request->subSectionId;
       $phm_id = $request->phm_id;
       echo " sectID:".$sectionId;
       echo " subSectionId:".$subSectionId;
       echo " phm_id:".$phm_id;
       
       \DB::table('sub_sections')->where('phm_id', $phm_id)->where('sub_section_no', $subSectionId)->where('section_id', $sectionId)->delete();
        echo json_encode("successfull");
       
    }

}
