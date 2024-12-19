<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response as Download;
use App\Phm;
use App\Sections;
use App\SubSections;
use App\Looker;
use App\Client_folder_mapping;
use App\users_folder_access;
use PDF;
use DB;
use App\Libraries\HTMLtoOpenXML;
use Response;
use App\Libraries\Helpers;

//require_once 'bootstrap.php';
class phmController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('phm');
        $this->helper = new Helpers;
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
        ->select('phm.*','client_folder_mapping.folder_name','report_type.report_type')
        ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
        ->join('report_type','phm.report_type','=','report_type.report_type_id')
       ->where(['phm.is_active' => '1','client_folder_mapping.is_active' => '1','phm.entity_id' => env('env_entity_id')])
       ->orwhere(['phm.is_master' => '1'])
        ->get();

        return view('phm.index',compact('activeMenu', 'activeSubMenu','phmData'));
    }

    /**
     * create PHM report.
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

		$url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
		$method = "POST";
		$resp= $this->curlCall($url, $method);
		$responseData = json_decode($resp,true);
		//call to lookers folder api
		$query = array('access_token' => $responseData['access_token']);

        $method1 = 'GET';

        $folderChildUrl = $api_url . "folders/2433/children";
        $folderChildUrl1 = $api_url . "folders/88/children";

		$childData= $this->curlCall($folderChildUrl, $method1,$query);

		$childData= json_decode($childData, true);

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
        $reporttypes = DB::select("select report_type_id,report_type from report_type where is_active =1");
        

        return view('phm.create',compact('activeMenu', 'activeSubMenu','folderChildArr','reporttypes'));
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
            'entity_id' => env('env_entity_id'),
            'start_date' => date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d'),
            'end_date' => date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d'),
            'pharma_start_date' => date_format(date_create_from_format('m-j-Y', $request->pharma_start_date), 'Y-m-d'),
            'pharma_end_date' => date_format(date_create_from_format('m-j-Y', $request->pharma_end_date), 'Y-m-d'),
            'report_type' => $request->report_type,
            'created_by'        => auth()->user()->id,
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
         

        return redirect('reports')->with('success', 'PHM has been successfully created!!');
    }
    
    public function addSubSectionBelow($id, $section_id,$sub_section_id)
    {
        $flag1 = \DB::statement("UPDATE `sub_sections` SET `sub_section_no`= sub_section_no+1  WHERE `phm_id`='".$id."' and `section_id` = '".$section_id."' and `sub_section_no` > $sub_section_id");
        // $flag1 = \DB::table('sub_sections')
        //     ->where('phm_id', $id)
        //     ->where('section_id', $section_id)
        //     ->where('sub_section_no', $sub_section_id)
        //     ->update(['sub_section_no' => "sub_section_no"+ 1]);
        $sub_section_id++;
        $sub_section_id1 = SubSections::insert([
            'sub_section_title' =>  '',
            'sub_section_text' =>  '',
            'sub_section_no' =>$sub_section_id,
            'phm_id' => $id,
            'section_id' => $section_id,
            'look_img_url' => '',
            'look_id' => '',
            'look_name' => '',
        ]
        
        );
        // $flag = \DB::statement("INSERT INTO `sub_sections`( `section_no`, `phm_id`) VALUES (".$section_id.",'".$id."')")->id;
        // print_r($flag);exit;    
        $id = $this->helper->encrypt_decrypt($id, 'encrypt');
        return redirect('reports/edit/'.$id);
    }

    public function addSectionBelow($id, $section_id)
    {
       
        $flag1 = \DB::statement("UPDATE `sections` SET `section_no`= section_no+1  WHERE `phm_id`='".$id."' and `section_no` > $section_id");
        $section_id++;
        $crr_section_id = Sections::create([
            'section_title' =>  '',
            'section_text' =>  '',
            'section_no' => $section_id,
            'phm_id' => $id,
        ])->id;

        $sub_section_id1 = SubSections::insert([
            'sub_section_title' =>  '',
            'sub_section_text' =>  '',
            'sub_section_no' =>'1',
            'phm_id' => $id,
            'section_id' => $crr_section_id,
            'look_img_url' => '',
            'look_id' => '',
            'look_name' => '',
        ]
        
        );
        // $flag = \DB::statement("INSERT INTO `sub_sections`( `section_no`, `phm_id`) VALUES (".$section_id.",'".$id."')")->id;
        // print_r($flag);exit;    
        $id = $this->helper->encrypt_decrypt($id, 'encrypt');
        return redirect('reports/edit/'.$id);
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

    public function downloadPDF($encrypt_id)
    {
         ini_set('max_execution_time', 900);
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $phmData= \DB::table('phm')
                    ->select('phm.*','client_folder_mapping.folder_name')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where('phm.id' , $id)
                    ->first();

        

        $phmSectionData= \DB::table('sections')
                    ->where('phm_id' , $id)
                    ->orderBy('section_no')
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
         \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->getSettings()->setUpdateFields(true);

        //cover page start
        $imgUrl = $_SERVER['DOCUMENT_ROOT']."/dist/img/M.R.S. Analytics_Kairos_logo.png";
        $coverPage = $phpWord->addSection(["paperSize" => "Letter"]);
            $coverPage->addImage(
                $imgUrl,
               //$phmData->chart1,
                array(
                    'width'         => 350,
                    'height'        => 105,
                    'marginTop'     => -1,
                    'align'=>'center',
                    'wrappingStyle' => 'behind'
                )
            ); 
        $startDate1 = date_create($phmData->start_date);
        $startDate = date_format($startDate1,"F d, Y");
        $endDate1 = date_create($phmData->end_date);
        $endDate = date_format($endDate1,"F d, Y");

        $startDate2 = date_create($phmData->pharma_start_date);
        $startDatePharma = date_format($startDate2,"F d, Y");
        $endDate2 = date_create($phmData->pharma_end_date);
        $endDatePharma = date_format($endDate2,"F d, Y");

        $startyr = date_format($startDate1,"Y");
        $endyr = date_format($endDate1,"Y");
        $coverPage->addText('Population Health Management Report',  array('size' => 26,'Bold' => true, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addTextBreak(1);
        $coverPage->addText('Time Period:',  array('size' => 18,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText('Medical Data: '.$startDate.' to '.$endDate,  array('size' => 14,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText('Pharmacy Data: '.$startDatePharma.' to '.$endDatePharma,  array('size' => 14,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        // $coverPage->addText($startyr.'-'.$endyr.': '.$startDate.' to '.$endDate,  array('size' => 10,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addText($startyr.'-'.$endyr,  array('size' => 10,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        $coverPage->addTextBreak(2);
        $coverPage->addText('Prepared for:',  array('size' => 18,'Bold' => false, 'color' => 'Black'),array('align'=>'center'));
        
        $coverPage->addText($phmData->name,  array('size' => 26,'Bold' => true, 'color' => 'Black'),array('align'=>'center'));

        //cover page end
        //$section = $phpWord->addSection(['marginLeft' => 570, 'marginRight' => 500, 'marginTop' => 570, 'marginBottom' => 500]);
        $section = $phpWord->addSection(["paperSize" => "Letter",'marginLeft' => 1440, 'marginRight' => 1080, 'marginTop' => 1440, 'marginBottom' => 1080]);
        
        // Define styles
        $fontStyle12 = array('spaceAfter' => 60, 'size' => 10, 'name'=>'Arial');
        $fontStyle10 = array('size' => 10);
        $phpWord->addTitleStyle(null, array('size' => 22, 'bold' => true));
        $phpWord->addTitleStyle(1, array('size' => 16, 'color' => '333333', 'bold' => true));
        $phpWord->addTitleStyle(2, array('size' => 14, 'color' => '333333'));
        $phpWord->addTitleStyle(3, array('size' => 12, 'italic' => true));
        $phpWord->addTitleStyle(4, array('size' => 10));
        
        // Table of contents Start
            $section->addText('Table of contents', array('size' => 16, 'color' => '1f497d', 'bold' => true));
           // $section->addTextBreak(2);
         
            // Add TOC #1
            $toc = $section->addTOC($fontStyle12);
           // $section->addTextBreak(2);
        // Table of contents end
       // $section->addPageBreak();
        
        foreach($phmSectionData as $key => $phmSection){
            $cnt = $key + 1;
            //echo $phmSection->section_title;
            $phmSubSectionData= \DB::table('sub_sections')
                ->where('section_id' ,$phmSection->id)
                ->orderBy('sub_section_no')
                ->get()->toArray();
            
            
            
            if($phmSection->section_title !='' && $phmSection->section_title != null){
                $section->addPageBreak();
                $section->addTitle($cnt.". ".$phmSection->section_title, 1);
            }
           if($phmSection->section_text !='' && $phmSection->section_text != null){
                // $section_text_bold = str_replace("<strong>", "<b>", $phmSection->section_text);
                // $section_text_bold1 = str_replace("</strong>", "</b>", $section_text_bold);
                // $section_text_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($section_text_bold1);
                // $section->addText($section_text_toOpenXML);
                \PhpOffice\PhpWord\Shared\Html::addHtml($section,  $phmSection->section_text, false, false);
                $section->addTextBreak(1);
           }
            $counterr = 1;
            foreach($phmSubSectionData as $phmSubSection){
               // $sub_section_title_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($phmSubSection->sub_section_title);
               if($phmSubSection->sub_section_title !='' && $phmSubSection->sub_section_title != null){
                   if($counterr > 1 || $phmSection->section_text !=''){
                    $section->addPageBreak();
                   }
                    $section->addText($phmSubSection->sub_section_title, array('size' => 14, 'color' => '333333'));
               }
                $counterr++;
                if($phmSubSection->sub_section_text !='' && $phmSubSection->sub_section_text != null){
                    // $sub_section_text_bold = str_replace("<strong>", "<b>", $phmSubSection->sub_section_text);
                    // $sub_section_text_bold1 = str_replace("</strong>", "</b>", $sub_section_text_bold);
                    // $sub_section_text_toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($sub_section_text_bold1);
                    // $section->addText($sub_section_text_toOpenXML);
                    \PhpOffice\PhpWord\Shared\Html::addHtml($section,  $phmSubSection->sub_section_text, false, false);
                }
                $section->addTextBreak(1);
                if($phmSubSection->look_img_url !='' && $phmSubSection->look_img_url != null){
                    $section->addImage(
                        $phmSubSection->look_img_url,
                        array(
                            'width'         => 400,
                            'height'        => 300,
                            'marginTop'     => -1,
                            'marginLeft'    => -1,
                            'wrappingStyle' => 'behind',
                            'align'=>'center'
                        )
                    ); 
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
    public function edit($encrypt_id)
    {
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData = Phm::find($id);

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
        $folderChildUrl1 = $api_url . "folders/88/children";

		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);

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
        
        $phmData= \DB::table('phm')
                    ->select('phm.*','client_folder_mapping.folder_name')
                    ->join('client_folder_mapping','phm.client_id','=','client_folder_mapping.folder_id')
                    ->where('phm.id' , $id)
                    ->first();

        $phmSectionData= \DB::table('sections')
                    ->where('phm_id' , $id)
                    ->orderBy('section_no')
                    ->get();
        $phmSectionDataCount = count($phmSectionData)+1;

        foreach($phmSectionData as $phmSection){
                //echo $phmSection->section_title;
                $phmSubSectionData= \DB::table('sub_sections')
                    ->where('section_id' ,$phmSection->id)
                    ->orderBy('sub_section_no')
                    ->get()->toArray();
              // echo $subsectionCount= count($phmSubSectionData);

               $phmSectioncount['section_id'] = "$phmSection->section_no";
               $phmSectioncount['subSectCount'] = count($phmSubSectionData);
               $phmSectionCountArr[]=$phmSectioncount;
               

                $phmSectionData1['section'] = $phmSection;
                $phmSectionData1['subSection'] = $phmSubSectionData;
                $phmSectionDataArr[]=$phmSectionData1;
        }
        $phmSectionCountArr = json_encode($phmSectionCountArr);
       //exit;
        $reporttypes = DB::select("select report_type_id,report_type from report_type where is_active =1");
       // $folderAccess = users_folder_access::select('folder_id')->where("user_id","=",$id)->get()->toArray();
        return view('phm.edit',compact('activeMenu', 'id','activeSubMenu','phmData','folderChildArr','phmSectionDataArr','phmSectionCountArr','phmSectionDataCount','reporttypes'));
    }

    public function canned($encrypt_id)
    {
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData = Phm::find($id);

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
        $folderChildUrl1 = $api_url . "folders/88/children";

		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);

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
        // $reporttypes = DB::select("select report_type_id,report_type from report_type where is_active =1");
        $reporttypes = \DB::table('report_type')
        ->select('report_type_id','report_type')
        ->where('is_active',1)
        ->get();
       return view('phm.canned',compact('activeMenu', 'id','activeSubMenu','phmData','folderChildArr','phmSectionDataArr','phmSectionCountArr','phmSectionDataCount','reporttypes'));
   
    }
     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($encrypt_id)
    {
        $id = $this->helper->encrypt_decrypt($encrypt_id,'decrypt');
        $activeMenu = '23';
        $activeSubMenu = '0';
        $phmData = Phm::find($id);

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
        $folderChildUrl1 = $api_url . "folders/88/children";

		$childData= $this->curlCall($folderChildUrl, $method1,$query);
		$childData= json_decode($childData, true);

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
        // $reporttypes = DB::select("select report_type_id,report_type from report_type where is_active =1");
        $reporttypes = \DB::table('report_type')
        ->select('report_type_id','report_type')
        ->where('is_active',1)
        ->get();
       // $folderAccess = users_folder_access::select('folder_id')->where("user_id","=",$id)->get()->toArray();
        return view('phm.copy',compact('activeMenu', 'id','activeSubMenu','phmData','folderChildArr','phmSectionDataArr','phmSectionCountArr','phmSectionDataCount','reporttypes'));
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
        $phm->pharma_start_date =date_format(date_create_from_format('m-j-Y', $request->pharma_start_date), 'Y-m-d');
        $phm->pharma_end_date = date_format(date_create_from_format('m-j-Y', $request->pharma_end_date), 'Y-m-d');
        $phm->report_type =$request->report_type;
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




        $id = $this->helper->encrypt_decrypt($id, 'encrypt');
        return redirect('reports/edit/'.$id)->with('success', 'PHM has been successfully created!!');
    }


    public function saveCanned (Request $request, $id)
    {   
        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        
       // $folder_id= $_POST['folder_id'];
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        






        $id = Phm::create([
            'name' =>  $request->name,
            'client_id' =>  $request->client_id,
            'entity_id' => env('env_entity_id'),
            'start_date' => date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d'),
            'end_date' => date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d'),
            'pharma_start_date' =>date_format(date_create_from_format('m-j-Y', $request->pharma_start_date), 'Y-m-d'),
            'pharma_end_date' => date_format(date_create_from_format('m-j-Y', $request->pharma_end_date), 'Y-m-d'),
            'report_type' => $request->report_type,
            'created_by'        => auth()->user()->id,
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
           // echo $counter->subSectCount;
            for($i=1; $i<=$counter->subSectCount; $i++){
                $var_sub_section_heading = "sub_section_heading_".$counter->section_id."_".$i;
                $var_sub_section_text = "sub_section_text_".$counter->section_id."_".$i;
                $var_chart = "chart_".$counter->section_id."_".$i;
                $var_chart_id = "chart_id_".$counter->section_id."_".$i;
                $var_chart_name = "chart_name_".$counter->section_id."_".$i;
                $lookName = $request->$var_chart_name;
                
                //call to lookers folder api
                $look_img_url = "";
                    $look_id = "";
                    $look_name = "";
                if($lookName !="" || $lookName != null){
                    $urlSend ="https://dynpro.cloud.looker.com:443/api/3.1/looks/search?title=".$lookName."&space_id=".$request->client_id;
                    //echo $urlSend;
                    $query = array('access_token' => $responseData['access_token']);
                    $url1 = str_replace(' ', '%20', $urlSend);
                    $method1 = "GET";
                    $lookerData= $this->curlCall($url1, $method1,$query);
                    $lookerData= json_decode($lookerData, true);
                    if(is_array($lookerData) && count($lookerData) > 0){
                        $look_img_url = $lookerData[0]['image_embed_url'];
                        $look_id = $lookerData[0]['id'];
                        $look_name = $lookerData[0]['title'];
                    }
                }
                
             

                $sub_section_id = SubSections::insert([
                    'sub_section_title' =>  $request->$var_sub_section_heading,
                    'sub_section_text' =>  $request->$var_sub_section_text,
                    'sub_section_no' =>$i,
                    'phm_id' => $id,
                    'section_id' => $section_id,
                    'look_img_url' => $look_img_url,
                    'look_id' => $look_id,
                    'look_name' => $look_name,
                ]
                
                );
            }
            
         }
       // exit;

        return redirect('reports')->with('success', 'PHM has been successfully Copied from Master!!');
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
            'entity_id' => env('env_entity_id'),
            'start_date' => date_format(date_create_from_format('m-j-Y', $request->start_date), 'Y-m-d'),
            'end_date' => date_format(date_create_from_format('m-j-Y', $request->end_date), 'Y-m-d'),
            'pharma_start_date' =>date_format(date_create_from_format('m-j-Y', $request->pharma_start_date), 'Y-m-d'),
            'pharma_end_date' => date_format(date_create_from_format('m-j-Y', $request->pharma_end_date), 'Y-m-d'),
            'report_type' =>$request->report_type,
            'created_by'        => auth()->user()->id,   
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
        

        return redirect('reports')->with('success', 'PHM has been successfully Copied!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // $user = Phm::find($id);
        // $user->delete();
        // DB::table("phm")->where("id", $id)->delete();
        // DB::table("sections")->where("phm_id", $id)->delete();
        // DB::table("sub_sections")->where("phm_id", $id)->delete();
        DB::table('phm')->where('id', $id)->update(['is_active' => '0']);
        DB::table('sections')->where('phm_id', $id)->update(['is_active' => '0']);
        DB::table('sub_sections')->where('phm_id', $id)->update(['is_active' => '0']);
        return redirect('reports')->with('success','PHM Report has been deleted successfully!!');
    }

    public function getFolder()
    {
        $lookerSetting = Looker::find('1');
		$api_url = $lookerSetting->api_url;
		$client_id = $lookerSetting->client_id;
        $client_secret = $lookerSetting->client_secret;
        
        $folder_id= $_POST['folder_id'];
        $url = $api_url . "login?client_id=".$client_id."&client_secret=".$client_secret;
        $method = "POST";
        $resp= $this->curlCall($url, $method);
        $responseData = json_decode($resp,true);
        //call to lookers folder api
        $query = array('access_token' => $responseData['access_token']);
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
       \DB::statement("UPDATE `sections` SET `section_no`= section_no-1  WHERE `phm_id`='".$phm_id."' and `section_no` > $sectionId");
       \DB::table('sub_sections')->where('phm_id', $phm_id)->where('section_id', $sectionId)->delete(); 
       echo json_encode("successfull");
       
    }

    public function markMaster(Request $request)
    {

       
       $phm_id = $request->phm_id;
       $flag = $request->flag;
       echo $phm_id."***";
       echo $flag;
       \DB::statement("UPDATE `phm` SET `is_master`= ".$flag."  WHERE `id`='".$phm_id."'");
       
       echo json_encode("successfull");
       
    } 

    public function removeSubSection(Request $request)
    {

       $sectionId = $request->sectionId;
       $subSectionId = $request->subSectionId;
       $phm_id = $request->phm_id;
    //    echo " sectID:".$sectionId;
    //    echo " subSectionId:".$subSectionId;
    //    echo " phm_id:".$phm_id;
       \DB::table('sub_sections')->where('phm_id', $phm_id)->where('sub_section_no', $subSectionId)->where('section_id', $sectionId)->delete();
       \DB::statement("UPDATE `sub_sections` SET `sub_section_no`= sub_section_no-1  WHERE `phm_id`='".$phm_id."' and `section_id` = '".$sectionId."' and `sub_section_no` > $subSectionId"); 
       echo json_encode("successfull");
       
    }

    public function uploadDoc(Request $request)
    {
        if ($request->hasFile('uploadFile')) {
        $file = $request->file('uploadFile');
        $name = time() .'_'. $file->getClientOriginalName();
        $filePath = 'PHM/' . $name;
        Storage::disk('s3')->put($filePath, file_get_contents($file));
        $phm = Phm::find($request->phm_id);
        $phm->file_path = $filePath;
        $phm->save();
        }
        return redirect('reports')->with('success', 'File has been successfully uploaded!!');
    }
    public function download_formatted_copy($folder,$filename)
    {
        $filepath=$folder.'/'.$filename;
        $headers = [
            'Content-Type'        => 'Content-Type: application/zip',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ];

        return \Response::make(Storage::disk('s3')->get($filepath), 200, $headers);
    }
}
