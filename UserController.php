<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Member;

class UserController extends Controller

{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    
    public function index(Request $request){
        /*$res['success'] = true;
        $res['message'] = 'Success register!';
        return response($res);*/
        return 'asxasx';
    }
    
    
    public function register(Request $request)
    {
        $hasher = app()->make('hash');
        $username = $request->input('username');
        $email = $request->input('email');
        $password = $hasher->make($request->input('password'));
        $register = User::create([
            'username'=> $username,
            'email'=> $email,
            'password'=> $password,
        ]);
        if ($register) {
            $res['success'] = true;
            $res['message'] = 'Success register!';
            return response($res);
        }else{
            $res['success'] = false;
            $res['message'] = 'Failed to register!';
            return response($res);
        }
    }
    
    
    public function get_user(Request $request, $id)
    {
        $user = User::where('id', $id)->get();
        if ($user) {
              $res['success'] = true;
              $res['message'] = $user;
        
              return response($res);
        }else{
          $res['success'] = false;
          $res['message'] = 'Cannot find user!';
        
          return response($res);
        }
        
    }
 /*   public function endek($encrypt_decrypt,$string){
        $password = env('PASSWORD_ENCRYPT');
        $method = 'aes-128-cfb';
        $iv = substr(hash('sha1', $password), 0, 16);
        $output='';
        if($encrypt_decrypt=='encrypt'){
            $output = openssl_encrypt($string, $method, $password, 0, $iv);
            $output = base64_encode($output);
        } else if($encrypt_decrypt=='decrypt'){
            $output = base64_decode($string);
            $output = openssl_decrypt($output, $method, $password, 0, $iv);
        }
        return $output;
    }*/
    function checkpoint(Request $request,$table, $columnId, $id) {
        //$sqlcek=query("SELECT checkPoint FROM $table");
        $sqlcek = DB::select("SELECT checkPoint FROM $table");
        $db=env('DB_DATABASE');
        //$db="cdcasxzxra";
        if (!$sqlcek){
            $hasil=FALSE;
        }else{
            $query = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db' and TABLE_NAME = '$table' and (COLUMN_NAME='mId' or COLUMN_NAME='mPoin' or COLUMN_NAME='mRedeemPoin' or COLUMN_NAME='mTotalPoin' or COLUMN_NAME='mVisit')");
            $no=0;

            foreach ($query as $key => $value){
                $queryx =  DB::select("select ".$value->COLUMN_NAME." as a from $table where $columnId='$id'");
                foreach ($queryx as $keyx => $valuex){
                    $datax[$no][$value->COLUMN_NAME] = $valuex->a;
                    //$datax[$no][$data['COLUMN_NAME']]= $valuex[$value->['COLUMN_NAME']];
                    //print $valuex->COLUMN_NAME;
                }
                $no++;
            }


            $querycp =  DB::select("select checkPoint from $table where $columnId='$id'");
            $text_json=json_encode($datax);
            $cp = endek('encrypt',$text_json);

            var_dump($datax);
            var_dump($querycp[0]->checkPoint);
            dd($cp);


            $update = DB::table($table)->whereRaw("checkPoint='$cp'")->update([ $columnId => $id,]);

            //$update=query("update $table set checkPoint='$cp' where $columnId='$id'");
            $hasil=TRUE;
            return $hasil;



//            if ($cp==$querycp[0]->checkPoint){
//
//            }else{
//                $now = time();
//                $query = DB::table('storesetting')->whereRaw("setId='6'")->update(['setValue' => 'off',]);
//                if($query){
//                    $getval = DB::table('storesetting')
//                        ->select(DB::raw('setValue'))
//                        ->where([
//                            ['setVar', '=', "SITE_EMAIL"],
//                        ])->first();
//
//                    $getvalnews = DB::table('storesetting')
//                        ->select(DB::raw('setValue'))
//                        ->where([
//                            ['setVar', '=', "NEWSLETTER_HEADER"],
//                        ])->first();
//
////                ----------------------------------KIRIM EMAIL KE DEVELOPER --------------------------
//                    $pesan = '<img src="'.env('SITE_URL').$getvalnews->setValue.'" title="Logo" alt="logo" />"<br />Pada tabel <b> '.$table.' </b> terdapat data yang invalid di '.$columnId.'  : <b> '.$id.' </b>';
//
//                    $sentsubject = "DATA ERROR";
//                    $urlsource = "https://jnpgroup.com";
//                    $head = $getval->setValue . "#" . env('SITE_NAME');
//                    $querytomember = DB::table('email_queue')->insertGetId([
//                        'emailTo' => $getval->setValue,
//                        'emailSubject' => $sentsubject,
//                        'emailMsg' => $pesan,
//                        'emailMsgType' => 'html',
//                        'emailHead' => $head,
//                        'emailDate' => $now,
//                        'emailDateSent' => '0',
//                        'emailStatus' => 'y',
//                        // 'urlSource' => $urlsource,
//                        'emailAttachDir' => '',
//                        'emailAttachFile' => '',
//                        'emailAttachType' => '',
//                    ]);
//                }
//            }
        }
    }


    //
}
