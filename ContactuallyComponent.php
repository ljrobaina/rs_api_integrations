<?php
require 'ContactuallyApi.php';
/**
 * Contactually Component
 */
class ContactuallyComponent extends Component {
	var $contactuallyAPI;
	
	function __construct(ComponentCollection $collection, $settings = array()){
		$this->contactuallyapikey = Configure::read('Site.ContactuallyAPI');
		$this->contactuallyAPI    = new ContactuallyAPI(trim(Configure::read('Site.ContactuallyAPI')));
	}
	
	/**
	 * Send Contact to Contactually Vendor to send the API request
	 * @param  array  $leaddata [Lead data to send to the Contactually app]
	 * @return [type]           [description]
	 */
	function sendContact($leaddata=array()){
		// Send lead information
		$lead = array(
			'first_name'      => $leaddata['Lead']['first_name'],
			'last_name'       => $leaddata['Lead']['last_name'],
			'company'         => '',
			'title'           => '',
			'addresses'       => array( array(
									'label'    => 'Home', 
									'street_1' => $leaddata['Lead']['street'].' '.$leaddata['Lead']['address'], 
									'city'     => $leaddata['Lead']['city'], 
									'state'    => $leaddata['Lead']['state'], 
									'zip'      => $leaddata['Lead']['zipcode'], 
									'country'  => Configure::read('Site.countryCode'),
									)),
			'email_addresses' => array( array(
									'label'   => '', 
									'address' => !empty($leaddata['Lead']['email']) ? $leaddata['Lead']['email'] : '', 
									)),
			'phone_numbers'   => array( array(
									'label'  => '', 
									'number' => !empty($leaddata['Lead']['phone']) ? $leaddata['Lead']['phone'] : '', 
									)),
			'custom_field_values'   => array($leaddata['Lead']),
		);

		$bucket_id = Configure::read('Site.ContactuallyBucket');
		$responseUrl = $this->contactuallyAPI->createContact($lead, $bucket_id);
		
		// If the contact was added in the app save logs in ContactuallyLead table
		if($responseUrl['data']['id']){
			$conactid = $responseUrl['data']['id'];
			
			if($conactid){
				$arrContactuallyLead = array();
				$arrContactuallyLead['ContactuallyLead']['lead_id'] = $leaddata['Lead']['id'];
				$arrContactuallyLead['ContactuallyLead']['contactually_id'] = $conactid;
				// Create log
				ClassRegistry::init('ContactuallyLead')->create();
				ClassRegistry::init('ContactuallyLead')->save($arrContactuallyLead);
			}
		}
	}
	/**
	 * Extract the contact id from the API response
	 * @param  [type] $url [API response]
	 * @return [type]      [description]
	 */
	function getContactId($url){
		preg_match_all('/([\d]+)/', $url, $match);
		if(!empty($match[0][1])){
			return $match[0][1];
		}else{
			return false;
		}
		
	}

	/**
	 * Create bucket in the Contactually app to organice the leads from PSL site.
	 * @return [type] [description]
	 */
	public function createBucket() 
	{
		$responseUrl = $this->contactuallyAPI->createBucket();

		if (!empty($responseUrl['data']['id'])) {
			
			return $responseUrl['data']['id'];
		}
		else return false;
	}
}
