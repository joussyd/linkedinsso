<?php namespace cmi\linkedinsso;

require('config/linkedin-config.php');

/**
 * LinkedInSignIn class for Sign In on LinkedIn using cURL
 *
 * 
 */
class LinkedInSignIn
{

	/**
     * Generate the LinkedIn Login Url
     *
     *
     * @return string
     */
	function GenerateLoginUrl()
	{
		// append app credentials to generate linkedin login url
		$login_url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id='. CLIENT_ID .'&redirect_uri='. CLIENT_REDIRECT_URL .'&state=CSRF';

		//return login url
		return $login_url;
	}

	/**
     * Get User's access token
     *
     * @param string $code     The linkedin authorization code
     * @return string
     */
	public function GetAccessToken($code) {	
		$url = 'https://www.linkedin.com/uas/oauth2/accessToken';			
		
		//request for access token
		$curlPost = 'client_id=' . CLIENT_ID . '&redirect_uri=' . CLIENT_REDIRECT_URL . '&client_secret=' .CLIENT_SECRET . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);

		// send request then decode the returned json string	
		$data = json_decode(curl_exec($ch), true);

		// get request's return code
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);	

		// check if the return code is OK	
		if($http_code != 200) 
			throw new Exception('Error : Failed to receieve access token');	

		// return access token
		return $data;	
	}

	/**
     * Get User's Basic Profile iInformation
     *
     * @param string $access_token     The User's access token
     * @return array
     */
	public function GetUserProfileInfo($access_token) {	

		//linkedin api with scope
		$url = 'https://api.linkedin.com/v1/people/~:(id,num-connections,picture-url,email-address,first-name,last-name,picture-urls::(original))?format=json';		
		// request for user's basic profile info using the provided access token
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));
		
		// send request then decode the returned json string
		$data = json_decode(curl_exec($ch), true);

		//get the request's return code
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		

		//check if return is OK
		if($http_code != 200) 
			throw new Exception('Error : Failed to get user information');
			
		// return user profile info
		return $data;
	}
}