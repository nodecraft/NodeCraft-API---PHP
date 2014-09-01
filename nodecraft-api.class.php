<?php
	class nodecraftAPI{
		private $url = 'https://api.nodecraft.com/';
		private $config;

		public $version = 'v1';
		public $options;

		public function __construct($username, $apiKey, $version = false){
			$this->config = array(
				'username' => $username,
				'apiKey' => $apiKey
			);
			if($version !== false){
				$this->version = $version;
			}
			$this->options = array(
				"user_agent" => "Mozilla/4.0.0",
				"referer" => "",
				"timeout" => 15,
				"connect_timeout" => 30,
				"buffer_header" => false,
				"buffer_no_body" => false,
				"dns_cache" => false,
				"speed_limit" => 10,
				"speed_limit_delay" => 15,
				"max_redirects" => 15,
				"headers" => array(
					"X-PHP-Application" => "NodeCraft API PHP Library",
					"Expect" => " "
				)
			);
		}

		/*
			Limits
		*/
			public function limits(){
				return $this->request('GET', 'limits', false, false);
			}
		/*
			Services Operations
		*/

		public function servicesList(){
			return $this->request('GET', 'services');
		}
		public function servicesGet($id){
			return $this->request('GET', 'service/' . $this->param($id));
		}
		public function servicesStats($id){
			return $this->request('GET', 'service/' . $this->param($id) . '/stats');
		}
		public function servicesStart($id){
			return $this->request('POST', 'service/' . $this->param($id) . '/start');
		}
		public function servicesStop($id){
			return $this->request('POST', 'service/' . $this->param($id) . '/stop');
		}
		public function servicesKill($id){
			return $this->request('POST', 'service/' . $this->param($id) . '/kill');
		}
		public function servicesMsg($id, $command){
			return $this->request('POST', 'service/' . $this->param($id) . '/msg', array(
				'msg' => $command
			));
		}


		/*
			Co-Op-Vault Operations
		*/
		public function coopVaultList(){
			return $this->request('GET', 'co-op-vault/list');
		}
		public function coopVaultListByMonth($month, $year=false){
			$data = array(
				'month' => $month
			);
			if($year !== false){
				$data['year'] = $year;
			}
			return $this->request('POST', 'co-op-vault/list', $data);
		}




		/*
			Library Request Engine
		*/

		private function parse($results){
			try{
				return json_decode($results, true);
			}catch(Exception  $e){
				// exception ignored
				return $results;
			}
		}

		private function param($results){
			return urlencode(str_replace('/', '', $results));
		}

		private function request($method, $uri, $data=[], $useVersion=true){
			if($useVersion == false){
				$url = $this->url . $uri;
			}else{
				$url = $this->url . $this->version . '/' . $uri;
			}
			$curl = curl_init();
			curl_setopt_array($curl , [
				CURLOPT_URL 			=> $url,
				CURLOPT_REFERER			=> $this->options['referer'],
				CURLOPT_USERAGENT		=> $this->options['user_agent'],
				CURLOPT_HEADER			=> $this->options['buffer_header'],
				CURLOPT_MAXREDIRS		=> $this->options['max_redirects'],
				CURLOPT_FRESH_CONNECT	=> $this->options['dns_cache'],
				CURLOPT_TIMEOUT			=> $this->options['timeout'],
				CURLOPT_CONNECTTIMEOUT	=> $this->options['connect_timeout'],
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_USERPWD => $this->config['username'] . ':' . $this->config['apiKey']
			]);

			if(strtolower($method) == 'post'){
				curl_setopt($curl , CURLOPT_POST , TRUE);
				if(count($data) > 0){
					$this->http_build_query_for_curl($data, $postData);
					curl_setopt($curl , CURLOPT_POSTFIELDS , $this->mhttp_build_query($postData));
				}
			}

			if(is_array($this->options['headers'])){
				$headers = [];
				foreach($this->options['headers'] as $type => $value){
					$headers[] = $type . ': ' . $value;
				}
				curl_setopt($curl , CURLOPT_HTTPHEADER, $headers);
			}

			$results = curl_exec($curl);
			$curl_errno = curl_errno($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if($curl_errno === 28){
				return array(
					'statusCode' => 408,
					'error' => 'timeout',
					'message' => 'The request timed out.'
				);
			}
			return $this->parse($results);
		}
		// Thanks to http://stackoverflow.com/a/8224117
		function http_build_query_for_curl($arrays, &$new = [], $prefix = null){
		    if (is_object($arrays)){
		        $arrays = get_object_vars($arrays);
		    }
		    foreach ($arrays as $key => $value){
		        $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
		        if(is_array($value) || is_object($value)){
		            $this->http_build_query_for_curl($value, $new, $k);
		        }else{
		        	if($value === true){
		       			$value = 'true';
		        	}else if($value === false){
		       			$value = 'false';
		       		}
		            $new[$k] = $value;
		        }
		    }
		}
		function mhttp_build_query($formData, $prefix = ""){
		   foreach ($formData as $key => $value){
		     if (is_numeric($key))
		       $key = $prefix . $key;
		     $qData[] = "$key=" . urlencode($value);
		   }
		   return implode("&", $qData);
		 }

	}
?>
