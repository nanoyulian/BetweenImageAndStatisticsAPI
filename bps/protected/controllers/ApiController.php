<?php 

class ApiController extends Controller
{
	 // Members
	 /**
	  * Key which has to be in HTTP USERNAME and PASSWORD headers 
	  */
	 Const APPLICATION_ID = 'ASCCPE';
	 
	 /**
	  * Default response format
	  * either 'json' or 'xml'
	  */
	 private $format = 'json';
	 /**
	  * @return array action filters
	  */
	 public function filters()
	 {
	    return array();
	 }

 	private function _getStatusCodeMessage($status)
	{
	    // these could be stored in a .ini file and loaded
	    // via parse_ini_file()... however, this will suffice
	    // for an example
	    $codes = Array(
		200 => 'OK',
		400 => 'Bad Request',
		401 => 'Bad Request',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
	    );
	    return (isset($codes[$status])) ? $codes[$status] : '';
	}

	private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
	    // set the status
	    $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
	    header($status_header);
	    // and the content type
	    header('Content-type: ' . $content_type);
	 
	    // pages with body are easy
	    if($body != '')
	    {
			// send the body
			echo $body;
	    }
	    // we need to create the body if none is passed
	    else
	    {
			// create some body messages
			$message = '';
		 
			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch($status)
			{
				 case 400:
			        $message = 'Parameter url tidak ada, minimal ada 1 parameter (url)';
			        break;
			    case 401:
			        $message = 'Parameter url tidak valid atau kosong';
			        break;
			    case 404:
			        $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
			        break;
			    case 500:
			        $message = 'Server Error, Pastikan Image URL anda valid';
			        break;
			    case 501:
			        $message = 'The requested method is not implemented.';
			        break;
			}
		 
			// servers don't always have a signature turned on 
			// (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' 
					. $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
		 
			// this should be templated in a real-world solution
			$body = '
				<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
				<html>
				<head>
				    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
				    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
				</head>
				<body>
				    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
				    <p>' . $message . '</p>
				    <hr />
				    <address>' . $signature . '</address>
				</body>
				</html>';
		 
			echo $body;
	    }
	    Yii::app()->end();
	}


	private function _checkAuth()
	{
	    // Check if we have the USERNAME and PASSWORD HTTP headers set?
	    if(!(isset($_SERVER['HTTP_X_USERNAME']) and isset($_SERVER['HTTP_X_PASSWORD']))) {
			// Error: Unauthorized
			$this->_sendResponse(401);
	    }
	    $username = $_SERVER['HTTP_X_USERNAME'];
	    $password = $_SERVER['HTTP_X_PASSWORD'];
	    // Find the user
	    $user=User::model()->find('LOWER(username)=?',array(strtolower($username)));
	    if($user===null) {
			// Error: Unauthorized
			$this->_sendResponse(401, 'Error: User Name is invalid');
	    } else if(!$user->validatePassword($password)) {
			// Error: Unauthorized
			$this->_sendResponse(401, 'Error: User Password is invalid');
	    }
	}

	//Generate Token API Clarifai Setiap Transaksi
	private function _getToken() 
	{
		$url = "https://api.clarifai.com/v1/token/";
		$post_data = array(
			'client_id' => 'p6if27bhOARsjFvxYz532djoECykmSTrD5mZrXQg',
			'client_secret' => 'P7aC10uofh6z5sdvVE19sT4HwzkLnce-PoBLiZ_n',
			'grant_type'=>'client_credentials'
		    );
		//print_r($url);
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		$token = json_decode($result); //mengembalikan objek dari json
		//print_r($token);
		return $token->access_token; //akses variabel dalam objek json
	
	}
	
	/*
		Translate Text To Indonesian with Yandex API
		return array of indonesian_tags
	 */
	private function _translateTags($tags) 
	{
		$tags_id = array_slice($tags, 0, 3); 
		//print_r($tags_id);
		$url = "https://translate.yandex.net/api/v1.5/tr.json/translate ? key=trnsl.1.1.20161116T144704Z.46ade8d9c0b719ff.bd1c76a532959c5a0749fe6c7bf054c1b05ef32c";
		
		// build a full statement for API request
		$url_text = "&text=";
		foreach($tags_id as $key=>$val) {
			$url_text = $url_text.$val.",";
		}
		$url_text = rtrim($url_text, ",");
		$url_text = $url_text."&lang=en-id";		
		$url = $url.$url_text;
		$url = preg_replace('/\s+/', '', $url);
		//print_r($url);
		// Request result with API Yandex
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);	
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $header_data); 
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		//mengembalikan objek response TAG json
		$obj_response = json_decode($result);  
		//print_r($obj_response->text);
		
		return $obj_response->text;
	}
	
	//ambil image tags via Clarifai API via GET
	private function _getImageTags($imgurl) 
	{
		$url = "https://api.clarifai.com/v1/tag/?url=".$imgurl;
		
		//cek dan ambil token yang valid
		$token = $this->_getToken();		
		//tambahkan token di url
		$url = $url."&access_token=".$token;
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);	
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		//mengembalikan objek response TAG json
		$obj_response = json_decode($result);  
		//kembalikan objek response	
		return $obj_response->results[0]->result->tag; 	
	}
	
	//ambil image tags via Clarifai API via GET
	private function _postImageTags($imgurl) 
	{
		$url = "https://api.clarifai.com/v1/tag/";
		$post_data = array(
			'encoded_data' => $imgurl		
	    );
	   //print_r($imgurl);
		//cek dan ambil token yang valid
		$token = $this->_getToken();		//print_r($token);
		
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);	
		// Set Header kirim token yg valid
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token ));
	    // SET POST DATA 
	   curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		//mengembalikan objek response TAG json
		$obj_response = json_decode($result);  
		//kembalikan objek response	
		return $obj_response->results[0]->result->tag; 	
		
	}
	
		
    // Actions REST GET/POST
	 public function actionGetUrlImage()
	 {
	 	
		// Check if id was submitted via GET
		if(!isset($_GET['url']))	{
			$this->_sendResponse(400);		
		} 
		else {
			
			//dapatkan url/path image
			$url = Yii::app()->request->getParam('url');					
			
			if ($url !== '') {
				
							//dapatkan objek tags dengan token yang valid
							$obj_response_tag = $this->_getImageTags($url);		
							$arr_id_tags=[];	
							
							if ( !empty($obj_response_tag->classes[0]) ) {
							
											//translate tags ke bahasa indonesia			
											$arr_id_tags = explode(',',$this->_translateTags($obj_response_tag->classes)[0]) ;
											
											//cek apakah ada array yg empty
											$curr_id_tags_count = count($arr_id_tags);
											$selisih = 3 - $curr_id_tags_count;			
											
											switch ($selisih) {
												    case 0:
												        break;
												    case 1:
												        $arr_id_tags[] = "kosong";
												        break;
												    case 2:
												        $arr_id_tags[] = "kosong";
												        $arr_id_tags[] = "kosong";
												        break;
												   
											} 
											//print_r($arr_id_tags);
											
											// slice 2 saja probabilitynya
											$arr_id_probs = array_slice($obj_response_tag->probs, 0, 3); 
											
											// create associative array tags indonesia dgn probability
											// $arr_tags 	 = array_combine($arr_id_tags,$arr_id_probs);
											
											/*
												To DO : 
													1. Terjemahkan ke Indonesia OK
													2. Query Data dr Tabel BPS berdasarkan Tags !!!! the HARDEST PART OK 1 variabel 1 data
													3. Buat Format HTML DOM 
													4. Response JSON
													
													utk no.2 : 
													
													a. query dengan Like pada tabel _variabel berdasarkan tag yg dihasilkan
														SELECT * FROM  newtemplate._variabel WHERE ( LOWER(nama_variabel) LIKE '%penduduk%' AND id_kelompok_turunan_tahun = '0')
															UNION 
														SELECT * FROM  newtemplate._variabel WHERE (LOWER(nama_variabel) LIKE '%perempuan' AND id_kelompok_turunan_tahun = '0')
														ORDER BY id_variabel ASC;
														
													b. query (tahun N - 2) utk mendapatkan id_tahun,dan simpan dalam variabel tahun
													b. simpan dalam array id_variabel
													c. jika id_variabel > 0 dan untuk setiap id_variabel : 
														- 1
																SELECT 
																  _variabel.nama_variabel, _item_vertical_variabel.nama_item_vertical_variabel, _data.data_content, _satuan.nama_satuan
																FROM 
																  newtemplate._data
																INNER JOIN  newtemplate._variabel ON  _data.id_variabel = _variabel.id_variabel
																INNER JOIN  newtemplate._item_vertical_variabel ON _data.id_wilayah = _item_vertical_variabel.id_item_vertical_variabel
																INNER JOIN  newtemplate._satuan ON _variabel.id_satuan = _satuan.id_satuan
																WHERE 
																  _data.id_tahun = 110  AND
																  _data.id_variabel = 36 ;
														- 2 Tambahkan Hasil query dalam Array hasil
														
													 
											*/
											$connection=Yii::app()->db;   // assuming you have configured a "db" connection
											$tag0 = trim($arr_id_tags[0]);
											$tag1 = trim($arr_id_tags[1]);
											$tag2 = trim($arr_id_tags[2]);
											
											//print_r($tag0); 
											//print_r($tag1);
											//print_r($tag2);
											
											// Mendapatkan id_variabel2x utk disimpan dalam array
											$sql="SELECT id_variabel FROM  newtemplate._variabel 
													WHERE ( LOWER(nama_variabel) LIKE  '%".$tag0."%' OR LOWER(nama_variabel) LIKE '%".$tag1."%' OR LOWER(nama_variabel) LIKE '%".$tag2."%') 
													AND id_kelompok_turunan_tahun = '0' AND id_kelompok_turunan_variabel = '0'	
													ORDER BY id_variabel ASC";				
														
											$command=$connection->createCommand($sql);
											// replace the placeholder ":username" with the actual username value
											//$command->bindParam(":tag1",$tag0);
											//$command->bindParam(":tag2","%$arr_id_tags[1]%",PDO::PARAM_STR);
											$id_var_arr =$command->queryAll();
											//print_r($id_var_arr);
											
											// Default data tahun adalah 2 (N-2), kecuali jika user ada set parameter tahun misal (N-1) 
											$pengurang_year = 2;
											if(isset($_GET['year'])) {
												$pengurang_year = Yii::app()->request->getParam('year');
											}				
											$year = date('Y') - $pengurang_year; 
													
											$sql="SELECT id_tahun FROM newtemplate._tahun WHERE nama_tahun = :year";			
											$command=$connection->createCommand($sql);
											$command->bindParam(":year",$year,PDO::PARAM_STR);
											$year_id =$command->queryScalar();
											
											//cek jumlah id_variabel berkaitan jumlah tabel yang akan dikembalikan ke user
											$num_var = count($id_var_arr);
											//print_r($num_var);
											//print_r($year_id);
											$tabel_arr = []; // untuk nyimpan hasil query dr tiap variabel
											$html_tabel= ""; // untuk nyimpan hasil tabel buat dikirim ke user dalam html
																		
											if(count($num_var) > 0) {
												for($i = 0; $i < $num_var; $i++) {
													$sql = "SELECT 
																  _variabel.nama_variabel, _item_vertical_variabel.nama_item_vertical_variabel, _data.data_content, _satuan.nama_satuan
																FROM 
																  newtemplate._data
																INNER JOIN  newtemplate._variabel ON  _data.id_variabel = _variabel.id_variabel
																INNER JOIN  newtemplate._item_vertical_variabel ON _data.id_wilayah = _item_vertical_variabel.id_item_vertical_variabel
																INNER JOIN  newtemplate._satuan ON _variabel.id_satuan = _satuan.id_satuan
																WHERE 
																  _data.id_tahun = ".$year_id."  AND  _data.id_variabel = ".$id_var_arr[$i]['id_variabel'];
													$command=$connection->createCommand($sql);
													$tabel_arr[$i] = $command->queryAll(); // simpan tabel query variabel di setiap index array
														
												}				
											}
										   
											//print_r($tabel_arr);
											/*
													        (judul)
													--------------------------------------------------------------
													--                  variabel utama (satuan)
													--------------------------------------------------------------
													-- nama_item_vertical_variabel[0]       -      data_content[0]
													--------------------------------------------------------------
													-- nama_item_vertical_variabel[1]       -      data_content[1]
													--------------------------------------------------------------
											
											<table class='table table-striped table-condensed table-bordered'>
													<thead>
															<tr> <th>Judul</th> </tr>
													</thead>
													<tbody>
															<tr> <td>nama_item_vertical_variabel[0]</td> <td>data_content[0]</td> </tr>
															<tr> <td>nama_item_vertical_variabel[1]</td> <td>data_content[1]</td> </tr>
													</tbody>
											</table>
								
											*/
											
											// Konversi tabel dimasing2x tabel_arr ke dalam format tabel html dan simpan dalam variabel htmldata untuk dikirim
											foreach($tabel_arr as $idx_tabel => $tabel) {
												if(!empty($tabel)) {
													$html_tabel_row = "";
													$judul = "";
													$satuan = "";
													foreach ($tabel as $data) {
														
														$html_tabel_row = 	$html_tabel_row."<tr><td>".$data['nama_item_vertical_variabel']."</td><td>".$data['data_content']."</td></tr>";			
														$judul = $data['nama_variabel'];	
														$satuan = $data['nama_satuan'];
																
													}
													$html_tabel_temp =  "<table class='table table-striped table-condensed table-bordered'>
																				<thead><tr> <th colspan='2'>".$judul." (Tahun ".$year.")</th> </tr></thead><tbody>
																				<tr><td>Variabel</td><td>Satuan (".$satuan.")</td></tr>
																				<tr><td>(1)</td><td>(2)</td></tr>										
																			  ".$html_tabel_row.
																			  "</tbody></table>";		
													// add html tabel ke array
													$html_tabel = $html_tabel."<br>".$html_tabel_temp."<br>";
												}
											
											}
											if(trim($html_tabel) == "") {
													$html_tabel= "<table class='table table-striped table-condensed table-bordered'>
																			<thead>
																			<tr><th>Mohon Maaf Variabel(tag) Belum ada di Database</th></tr>
																			</thead>
																			<tbody>
																			<tr><td>Data Kosong</td></tr>
																			</tbody>
																		</table>";
											}
											//print_r($html_tabel);
											
											
											$response = array(
														"url"=>$url, 
														"htmldata"=>$html_tabel, 
														//"tags" => $obj_response_tag->classes,
														"tags" => $arr_id_tags,
														"probs" =>$arr_id_probs,
														//"tags_probs" => $arr_tags,
														"sumber"=>"Badan Pusat Statistik-RI", 										
														);
											
											$this->_sendResponse(200, CJSON::encode($response,JSON_UNESCAPED_SLASHES));
											
						}  else {// obj response tag tidak kosong		
							
								$this->_sendResponse(500); //url kosong
						}					
				
				} else {
				
					$this->_sendResponse(401); //url kosong
			} 
			
		} // response !== 400
		
	 } // EOF image()
	 
	 
	 // Actions REST GET with post base 64 image
	 public function actionGetBaseImage()
	 {
		// Check if id was submitted via POST
		if(!isset($_POST['url']))	{		
			echo $this->_sendResponse(400 );		
			//echo  json_encode("kambing");
		}	
		else {
			
			//dapatkan url/path image
			$url = $_POST['url'];		//print_r($url);
			
			//dapatkan objek tags 
			$obj_response_tag = $this->_postImageTags($url);		//print_r($obj_response_tag);
			
			$arr_id_tags=[];
			//translate tags ke bahasa indonesia			
			$arr_id_tags = explode(',',$this->_translateTags($obj_response_tag->classes)[0]) ;
			
			//cek apakah ada array yg empty
			$curr_id_tags_count = count($arr_id_tags);
			$selisih = 3 - $curr_id_tags_count;			
			
			switch ($selisih) {
				    case 0:
				        break;
				    case 1:
				        $arr_id_tags[] = "kosong";
				        break;
				    case 2:
				        $arr_id_tags[] = "kosong";
				        $arr_id_tags[] = "kosong";
				        break;
				   
			} 
			//print_r($arr_id_tags);
			
			// slice 3 saja probabilitynya
			$arr_id_probs = array_slice($obj_response_tag->probs, 0, 3); 
			
	
			/*
					copy kan syntax dari GET diatas
					
					*****************************************************************************
			*/
			$connection=Yii::app()->db;   // assuming you have configured a "db" connection
			$tag0 = trim($arr_id_tags[0]);
			$tag1 = trim($arr_id_tags[1]);
			$tag2 = trim($arr_id_tags[2]);
			
			//print_r($tag0); 
			//print_r($tag1);
			//print_r($tag2);
			
			// Mendapatkan id_variabel2x utk disimpan dalam array
			$sql="SELECT id_variabel FROM  newtemplate._variabel 
					WHERE ( LOWER(nama_variabel) LIKE  '%".$tag0."%' OR LOWER(nama_variabel) LIKE '%".$tag1."%' OR LOWER(nama_variabel) LIKE '%".$tag2."%') 
					AND id_kelompok_turunan_tahun = '0' AND id_kelompok_turunan_variabel = '0'	
					ORDER BY id_variabel ASC";				
						
			$command=$connection->createCommand($sql);
			// replace the placeholder ":username" with the actual username value
			//$command->bindParam(":tag1",$tag0);
			//$command->bindParam(":tag2","%$arr_id_tags[1]%",PDO::PARAM_STR);
			$id_var_arr =$command->queryAll();
			//print_r($id_var_arr);
			
			// Default data tahun adalah 2 (N-2), kecuali jika user ada set parameter tahun misal (N-1) 
			$pengurang_year = 2;
			if(isset($_GET['year'])) {
				$pengurang_year = Yii::app()->request->getParam('year');
			}				
			$year = date('Y') - $pengurang_year; 
					
			$sql="SELECT id_tahun FROM newtemplate._tahun WHERE nama_tahun = :year";			
			$command=$connection->createCommand($sql);
			$command->bindParam(":year",$year,PDO::PARAM_STR);
			$year_id =$command->queryScalar();
			
			//cek jumlah id_variabel berkaitan jumlah tabel yang akan dikembalikan ke user
			$num_var = count($id_var_arr);
			//print_r($num_var);
			//print_r($year_id);
			$tabel_arr = []; // untuk nyimpan hasil query dr tiap variabel
			$html_tabel= ""; // untuk nyimpan hasil tabel buat dikirim ke user dalam html
										
			if(count($num_var) > 0) {
				for($i = 0; $i < $num_var; $i++) {
					$sql = "SELECT 
								  _variabel.nama_variabel, _item_vertical_variabel.nama_item_vertical_variabel, _data.data_content, _satuan.nama_satuan
								FROM 
								  newtemplate._data
								INNER JOIN  newtemplate._variabel ON  _data.id_variabel = _variabel.id_variabel
								INNER JOIN  newtemplate._item_vertical_variabel ON _data.id_wilayah = _item_vertical_variabel.id_item_vertical_variabel
								INNER JOIN  newtemplate._satuan ON _variabel.id_satuan = _satuan.id_satuan
								WHERE 
								  _data.id_tahun = ".$year_id."  AND  _data.id_variabel = ".$id_var_arr[$i]['id_variabel'];
					$command=$connection->createCommand($sql);
					$tabel_arr[$i] = $command->queryAll(); // simpan tabel query variabel di setiap index array
						
				}				
			}
		   
			// Konversi tabel dimasing2x tabel_arr ke dalam format tabel html dan simpan dalam variabel htmldata untuk dikirim
			foreach($tabel_arr as $idx_tabel => $tabel) {
				if(!empty($tabel)) {
					$html_tabel_row = "";
					$judul = "";
					$satuan = "";
					foreach ($tabel as $data) {
						
						$html_tabel_row = 	$html_tabel_row."<tr><td>".$data['nama_item_vertical_variabel']."</td><td>".$data['data_content']."</td></tr>";			
						$judul = $data['nama_variabel'];	
						$satuan = $data['nama_satuan'];
								
					}
					$html_tabel_temp =  "<table class='table table-striped table-condensed table-bordered'>
												<thead><tr> <th colspan='2'>".$judul." (Tahun ".$year.")</th> </tr></thead><tbody>
												<tr><td>Variabel</td><td>Satuan (".$satuan.")</td></tr>
												<tr><td>(1)</td><td>(2)</td></tr>										
											  ".$html_tabel_row.
											  "</tbody></table>";		
					// add html tabel ke array
					$html_tabel = $html_tabel."<br>".$html_tabel_temp."<br>";
				}
			
			}
			if(trim($html_tabel) == "") {
					$html_tabel= "<table class='table table-striped table-condensed table-bordered'>
											<thead>
											<tr><th>Mohon Maaf Variabel(tag) Belum ada di Database</th></tr>
											</thead>
											<tbody>
											<tr><td>Data Kosong</td></tr>
											</tbody>
										</table>";
			}
			//print_r($html_tabel);
			/* *********************************** SELESAI *********************************************/
			
			//$img64 = "<img src='data:image/png;base64,".$url." ' alt='Red dot' /> <br>";
			//$html_tabel = $img64.$html_tabel;
			$response = array(
						//"url"=>$url, 
						"htmldata"=>$html_tabel, 						
						"tags" => $arr_id_tags,
						"probs" =>$arr_id_probs,
						"sumber"=>"Badan Pusat Statistik-RI", 
						
						);
			//print_r($response);
			echo  json_encode($response);			
		}
	
	 } // EOF postlink()
   
  
}

?>
