<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Namespaces
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\libraries\WebSocket\Server;

/**
 * @package   CodeIgniter Ratchet WebSocket Library: Main class
 * @category  Libraries
 * @author    Taki Elias <taki.elias@gmail.com>
 * @license   http://opensource.org/licenses/MIT > MIT License
 * @link      https://github.com/takielias
 *
 * CodeIgniter WebSocket library. It allows you to make powerfull realtime applications by using Ratchet Websocket technology
 */

/**
 * Inspired By
 * Ratchet Websocket Library: helper file
 * @author Romain GALLIEN <romaingallien.rg@gmail.com>
 */

#[\AllowDynamicProperties]
class Websocket
{
	/**
	 * CI Super Instance
	 * @var array
	 */
	private $CI;

	/**
	 * Default host var
	 * @var string
	 */
	public $host = null;

	/**
	 * Default host var
	 * @var string
	 */
	public $port = null;

	/**
	 * Default auth var
	 * @var bool
	 */
	public $auth = false;

	/**
	 * Default Timer Interval var
	 * @var bool
	 */
	public $timer_interval = 1;

	/**
	 * Default debug var
	 * @var bool
	 */
	public $debug = false;

	/**
	 * Auth callback informations
	 * @var array
	 */
	public $callback = array();

	/**
	 * Config vars
	 * @var array
	 */
	protected $config = array();

	/**
	 * Define allowed callbacks
	 * @var array
	 */
	protected $callback_type = array('auth', 'event', 'close', 'citimer', 'roomjoin', 'roomleave', 'roomchat');

	/**
	 * Class Constructor
	 * @method __construct
	 * @param array $config Configuration
	 * @return void
	 */
	public function __construct(array $config = array())
	{
		// Load the CI instance
		$this->CI = &get_instance();

		// Load the class helper
		$this->CI->load->helper('custom_websocket_helper');

		// Define the config vars
		$this->config = (!empty($config)) ? $config : array();

		// Config file verification
		if (empty($this->config)) {
			output('fatal', 'The configuration file does not exist');
		}

		// Assign HOST value to class var
		$this->host = (!empty($this->config['websocket']['host'])) ? $this->config['websocket']['host'] : '';

		// Assign PORT value to class var
		$this->port = (!empty($this->config['websocket']['port'])) ? $this->config['websocket']['port'] : '';

		// Assign AUTH value to class var
		$this->auth = (!empty($this->config['websocket']['auth'] && $this->config['websocket']['auth'])) ? true : false;

		// Assign DEBUG value to class var
		$this->debug = (!empty($this->config['websocket']['debug'] && $this->config['websocket']['debug'])) ? true : false;

		// Assign Timer value to class var
		$this->timer = (!empty($this->config['websocket']['timer_enabled'] && $this->config['websocket']['timer_enabled'])) ? true : false;

		// Assign Timer Interval value to class var
		$this->timer_interval = (!empty($this->config['websocket']['timer_interval'])) ? $this->config['websocket']['timer_interval'] : 1;
	}

	/**
	 * Launch the server
	 * @method run
	 * @return string
	 */
	public function run()
	{
		// Initiliaze all the necessary class
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Server()
				)
			),
			$this->port,
			$this->host
		);

		//If you want to use timer
		if ($this->timer != false) {
			$server->loop->addPeriodicTimer($this->timer_interval, function () {
				if (!empty($this->callback['citimer'])) {
					call_user_func_array($this->callback['citimer'], array(date('d-m-Y h:i:s a', time())));
				}
			});
		}

		// Run the socket connection !
		$server->run();
	}

	/**
	 * Define a callback to use auth or event callback
	 * @method set_callback
	 * @param array $callback
	 * @return void
	 */
	public function set_callback($type = null, array $callback = array())
	{
		// Check if we have an authorized callback given
		if (!empty($type) && in_array($type, $this->callback_type)) {

			// Verify if the method does really exists
			if (is_callable($callback)) {

				// Register callback as class var
				$this->callback[$type] = $callback;
			} else {
				output('fatal', 'Method ' . $callback[1] . ' is not defined');
			}
		}
	}
}
