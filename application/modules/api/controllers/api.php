<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * @package	Rest Server
 * @author	Syahril Hermana
 */

class api extends REST_Controller {

	protected $header;
	protected $methods = [
		'index_put' => ['level' => 10, 'limit' => 10],
		'index_delete' => ['level' => 10],
		'level_post' => ['level' => 10],
		'regenerate_post' => ['level' => 10],
	];

	public function __construct()
	{
		parent::__construct();

		$this->header = apache_request_headers();
	
	}

	/** Sample JWT encode & decode */
	public function index()
	{
		$this->load->library("JWT");
		$CONSUMER_KEY = 'some-key';
		$CONSUMER_SECRET = 'some-secret';
		$CONSUMER_TTL = 86400;
		$jwt =  $this->jwt->encode(array(
			'consumerKey'=>$CONSUMER_KEY,
			'accountId'=>45,
			'issuedAt'=>date(DATE_ISO8601, strtotime("now")),
			'ttl'=>$CONSUMER_TTL
		), $CONSUMER_SECRET);
		
		echo json_encode($this->jwt->decode($jwt, $CONSUMER_SECRET));
	}

	public function data_get()
	{
		// If JWT authorization
		if(!isset($this->headers["Authorization"]) || empty($this->headers["Authorization"]))
		{
			//mejorar la validación, pero si está aquí es que no tenemos el token
			$this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
		}

		// Users from a data store e.g. database
		$users = [
			['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'fact' => 'Loves coding'],
			['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'fact' => 'Developed on CodeIgniter'],
		];

		$id = $this->get('id');

		// If the id parameter doesn't exist return all the users

		if ($id === NULL)
		{
			// Check if the users data store contains users (in case the database result returns NULL)
			if ($users)
			{
				// Set the response and exit
				$this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
			}
			else
			{
				// Set the response and exit
				$this->response([
					'status' => FALSE,
					'message' => 'No users were found'
				], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
			}
		}

		// Find and return a single record for a particular user.

		$id = (int) $id;

		// Validate the id.
		if ($id <= 0)
		{
			// Invalid id, set the response and exit.
			$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}

		// Get the user from the array, using the id as key for retreival.
		// Usually a model is to be used for this.

		$user = NULL;

		if (!empty($users))
		{
			foreach ($users as $key => $value)
			{
				if (isset($value['id']) && $value['id'] === $id)
				{
					$user = $value;
				}
			}
		}

		if (!empty($user))
		{
			$this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->set_response([
				'status' => FALSE,
				'message' => 'User could not be found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}

}
