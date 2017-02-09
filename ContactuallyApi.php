<?php 

/**
* Contactually API v2 Integration - PSL
*/
class ContactuallyApi
{
	var $url = 'https://api.contactually.com/v2';
	
	function __construct($apiKey)
	{
		$this->ApiKey = $apiKey;
	}

    /**
     * Send lead information to Contactually
     * @param  [array] $lead   [Lead information]
     * @return [array] $result [API response]
     */
	public function createContact($lead, $bucket_id)
	{
    	$path = "/contacts";

    	$data['data'] = $lead; debug($data);

    	$result = $this->post($data, $path);

    	debug($result);

    	// Add a contact in to a bucket
    	if ($this->response_code == 201 && !empty($result['data'])) {
    		
    		$result_bucket_contact = $this->createBucketContact($result['data']['id'], $bucket_id);
    	}

    	debug($result_bucket_contact);

    	return $result;
	}

	/**
     * Create a bucket to organize PSL leads in Contactuially.
     * @return [array] [return Bucket data]
     */
    public function createBucket()
    {
		$path = "/buckets";
		
		$bucket['data'] = array('name' => 'Prime Seller Leads' , 'goal' => 'PSL leads', 'reminder_interval' => 30);
		
		$result = $this->post($bucket, $path);

		return $result;
	}

	/**
     * Send lead information to Contactually
     * @param  [array] $lead   [Lead information]
     * @return [array] $result [API response]
     */
	public function createBucketContact($contact_id, $bucket_id)
	{
    	$path = "/buckets/".$bucket_id."/contacts";

    	$data['data'] = array(array('id' => $contact_id));

    	$result = $this->post($data, $path);

    	debug($result);

    	return $result;
	}

	
    //======= API Conection Methods ============//


	/**
	 * Retrieving a single instance or list of resources
	 * @param  string $uri    [Api method]
	 * @param  array  $params [Lead information]
	 */
	public function get($params = array(), $path)
    {
        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url.$path,
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
            CURLOPT_URL => $this->url.$path,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($params),
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
        $curl_params[CURLOPT_HTTPHEADER] = array('Content-Type: application/json', 'Authorization: Bearer '.$this->ApiKey.'');

        foreach($curl_params as $option => $value) {
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