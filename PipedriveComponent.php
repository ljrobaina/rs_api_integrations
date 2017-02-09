<?php

require 'PipedriveApi.php';
/**
 * Pipedrive Component
 */
class PipedriveComponent {
	
	function __construct(ComponentCollection $collection, $settings = array()){
		$this->pipedriveapikey = ' '; // Specifie the API key.
		$this->pipedriveAPI    = new PipedriveAPI($this->pipedriveapikey);
	}
	
	/**
	 * Send Lead information to Pipedrive Vendor to send the API request
	 * @param  array  $leaddata [Lead data to send to the Pipedrive app]
	 * @return [type]           [description]
	 */
	function sendLead($leaddata=array(), $LeadId){

		// Get next user to assign to a person and a deal.
		$next_user = $this->getUser();

		// Send lead information
		$lead = array(
			'name'     => $leaddata['Lead']['first_name'].' '.$leaddata['Lead']['last_name'],
			'8782cebb0202c9b2187750aa502cad791f8cb735' => "1",
			'657a71eaddc20063f71b291e168bd1a3e203dd82' => !empty($leaddata['Lead']['fromsite']) ? $leaddata['Lead']['fromsite'] : '',
			'org_id'   => 0,
			'email'    => !empty($leaddata['Lead']['email']) ? $leaddata['Lead']['email'] : '',
			'phone'    => !empty($leaddata['Lead']['phone']) ? $leaddata['Lead']['phone'] : '',
			'add_time' => date('Y-m-d H:i:s'),
			'owner_id' => $next_user['RoundRobinPipedrive']['user_id'],
		);
		$responseUrl = $this->pipedriveAPI->addPerson($lead);

		// If the Person was created create a deal asociated to this person
		if($responseUrl['data']['id']){
			$personid = $responseUrl['data']['id'];
			
			if($personid){
				$responseDeal = $this->createDeal($responseUrl['data']);
			}
		}
		
		// If the person and deal was added in the app save logs in PipedriveLead table
		if($responseUrl['data']['id'] && $responseDeal){
			$person_id = $responseUrl['data']['id'];
			$deal_id = $responseDeal;

			$arrPipedriveLead = array();
			$arrPipedriveLead['PipedriveLead']['lead_id'] = $LeadId;
			$arrPipedriveLead['PipedriveLead']['person_id'] = $person_id;
			$arrPipedriveLead['PipedriveLead']['deal_id'] = $deal_id;
			$arrPipedriveLead['PipedriveLead']['user_id'] = $responseUrl['data']['owner_id']['id'];

			// Create log
			ClassRegistry::init('PipedriveLead')->create();
			ClassRegistry::init('PipedriveLead')->save($arrPipedriveLead);
		}
	}

	/**
	 * Create deal in the Pipedrive asociated a person.
	 * @return [type] [description]
	 */
	public function createDeal($person_data) 
	{
		// Send deal information
		$deal = array(
			'title'      => $person_data['name'].' deal',
			'value'      => 199,
			'person_id'  => $person_data['id'],
			'add_time'   => date('Y-m-d H:i:s'),
			'user_id'    => $person_data['owner_id']['id'],
		);

		$responseUrl = $this->pipedriveAPI->addDeal($deal);

		if (!empty($responseUrl['data']['id'])) {
			
			return $responseUrl['data']['id'];
		}
		else return false;
	}

	/**
	 * Round Robin to assign users to persons
	 * @return [type] [description]
	 */
	public function getUser() 
	{
		$this->PipedriveLead = ClassRegistry::init('PipedriveLead');
		$this->rrPipeDrive = ClassRegistry::init('RoundRobinPipedrive');

		// Get the last user id assigned to a person
		$last_assignee = $this->PipedriveLead->find('first',array('order'=>'id DESC'));

		$last_assignee_id = $last_assignee['PipedriveLead']['user_id'];

		// Get the last user order assigned
		$last_user = $this->rrPipeDrive->find('first', array('conditions' => array('user_id' => $last_assignee_id)));

		$last_user_order = $last_user['RoundRobinPipedrive']['order'];

		// The next user to assign will be the last user with order + 1
		$next_assigne = $this->rrPipeDrive->find('first', array('conditions' => array('order' => $last_user_order+1)));

		// If does't exist an user with this order is beceuse is the last one
		// Next one will be the user with order 1.
		if (empty($next_assigne)) {
			
			$next_assigne = $this->rrPipeDrive->find('first', array('conditions' => array('order' => 1)));
		}

		return $next_assigne;
	}
}
