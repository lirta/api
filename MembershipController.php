<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Member;

class MembershipController extends Controller
{

    public function logintwitter(Request $request)
    {
        $this->validate($request, [ 
            'name' => 'required',
            'twitterid' => 'required',
            'deviceid' => 'required',
        ]); 

        $name = $request->input('name');
        $email = $request->input('email');
        $twitterid = $request->input('twitterid');
        $deviceid = $request->input('deviceid');
        $token = $request->header('X-Token');
        $locationlat = "";
        $locationlong = "";
        $ipadd = visitor_ip();

        $now = time();
        $error = array();

        if(empty($twitterid) or empty($deviceid)) {
            $error = 'Periksa kembali data anda';
        }
        if(!empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email tidak valid';
            }
        }

        if ($request->input('devicelocationlat')) {
            $locationlat = $request->input('devicelocationlat');
        }
        if ($request->input('devicelocationlong')) {
            $locationlong = $request->input('devicelocationlong');
        }
        

        if (!$error) {
            
            if (empty($email)) {
                $where="mTwitterId='$twitterid'";
            } else {
                $where="mEmail='$email'";
            }
            
            $member = DB::table('member')->whereRaw("$where AND mDeleted='0' AND mStatus='y'")->first();

            if($member){

                $memberid = $member->mId;
                $cek_member_api = DB::table('member_api')->whereRaw("memberApiDeviceId='$deviceid'")->first();
                if ($cek_member_api) {
                    $id = $cek_member_api->maId;
                    $update_memberapi = DB::table('member_api')->where('maId', $id)->update([
                        'mId' => $memberid
                    ]);
                }
                
                foreach ($member as $key => $value) {
                    $send[$key] = $value;
                }

                $update_lastlogin = DB::table('member')->where('mId', $memberid)->update(['mLastLogin' => $now]);
                if ($update_lastlogin) {
                    $res['success'] = true;
                    $res['message'] = [$send];
                }

                $recheckdevice = DB::table('member_api')->where("memberApiDeviceId", $deviceid)->first();

                    if($recheckdevice->mId){
                        $mid = $recheckdevice->mId;
                    } else {
                        $mid = 0;
                    }

                    $member_address = DB::table('member_location')->insert([
                        'mId' => $mid,
                        'locationLat' => $locationlat,
                        'locationLong' => $locationlong,
                        'locationDate' => date("Y") . "-" . date("m") . "-" . date("d"),
                        'locationDay' => date("d"),
                        'locationMonth' => date("m"),
                        'locationYear' => date("Y"),
                        'locationTime' => date("H:i:s"),
                        'locationTimestamp' => $now,
                        'locationIp' => $ipadd,
                        'locationType' => 'app'
                    ]);

            } else {

                if(!empty($email)) {
                    $checkmember = DB::table('member')->whereRaw("mTwitterId='$twitterid' AND mEmail='$email' AND mDeleted='0'")->get();
                    if(count($checkmember)>0){

                        $res['success'] = false;
                        $res['errorMessage'] = 'Member sudah terdaftar atau akun Anda sedang diblokir';
                        return response($res); 
                    }
                }

                $urlsource = env('SITE_URL');
                $code = codegen(32);
                $codeEmail = codegen(32);
                $codeHP = strtoupper(codegencard(6));
                $expdate = $now + 3600;
                $qrdir = date('dmY');
                $fullname = split_name($name);


                $addmember = DB::table('member')->insertGetId([
                    'mFbId' => '',
                    'mTwitterId' => $twitterid,
                    'mSmsId' => '',
                    'mGoogleId' => '',
                    'mAppleId' => '',
                    'provinsiId' => '',
                    'kabupatenId' => 0,
                    'kecamatanId' => 0,
                    'kelurahanId' => 0,
                    'mFirstName' => $fullname[0],
                    'mLastName' => $fullname[1],
                    'mName' => $name,
                    'mGender' => '',
                    'urlSource' => env('SITE_URL'),
                    'mDir' => '',
                    'mPic' => '',
                    'mPassword' => '',
                    'mCode' => $code,
                    'mEmail' => $email,
                    'mEmailCode' => $codeEmail,
                    'mEmailStatus' => 'n',
                    'mEmailCodeDate' => $expdate,
                    'mHp' => '',
                    'mHpCode' => '',
                    'mHpStatus' => 'n',
                    'mHpCodeDate' => '',
                    'mWA' => '',
                    'mDateReg' => $now,
                    'mLastLogin' => $now,
                    'mStatus' => 'y',
                    'mDeleted' => 0,
                    'mGoogleHD' => '',
                    'mGoogleLink' => '',
                    'mGoogleLocale' => '',
                    'mSmsCountryPrefix' => '',
                ]);

                $cid = $addmember;
                if ($addmember) {
                    
                    if(!empty($deviceid)){
                        $cek_member_api = DB::table('member_api')->whereRaw("memberApiDeviceId='$deviceid'")->first();
                        if ($cek_member_api) {
                            $update_memberapi = DB::table('member_api')->where('memberApiDeviceId', $deviceid)->update([
                                'mId' => $cid,
                            ]);
                        }

                        $recheckdevice = DB::table('member_api')->where("memberApiDeviceId", $deviceid)->first();


                        if($recheckdevice->mId){
                            $mid = $recheckdevice->mId;
                        } else {
                            $mid = 0;
                        }

                        $member_address = DB::table('member_location')->insert([
                            'mId' => $mid,
                            'locationLat' => $locationlat,
                            'locationLong' => $locationlong,
                            'locationDate' => date("Y") . "-" . date("m") . "-" . date("d"),
                            'locationDay' => date("d"),
                            'locationMonth' => date("m"),
                            'locationYear' => date("Y"),
                            'locationTime' => date("H:i:s"),
                            'locationTimestamp' => $now,
                            'locationIp' => $ipadd,
                            'locationType' => 'app'
                        ]);

                    }
                }

                if(!empty($email)) {
                    $datereg = $now;
                    $getmemberdetail = DB::table('member')->whereRaw("mId='$cid' AND mEmailStatus = 'n'")->first();
                    if ($getmemberdetail) {
                        $verifycode = codegen(32);
                        $cusname = $getmemberdetail->mName;
                        $to = $getmemberdetail->mEmail;
                        $gettemplate = DB::table('email_template')->whereRaw("tId='1'")->first();
                        $subject = "Verfikasi Email";

                        $header = '<img src="'.env('SITE_URL').'/assets/images/emailhead.jpg" width="250">';
                        $message = $gettemplate->tEmail;
                        $message = str_ireplace("{HEADER}", $header, $message);
                        $message = str_ireplace("{MEMBERNAME}", stripquote($cusname), $message);
                        $message = str_ireplace("{EMAILVERIFYURL}", env('SITE_URL') . "/member/verifikasi-email/" . $cid . "/" . $getmemberdetail->mEmailCode . "/", $message);
                        $message = str_ireplace("{SYSTEMNAME}", env('SITE_NAME'), $message);
                        $message = str_ireplace("{FOOTERCONTACT}", '
                            <b>Tel</b>. <a style="color:#5a5a5a;" href="#" target="_blank"><u>081234567890</u></a> |
                              <b>WA</b>. <a style="color:#5a5a5a;" href="https://wa.me/62812345678" target="_blank"><u>081234567890</u></a> |
                              <b>Email</b>. <a style="color:#5a5a5a;" href="mailto:' . env('SITE_URL') . '" target="_blank"><u>' . env('SITE_URL') . '</u></a>
                         ', $message);
                        // $message = str_ireplace("{FOOTERSOSMED}", '
                        //           <a style="text-decoration:none" href="https://www.facebook.com/ecommerce/" target="_blank"  rel="nofollow" >
                        //             <img alt="img" src="' . env('SITE_URL') . '/assets/images/fb.png" width="35" height="35">
                        //           </a>
                        //           <a style="text-decoration:none" href="https://twitter.com/ecommerce/" target="_blank" rel="nofollow">
                        //             <img alt="img" src="' . env('SITE_URL') . '/assets/images/tw.png" width="35" height="35">
                        //           </a>
                        //           <a style="text-decoration:none" href="https://www.instagram.com/bisnislab/" target="_blank" rel="nofollow">
                        //             <img alt="img" src="' . env('SITE_URL') . '/assets/images/ig.png" width="35" height="35">
                        //           </a>
                        //           <a style="text-decoration:none" href="https://www.youtube.com/user/ecommerce" target="_blank" >
                        //             <img alt="img" src="' . env('SITE_URL') . '/assets/images/yt.png" width="35" height="35">
                        //           </a>      
                        //     ', $message);
                        $start = 2019;
                        $message = str_ireplace("{FOOTERCOPYRIGHT}", 'Copyright &copy; ' . $start . ' ' . (date("Y") > $start ? " - " . date("Y") : '') . getval("setValue","systemsetting","setId = '1'") . ' All rights reserved.', $message);
                        $from = getval("setValue","systemsetting","setId = '8'");
                        $fromname = getval("setValue","systemsetting","setId = '1'");
                        $header = $from . "#" . $fromname;
    
                        $isemailexist = DB::table('email_queue')->whereRaw("emailMsg='$message' AND emailTo='$to'")->first();
                        $blackemail = DB::table('email_blacklist')->whereRaw("blackEmail='$to'")->first();
                        if (!$isemailexist and !$blackemail) {
                            $querytomember = DB::table('email_queue')->insertGetId(['emailTo' => $to,
                                'emailSubject' => $subject,
                                'emailMsg' => $message,
                                'emailMsgType' => 'html',
                                'emailHead' => $header,
                                'emailDate' => $now,
                                'emailStatus' => 'n',
                                'emailAttachDir' => '',
                                'emailAttachFile' => '',
                            ]);
                        } else {
                            $res['success'] = false;
                            $res['errorMessage'] = 'Email anda termasuk dalam spam oleh sistem , silahkan hubungi admin@rocaloca.com untuk pengecekan ulang';
                            return response($res);
                        }
                    }
                }

                $login = DB::table('member')->whereRaw("mId='$cid'")->first();
                foreach ($login as $key => $value) {
                    $send[$key] = $value;
                }

                if(!empty($email)) {
                     $sqlgetonesignal = DB::table('member_notification_data')->whereRaw("notifPlayerId!='' AND notifTags LIKE '%$deviceid%'")->get();
                    if(count($sqlgetonesignal) > 0){
                        foreach($sqlgetonesignal as $datanotif){
                            $arronesignal[]=$datanotif->notifPlayerId;
                        }
                        $notifto=implode(",", $arronesignal);
                        $statusnotif="new";
                    }else{
                        $notifto="";    
                        $statusnotif="sent";
                    }

                    $now = time();
                    $day = date('d');
                    $month = date('m');
                    $year = date('Y');
                    $time = date('H:i:s');
                    $date = date("Y-m-d");
                    $insertnotif = DB::table('notification')->insertGetId([
                        'mId' => $cid,
                        'notificationTitle' => 'Registrasi Berhasil',
                        'notificationContent' => 'Selamat akun Anda telah aktif, silahkan lengkapi profil di halaman Account',
                        'notificationDay' => $day,
                        'notificationMonth' => $month,
                        'notificationYear' => $year,
                        'notificationdDate' => $date,
                        'notificationTime' => $time,
                        'notificationFullTime' => $now, 
                        'notificationSent' => $now,
                        'notificationTo' => $notifto,
                        'notificationStatus' => $statusnotif,
                        'notificationPage' => 'n',
                        'notificationAppsPage' => 'home:0',
                    ]);
                }

            }

            $res['success'] = true;
            $res['message'] = [$send];
            return response($res);
            
        } else {
            $res['success'] = false;
            $res['errorMessage'] = $error;
            return response($res);
        }
    }

    public function hpverification(Request $request)
    {
        $this->validate($request, [
            'hp' => 'required',
            'mid' => 'required',
        ]);

        $hp = $request->input('hp');
        $mid = $request->input('mid');
        $error = array();
        
        if(empty($hp) or empty($mid)) {
            $error = 'Periksa kembali data anda';
        } 

        $member = DB::table('member')->whereRaw("mId ='$mid' AND mDeleted='0' AND mStatus='y'")->first();
        if(!$member){
            $error = 'Member tidak ditemukan';
        }

        if (is_numeric($hp)) {
            if (substr($hp, 0, 2) != "62") {
                $trimhp = ltrim($hp, "0");
                $hpnew = "62" . $trimhp;
            } else {
                $hpnew = $hp;
            }
        } else {
            $error = 'Nomor HP tidak valid';
        }

        $checkhp = DB::table('member')->whereRaw("mHp ='$hpnew'")->get();
        if(count($checkhp)>0){
            $error = 'Nomor HP sudah digunakan';
        }

        if(!$error) {

            $now = time();
            $urlsource = env('SITE_URL');
            $code = codegen(32);
            $codeEmail = codegen(32);
            $codeHP=strtoupper(codegencard(6));
            $expdate = $now + 3600;

            $updatehp = DB::table('member')->where('mId', $mid)->update([
                'mHp' => $hpnew,
                'mHpCode' => $codeHP,
                'mHpStatus' => 'y',
                'mHpCodeDate' => $expdate,
            ]);

            $res['success'] = true;
            $res['message'] = 'Nomor HP berhasil diverifikasi';
            return response($res, 200);

        } else {

            $res['success'] = false;
            $res['errorMessage'] = $error;
            return response($res, 404);
        }


    }

    public function edithp(Request $request)
    {
        $this->validate($request, [
            'mid' => 'required',
            'newphone' => 'required',
        ]);
        $mid = $request->input('mid');

        $newphone = $request->input('newphone');
        $now = time();
        $rphone = ltrim($newphone, "0");
        $hp = "62" . $rphone;

        if ($mid  AND $newphone) {
            #$password = sha1($password . env('CMS_SALT_STRING'));
            $getmember = DB::table('member')->whereRaw("mHP='$newphone' OR mHP='$hp'")->first();
            if (!$getmember) {


                    $getrealpassword = DB::table('member')->whereRaw("mId='$mid'")->first();
                    if ($getrealpassword) {
                        $rphone = ltrim($newphone, "0");
                        $arraynumber = array();
                        $updatehp = DB::table('member')->where('mId', $mid)->update([
                            'mHp' => $hp,
                        ]);
                        $res['success'] = true;
                        $res['message'] = "Nomor HP berhasil diubah";
                        return response($res);
                    } else {
                        $res['success'] = false;
                        $res['errorMessage'] = 'Member tidak ditemukan.';
                        return response($res);
                    }

            } else {
                $res['success'] = false;
                $res['errorMessage'] = 'Nomor hp sudah digunakan.';
                return response($res);
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = 'Nomor hp tidak boleh kosong.';
            return response($res);
        }
    }


    public function checkhp(Request $request)
    {
        $this->validate($request, [
            'mid' => 'required',
            'newphone' => 'required',
        ]);
        $mid = $request->input('mid');

        $newphone = $request->input('newphone');
        $now = time();
        $rphone = ltrim($newphone, "0");
        $hp = "62" . $rphone;

        if ($mid  AND $newphone) {
            #$password = sha1($password . env('CMS_SALT_STRING'));
            $getmember = DB::table('member')->whereRaw("mHP='$newphone' OR mHP='$hp'")->first();
            if (!$getmember) {


                    $getrealpassword = DB::table('member')->whereRaw("mId='$mid'")->first();
                    if ($getrealpassword) {
                        $rphone = ltrim($newphone, "0");
                        $arraynumber = array();
                     
                        $res['success'] = true;
                        $res['message'] = "y";
                        return response($res);
                    } else {
                        $res['success'] = false;
                        $res['errorMessage'] = 'Member tidak ditemukan.';
                        return response($res);
                    }

            } else {
                $res['success'] = false;
                $res['errorMessage'] = 'Nomor hp sudah digunakan.';
                return response($res);
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = 'Nomor hp tidak boleh kosong.';
            return response($res);
        }
    }

    public function notification(Request $request, $id)
    {

        $notif = DB::table('notification')->whereRaw("mId='$id' or notificationTo='all' or notificationTo='mobile'")->orderBy('notificationSent', 'desc')->get();

        if (count($notif) > 0) {
            
            foreach ($notif as $datax) {
                $arra[] = $datax;
            }

            $res['success'] = true;
            $res['message'] = $arra;
            return response($res, 200);

        } else {

            $res['success'] = false;
            $res['errorMessage'] = 'Tidak ada notifikasi';
            return response($res, 200);

        }

    }

    public function unreadnotification(Request $request, $mId)
    {
        $totalbadge = 0;
        if(!empty($mId)){
            $query = DB::table('notification')->whereRaw("mId='$mId'")->orderBy('notificationSent', 'DESC')->get();
            // $query = DB::table('notification')->whereRaw("employeeId LIKE '%\"$mId\"%'")->orderBy('notificationSent', 'DESC')->get();
            if(count($query) > 0){
                foreach($query as $index => $data){
                    $arrayid = json_decode($data->mId); 
                    if ($mId == $arrayid) {
                        $arrayread = json_decode($data->notificationReadId);
                        if ($arrayread != null) {
                          if (!in_array($mId, $arrayread)) {
                            $totalbadge++;
                          }
                        }else {
                          $totalbadge++;
                        } 
                    }
                }
                $res['success'] = true;
                $res['message'] = $totalbadge;
            } else {
                $res['success'] = false;
                $res['errorMessage'] = "No notification found.";
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = "mId staff dan id perusahaan wajib diisi.";
        }
        return response($res, 200);
    }

    public function readallnotification(Request $request, $mId)
    {
        if(!empty($mId)){
            $query = DB::table('notification')->whereRaw("mId='$mId'")->orderBy('notificationSent', 'DESC')->get();
            if(count($query) > 0){
                foreach($query as $index => $data){
                    $arrayid = array();
                    $notifReadId = $data->notificationReadId;
                    if($notifReadId){
                        $arrayid = json_decode($notifReadId);
                        if(!in_array($mId, $arrayid)){
                            array_push($arrayid, $mId);
                        }
                    } else {
                        $arrayid[] = $mId; 
                    }
                    $arrayupdate = json_encode($arrayid);
                    $queryRead = DB::table('notification')->where('notificationId', $data->notificationId)->update([
                        'notificationReadId' => $arrayupdate,
                    ]);
                }
                $res['success'] = true;
                $res['message'] = "All notifications have been mark as read.";
            } else {
                $res['success'] = false;
                $res['errorMessage'] = "No notification found.";
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = "mId dan companyId wajib diisi.";
        }
        return response($res, 200);
    }

    public function readitemnotification(Request $request, $mId, $notificationId)
    {
        if(!empty($mId) && !empty($notificationId)){
            $query = DB::table('notification')->whereRaw("notificationId=$notificationId")->first();
            if($query){
                $notifReadId = $query->notificationReadId;
                if($notifReadId){
                    $arrayid = json_decode($notifReadId);
                    if(!in_array($mId, $arrayid)){
                        array_push($arrayid, $mId);
                    }
                } else {
                    $arrayid[] = $mId; 
                }
                $arrayupdate = json_encode($arrayid);
                $queryRead = DB::table('notification')->where('notificationId', $notificationId)->update([
                    'notificationReadId' => $arrayupdate,
                ]);
                $res['success'] = true;
                $res['message'] = $arrayupdate;
            } else {
                $res['success'] = false;
                $res['errorMessage'] = "Tidak ada notifikasi.";
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = "mId staff dan id notifikasi wajib diisi.";
        }
        return response($res, 200);
    }

    public function carddesign(Request $request) {
     
        $bidang = DB::select("SELECT * FROM member_card_design ORDER BY mdesignSort ASC");
        
        if(count($bidang) > 0){
            $arraybidang = array();
            foreach($bidang as $data){
                array_push($arraybidang,[
                    'mdesignId' => $data->mdesignId,
                    'mdesignName' => $data->mdesignName,
                    'mdesignDir' => $data->mdesignDir,
                    'mdesignFile' => $data->mdesignFile,
                    'mdesignSort' => $data->mdesignSort,
                    'mdesignTimestamp' => $data->mdesignTimestamp,
                    'cardurl' => $data->mdesignFile == "" ? "" : env('SITE_URL')."/assets/mcard/".$data->mdesignDir."/".$data->mdesignFile,
                ]);
            }
            $res['success'] = true;
            $res['message'] = $arraybidang;

            return response($res, 200);
        }else{
            $res['success'] = false;
            $res['errorMessage'] = 'kartu member tidak ada';

            return response($res, 404);
        }        
    }

    public function checkhploginsms(Request $request, $phone)
    {
      
        $now = time();

        if(is_numeric($phone)) {

            $rphone = ltrim($phone, "0");
            $hp = "62" . $rphone;

        } else {

            $res['success'] = false;
            $res['errorMessage'] = 'Nomor HP tidak valid.';
            return response($res);
        }

        if ($phone) {
            $getmember = DB::table('member')->whereRaw("mHP='$phone' OR mHP='$hp'")->first();
            if ($getmember) {

                $res['success'] = true;
                $res['message'] = $getmember->mId;
                return response($res);

            } else {
                $res['success'] = false;
                $res['errorMessage'] = 'Nomor HP belum terdaftar.';
                return response($res);
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = 'Silahkan masukkan nomor HP.';
            return response($res);
        }
    }

    public function loginsms(Request $request)
    {
        $this->validate($request, [ 
            'hp' => 'required',
            'deviceid' => 'required',
        ]); 

        $hp = $request->input('hp');
        $deviceid = $request->input('deviceid');
        $token = $request->header('X-Token');
        $locationlat = "";
        $locationlong = "";
        $ipadd = visitor_ip();

        $now = time();
        $error = array();

        if(empty($hp) or empty($deviceid)) {
            $error = 'Periksa kembali data anda';
        }
     

        if ($request->input('devicelocationlat')) {
            $locationlat = $request->input('devicelocationlat');
        }
        if ($request->input('devicelocationlong')) {
            $locationlong = $request->input('devicelocationlong');
        }

        if(is_numeric($hp)) {
            $rphone = ltrim($hp, "0");
            $phone = "62" . $rphone;
        } else {
            $error = 'Nomor HP tidak valid';
        }
        
        if (!$error) {
            
            $member = DB::table('member')->whereRaw("mHP='$hp' OR mHP='$phone'")->first();

            if($member){

                $memberid = $member->mId;
                $cek_member_api = DB::table('member_api')->whereRaw("memberApiDeviceId='$deviceid'")->first();
                if ($cek_member_api) {
                    $id = $cek_member_api->maId;
                    $update_memberapi = DB::table('member_api')->where('maId', $id)->update([
                        'mId' => $memberid
                    ]);
                }
                
                foreach ($member as $key => $value) {
                    $send[$key] = $value;
                }

                $update_lastlogin = DB::table('member')->where('mId', $memberid)->update(['mLastLogin' => $now]);
                if ($update_lastlogin) {
                    $res['success'] = true;
                    $res['message'] = [$send];
                }

                $recheckdevice = DB::table('member_api')->where("memberApiDeviceId", $deviceid)->first();

                    if($recheckdevice->mId){
                        $mid = $recheckdevice->mId;
                    } else {
                        $mid = 0;
                    }

                    $member_address = DB::table('member_location')->insert([
                        'mId' => $mid,
                        'locationLat' => $locationlat,
                        'locationLong' => $locationlong,
                        'locationDate' => date("Y") . "-" . date("m") . "-" . date("d"),
                        'locationDay' => date("d"),
                        'locationMonth' => date("m"),
                        'locationYear' => date("Y"),
                        'locationTime' => date("H:i:s"),
                        'locationTimestamp' => $now,
                        'locationIp' => $ipadd,
                        'locationType' => 'app'
                    ]);

                    $res['success'] = true;
                    $res['message'] = [$send];
                    return response($res);

            } else {

                $res['success'] = false;
                $res['errorMessage'] = "Member tidak terdaftar";
                return response($res);

            }
            
        } else {
            $res['success'] = false;
            $res['errorMessage'] = $error;
            return response($res);
        }
    }

    public function emailcheck(Request $request)
    {
        $this->validate($request, [
            'mid' => 'required',
            'email' => 'required',
        ]);
        $mid = $request->input('mid');
        $newemail = strtolower(trim($request->input('email')));
        $now = time();

        if ($newemail) {
            if (filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
                $getmember = DB::table('member')->whereRaw("mEmail='$newemail' AND mId!='$mid'")->first();
                if (!$getmember) {
                        $getmemberdetail = DB::table('member')->whereRaw("mId='$mid'")->first();
                        $codeEmail = codegen(32);
                        $expdate = $now + 3600;
                        $cusname = $getmemberdetail->mName;
                        $to = $newemail;

                        $tempemail = getval('tSubject,tEmail','email_template',"tId='1'");

                        if($tempemail){
                            $subject = $tempemail['tSubject'];
                            $message = $tempemail['tEmail'];
                        }

                        $header = '<img src="'.env('SITE_URL').'/assets/images/emailhead.jpg" width="250">';

                        $message = str_ireplace("{HEADER}", $header, $message);
                        $message = str_ireplace("{MEMBERNAME}", stripquote($cusname), $message);
                        $message = str_ireplace("{EMAILVERIFYURL}", env('SITE_URL') . "/member/verifikasi-email/" . $mid . "/" . $codeEmail . "/", $message);
                        $message = str_ireplace("{SYSTEMNAME}", getval("setValue","systemsetting","setId = '1'"), $message);
                        $message = str_ireplace("{FOOTERCONTACT}", '
                            <b>Tel</b>. <a style="color:#5a5a5a;" href="#" target="_blank"><u>081234567890</u></a> |
                              <b>WA</b>. <a style="color:#5a5a5a;" href="https://wa.me/62812345678" target="_blank"><u>081234567890</u></a> |
                              <b>Email</b>. <a style="color:#5a5a5a;" href="mailto:' . env('SITE_URL') . '" target="_blank"><u>' . env('SITE_URL') . '</u></a>
                         ', $message);
                        $message = str_ireplace("{FOOTERSOSMED}", '
                                  <a style="text-decoration:none" href="https://www.facebook.com/ecommerce/" target="_blank"  rel="nofollow" >
                                    <img alt="img" src="' . env('SITE_URL') . '/assets/images/fb.png" width="35" height="35">
                                  </a>
                                  <a style="text-decoration:none" href="https://twitter.com/ecommerce/" target="_blank" rel="nofollow">
                                    <img alt="img" src="' . env('SITE_URL') . '/assets/images/tw.png" width="35" height="35">
                                  </a>
                                  <a style="text-decoration:none" href="https://www.instagram.com/bisnislab/" target="_blank" rel="nofollow">
                                    <img alt="img" src="' . env('SITE_URL') . '/assets/images/ig.png" width="35" height="35">
                                  </a>
                                  <a style="text-decoration:none" href="https://www.youtube.com/user/ecommerce" target="_blank" >
                                    <img alt="img" src="' . env('SITE_URL') . '/assets/images/yt.png" width="35" height="35">
                                  </a>      
                            ', $message);
                        $start = 2019;
                        $message = str_ireplace("{FOOTERCOPYRIGHT}", 'Copyright &copy; ' . $start . ' ' . (date("Y") > $start ? " - " . date("Y") : '') . getval("setValue","systemsetting","setId = '1'") . ' All rights reserved.', $message);
                        $from = getval("setValue","systemsetting","setId = '8'");
                        $fromname = getval("setValue","systemsetting","setId = '1'");
                        $header = $from . "#" . $fromname;
                        
                        $isemailexist = DB::table('email_queue')->whereRaw("emailMsg='$message' AND emailTo='$to'")->first();
                        $blackemail = DB::table('email_blacklist')->whereRaw("blackEmail='$to'")->first();
                        if (!$isemailexist and !$blackemail) {
                            $querytomember = DB::table('email_queue')->insertGetId(['emailTo' => $to,
                                'emailSubject' => $subject,
                                'emailMsg' => $message,
                                'emailMsgType' => 'html',
                                'emailHead' => $header,
                                'emailDate' => $now,
                                'emailStatus' => 'n',
                                'emailAttachDir' => '',
                                'emailAttachFile' => '',
                            ]);
                            if ($querytomember) {

                                $dataarray = array();

                                $update_memberemail = DB::table('member')->where('mId', $mid)->update([
                                    'mEmail' =>  $newemail,
                                    'mEmailCode' => $codeEmail,
                                    'mEmailCodeDate' => $expdate,
                                ]);
                                if ($update_memberemail) {
                                    array_push($dataarray, [
                                        // 'mId' => $mid,
                                        'email' => $newemail,
                                        // 'emailCode' => $getmemberdetail->mEmailCode
                                    ]);
                                    $res['success'] = true;
                                    $res['verify'] = $dataarray;
                                    $res['message'] = "Link verifikasi telah dikirim ke email Anda. Email Anda akan berubah otomatis setelah verifikasi";
                                    return response($res);
                                }
                            } else {
                                $res['success'] = false;
                                $res['errorMessage'] = 'Email gagal dikirim, silahkan coba beberapa saat lagi.';
                                return response($res);
                            }
                        } 
                        else {
                            $res['success'] = false;
                            $res['errorMessage'] = 'Email anda termasuk dalam spam oleh sistem , silahkan hubungi admin@bappedapelalawan.go.id untuk pengecekan ulang';
                            return response($res);
                        }
                } else {
                    $res['success'] = false;
                    $res['errorMessage'] = 'Email sudah digunakan, silahkan gunakan email lainnya.';
                    return response($res);
                }
            } else {
                $res['success'] = false;
                $res['errorMessage'] = 'Email yang anda masukan salah.';
                return response($res);
            }
        } else {
            $res['success'] = false;
            $res['errorMessage'] = 'Email baru tidak boleh kosong.';
            return response($res);
        }
    }

   
}

