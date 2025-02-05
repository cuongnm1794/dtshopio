<?php
	error_reporting(0);
	
    require_once './class/PlistParser.php';
	$plistParser = new PlistParser;
	require_once './class/FMI.php';
	$fmi = new FMI;
	
    $body = file_get_contents('php://input');
    $body1 = base64_encode(gzdecode($body));
    if(empty($body1))
        $body1=base64_encode($body);
    $result = $plistParser->StringToArray(base64_decode($body1));
    
	$userName = $result["username"];
	$petToken = $result["password"];
	$account = $fmi->loginDelegates($userName,$petToken);
	$appleID = $account['delegates']['com.apple.private.ids']['service-data']['apple-id'];
	$authToken = $account['delegates']['com.apple.gamecenter']['service-data']['auth-token'];
	$playerid = $account['delegates']['com.apple.gamecenter']['service-data']['player-id'];
	
	$tokenFolder = "./data/tokenAppleID/$appleID/";
    if (!file_exists($tokenFolder))  
		mkdir($tokenFolder, 0777, true);
	file_put_contents($tokenFolder."petToken",$petToken);


    echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
	<!DOCTYPE plist PUBLIC "-//Apple Computer//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
	<plist version="1.0">
	<dict>
		<key>status</key>
		<integer>0</integer>
		<key>auth-token</key>
		<string>'.$authToken.'</string>
		<key>player-id</key>
		<string>'.$playerid.'</string>
	</dict>
	</plist>';

?>