<?php
	
class FMI
{

	private function realUDID($UDID)
	{
		if(!strpos($UDID,'-')) 
			return strtolower($UDID);
		else 
			return strtoupper($UDID);
	}
	
	public function add($appleID)
	{
		$deviceFolder = "./data/registeredAppleID/";
        if (!file_exists($deviceFolder))  
			mkdir($deviceFolder, 0777, true);
		if(is_null($appleID) || !filter_var($appleID, FILTER_VALIDATE_EMAIL))
			return "Apple ID is not vaild";
		if (!file_exists($deviceFolder.$appleID))
		{
			file_put_contents($deviceFolder.$appleID,$appleID);
			return $appleID.' success registed port 103.252.95.89:8883';
		}
			return $appleID.' existed port 103.252.95.89:8883';
	}
	
		public function apiRemove($appleID)
	{
		 $tokenFolder = "./data/tokenAppleID/$appleID/";
         if (file_exists($tokenFolder)) // check Token
        
		return $this->removePETv2($appleID, file_get_contents($tokenFolder."petToken"));

        
	else 
		return "Not Found Token Or Expired";
	
	}
	
	
	public function removePET($appleID, $PET, $UDID)
	{
		$UDID = $this->realUDID($UDID);
		$token = $this->fmipWipeToken($appleID,$PET);
		
		if($token == 'Token Expired')	
			return 'Token Expired';

		$decode = explode(":",$token);
		$DSID = $decode[0];
		$mmeFMIPWipeToken = $decode[1];
		$dataResponse = $this->remove($DSID,$mmeFMIPWipeToken,$UDID);
		
		if($dataResponse)
			return "Find My iPhone: OFF";
		else
			return 'Unauthorized';
	}
	
		public function removePETv2($appleID, $PET)
	{
        $dataResponse = $this->removeFix($appleID, $PET);
		return $dataResponse;
	}
	
	public function loginDelegates($appleID, $Password)
	{
		require_once 'PlistParser.php';
		$plistParser = new PlistParser;
		$url = "https://setup.icloud.com/setup/iosbuddy/loginDelegates";
	    $postData = '<?xml version="1.0" encoding="UTF-8"?>
		<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
		<plist version="1.0">
		<dict>
			<key>apple-id</key>
			<string>'.$appleID.'</string>
			<key>client-id</key>
			<string>FEF008A5-F554-46A1-9057-E4CF335668EF</string>
			<key>delegates</key>
			<dict>
				<key>com.apple.gamecenter</key>
				<dict/>
				<key>com.apple.mobileme</key>
				<dict/>
				<key>com.apple.private.ids</key>
				<dict>
					<key>protocol-version</key>
					<string>4</string>
				</dict>
			</dict>
			<key>password</key>
			<string>'.$Password.'</string>
		</dict>
		</plist>';
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL , $url ); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: es-es",  "Accept: */*",  "Content-Type: text/plist", "X-Apple-Find-API-Ver: 6.0", "X-Apple-I-MD-RINFO: 17106176", "Connection: keep-alive", "Content-Length: ".strlen($postData), "X-Apple-Realm-Support: 1.0", "X-MMe-Client-Info: <iPod5,1> <iPhone OS;9.3.5;13G36> <com.apple.AppleAccount/1.0 (com.apple.accountsd/113)>"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT , "accountsd/113 CFNetwork/758.5.3 Darwin/15.6.0" );
		curl_setopt($ch, CURLOPT_POST , 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS , $postData); 
 
		$response = curl_exec($ch); 
 
		if (curl_errno($ch))
			echo "Error: " . $curl_error($ch) . "<br>";
 
		curl_close($ch);
 
		$wipePlist=$plistParser->StringToArray($response);
		return $wipePlist;
	}

	public function fmipWipeToken($appleID, $Password)
	{
		require_once 'PlistParser.php';
		$plistParser = new PlistParser;
		$url = 'https://setup.icloud.com/setup/fmipauthenticate/$appleID';
		$basic=base64_encode($appleID.':'.$Password);
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL , $url ); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array(
			"Host: setup.icloud.com", 
			"Accept: */*",
			"Authorization: Basic".$basic,
			"Proxy-Connection: keep-alive",
			"X-MMe-Country: EC",
			"X-MMe-Client-Info: <iPhone7,2> <iPhone OS;8.1.2;12B440> <com.apple.AppleAccount/1.0 (com.apple.Preferences/1.0)>",
			"Accept-Language: es-es",
			"Content-Type: text/plist",
			"Connection: keep-alive"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT , "User-Agent: Ajustes/1.0 CFNetwork/711.1.16 Darwin/14.0.0" );
		curl_setopt($ch, CURLOPT_POST , 1); 

		$response = curl_exec($ch); 

		if (curl_errno($ch))
			echo "Error: " . $curl_error($ch) . "<br>";
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code!=200)
			return "Token Expired";

		$result = $plistParser->StringToArray($response);
		$DSID=$result['appleAccountInfo']['dsid'];
		$mmeFMIPWipeToken=$result['tokens']['mmeFMIPWipeToken'];

		return $DSID.":".$mmeFMIPWipeToken;
	}

	public function remove($DSID, $mmeFMIPWipeToken, $UDID)
		{
			$url = "https://p33-fmip.icloud.com/fmipservice/findme/".$DSID."/".$UDID."/unregisterV2";
			$postData = '{
				"deviceContext": {
					"deviceTS": "2017-02-01T20:33:11.880Z"
				},
				"deviceInfo": {
					"productType": "iPhone6,1",
					"udid": "'.$UDID.'",
					"fmipDisableReason": 1,
					"buildVersion": "13G36",
					"productVersion": "9.3.5"
				}
			}';
			$basic = base64_encode($DSID.':'.$mmeFMIPWipeToken);
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL , $url ); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
			curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Host: p33-fmip.icloud.com", 
				"Accept-Language: es-es", 
				"X-Apple-PrsId: ".$DSID,  
				"Accept: */*",  
				"Content-Type: application/json", 
				"X-Apple-Find-API-Ver: 6.0", 
				"X-Apple-I-MD-RINFO: 17106176", 
				"Connection: keep-alive", 
				"Authorization: Basic ".$basic, 
				"Content-Length: ".strlen($postData), 
				"X-Apple-Realm-Support: 1.0"));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_USERAGENT , "User-Agent: FMDClient/6.0 iPod5,1/13G36" );
			curl_setopt($ch, CURLOPT_POST , 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS , $postData); 
			$response = curl_exec($ch); 
			
			if (curl_errno($ch))
				echo "Error: " . $curl_error($ch) . "<br>";
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		
			if($http_code!=200)
				return false;
			return true;
	}
		public function removeFix($appleID, $PET)
	{
        $appToken=base64_encode($appleID.":".$PET);
        $curl = curl_init("https://fmipmobile.icloud.com/fmipservice/device/initClient");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Apple-Realm-Support: 1.0",
            "X-Apple-I-MD-RINFO: 17106176",
            "Accept: */*",
            "Authorization: Basic ".$appToken,
            "Accept-Language: en-us",
            "Content-Type: application/json; charset=utf-8",
            "X-Apple-Find-API-Ver: 3.0",
            "X-Apple-I-Client-Time: 2017-02-27T22:07:55Z",
            "X-Apple-AuthScheme: UserIdGuest",
            "User-Agent: FindMyiPhone/500 CFNetwork/808.3 Darwin/16.3.0",
            //"Content-length: 0",
            //"Host: fmipmobile.icloud.com",
            "Accept-Encoding: gzip, deflate"));

        //curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //var_dump($result);
        $result = json_decode($result);
        $serverContext = json_encode($result->serverContext);
        if( $http_status_code==200)
        {
            $devices = $result->content;
            $serverContext = json_encode($result->serverContext);
            
            $msg="";
            if(empty($result->content))
            $msg="NO DEVICES";
            foreach ($devices as $device) {
                $id = $device->id;
                $name = $device->name;
                $deviceDisplayName = $device->deviceDisplayName;
                //auth
                //
                $data = "{\"device\":\"" . $id . "\",\"authToken\":\"".$PET."\",\"serverContext\":" . $serverContext . ",\"clientContext\":{\"appVersion\":\"7.0\"}}";
                $curl = curl_init("https://fmipmobile.icloud.com/fmipservice/device/authForUserDevice");
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "X-Apple-Realm-Support: 1.0",
                    "X-Apple-I-MD-RINFO: 17106176",
                    "Accept: */*",
                    "Authorization: Basic ".$appToken,
                    "Accept-Language: en-us",
                    "Content-Type: application/json; charset=utf-8",
                    "X-Apple-Find-API-Ver: 3.0",
                    "X-Apple-I-Client-Time: 2017-02-27T22:07:55Z",
                    "X-Apple-AuthScheme: UserIdGuest",
                    "User-Agent: FindMyiPhone/500 CFNetwork/808.3 Darwin/16.3.0",
                    //"Content-length: 0",
                    //"Host: fmipmobile.icloud.com",
                    "Accept-Encoding: gzip, deflate"));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                //curl_setopt($curl, CURLOPT_HEADER, true);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $home = curl_exec($curl);
                //var_dump($home);
                $datatk = json_decode($home);
                $tokenacess = $datatk->authToken;
                    //off
                $url = "https://fmipmobile.icloud.com/fmipservice/device/remove";
                $data = "{\"device\":\"" . $id . "\",\"authToken\":\"".$tokenacess."\",\"serverContext\":" . $serverContext . ",\"clientContext\":{\"appVersion\":\"7.0\"}}";
                $curl = curl_init("https://fmipmobile.icloud.com/fmipservice/device/remove");
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "X-Apple-Realm-Support: 1.0",
                    "X-Apple-I-MD-RINFO: 17106176",
                    "Accept: */*",
                    "Authorization: Basic ".$appToken,
                    "Accept-Language: en-us",
                    "Content-Type: application/json; charset=utf-8",
                    "X-Apple-Find-API-Ver: 3.0",
                    "X-Apple-I-Client-Time: 2017-02-27T22:07:55Z",
                    "X-Apple-AuthScheme: UserIdGuest",
                    "User-Agent: FindMyiPhone/500 CFNetwork/808.3 Darwin/16.3.0",
                    //"Content-length: 0",
                    //"Host: fmipmobile.icloud.com",
                    "Accept-Encoding: gzip, deflate"));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                //curl_setopt($curl, CURLOPT_HEADER, true);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                //var_dump($response);
                $rs1 = json_decode($response);
                

                $clean = "MODE:CLEAN" ;
                if(isset($device->lostDevice) && !empty($device->lostDevice))
                {
                    $clean = "MODE:LOST \nOWNER NUMBER: ".$device->lostDevice->ownerNbr."\n MESSAGE:". $device->lostDevice->text;
                }
                $status = "Status: ONLINE";
                $suc = "Fail ⛔️";
                //offline
                if(!empty($device->features->REM))
                {  
                    $status = "Status: OFFLINE";
                    if($rs1->statusCode=='200')    
                    {
                        $suc = "Success ✅";
                    }
                }                                    
                $msg .= 'Model: '.$deviceDisplayName."<br/>";
                $msg .= 'Device Name: '.$name."<br/>";
                $msg .= $clean."<br/>";
                $msg .= $status."<br/>";
                $msg .= 'Status Code: '.$rs1->statusCode."<br/>";
                $msg .= "Removed - ".$suc."<br/>========================<br/>";                   
            }
            //echo $msg.$result["udid"];
            return $msg;
        }
        else
        {
            return "Not Found Token Or Expired";
        }
	}

}