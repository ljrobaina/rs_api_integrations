<?php 

/**
* Pipedrive API v1 Integration - PSL
*/
class PipedriveApi
{
	var $url = 'https://api.pipedrive.com/v1';
	
	function __construct($apiKey)
	{
		$this->api_token = $apiKey;
	}

    /**
     * Send lead information to Pipedrive
     * @param  [array] $lead   [Lead information]
     * @return [array] $result [API response]
     */
	public function addPerson($lead)
	{
    	$path = "/persons";

        debug($lead);

    	$result = $this->post($lead, $path);

    	debug($result);

    	return $result;
	}

	/**
     * Create a Deal asociated to a person.
     * @return [array] [return Deal data]
     */
    public function addDeal($deal)
    {
		$path = "/deals";
		
		$result = $this->post($deal, $path);

		debug($result);

        return $result;
	}

    /**
     * Get Users information.
     * @return [array] [return Users data]
     */
    public function getAllUsers()
    {
        $path = "/users";
        
        $result = $this->get($path);

        debug($result);

        return $result;
    }

	
    //======= API Conection Methods ============//


	/**
	 * Retrieving a single instance or list of resources
	 * @param  string $path    [Api method]
	 */
	public function get($path)
    {
        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url.$path."?api_token=".$this->api_token,
        );
        return $this->_execute($curl_opts);
    }

    /**
     * Creating a new resource
     * @param  string $uri    [Post url]
     * @param  array  $params [Post fields]
     * @return [type]         [description]
     */
    public function post($params = array(), $path)
    { 
        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url.$path."?api_token=".$this->api_token,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
        );
        return $this->_execute($curl_opts);
    }

    /**
     * Execute HTTP requests (Get, Post ...)
     * @param  array  $curl_params [CURL parameters to execute request]
     * @return array  $this->response_array [Returned dats from the API]
     */
    protected function _execute($curl_params = array())
    {
        // Open connection
        $connection = curl_init();

        foreach($curl_params as $option => $value) {
            debug($value);
            curl_setopt($connection, $option, $value);
        }
        // Execute request
		$response      = curl_exec($connection);
        $response_code = curl_getinfo($connection);
		$this->response_json  = $response;
		$this->response_array = json_decode($response, true);
		$this->response_code  = $response_code['http_code'];

        curl_close($connection);

        return $this->response_array;
    }
}