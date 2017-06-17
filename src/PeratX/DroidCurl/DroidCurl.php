<?php

/**
 * DroidCurl
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PeratX
 * @link https://github.com/PeratX
 */

namespace PeratX\DroidCurl;

use iTXTech\SimpleFramework\Util\Curl;
use iTXTech\SimpleFramework\Util\Util;
use PurplePixie\PhpDns\DNSQuery;

class DroidCurl extends Curl{
	public static $dnsServer = "114.114.114.114";
	public static $dnsPort = 53;
	public static $udp = true;
	public static $timeout = 60;

	public function exec(){
		if(Util::getOS() == "android"){
			preg_match("#https?://(.*?)($|/)#m", $this->url, $domain);
			$query = new DNSQuery(self::$dnsServer, self::$dnsPort, self::$timeout, self::$udp, false, false);
			$domain = $domain[1];
			$result = $query->Query($domain, "A");
			if($query->hasError() or $result->count() == 0){
				$address = "127.0.0.1";
			}else{
				$address = $result->current()->getData();
			}
			$extraHeader = "Host: " . $domain;
			$header = curl_getinfo($this->curl, CURLOPT_HEADER);
			if(is_array($header)){
				$header[] = $extraHeader;
			}else{
				$header = [$extraHeader];
			}
			$this->setHeader($header);
			$this->setUrl(str_replace($domain, $address, $this->url));
		}
		return parent::exec();
	}
}