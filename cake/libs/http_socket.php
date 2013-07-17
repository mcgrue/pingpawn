<?php
/**
 * HTTP Socket connection class.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs
 * @since         CakePHP(tm) v 1.2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Core', array('CakeSocket', 'Set', 'Router'));

/**
 * Cake network socket connection class.
 *
 * Core base class for HTTP network communication. HttpSocket can be used as an
 * Object Oriented replacement for cURL in many places.
 *
 * @package       cake
 * @subpackage    cake.cake.libs
 */
class HttpSocket extends CakeSocket {

/**
 * Object description
 *
 * @var string
 * @access public
 */
	var $description = 'HTTP-based DataSource Interface';

/**
 * When one activates the $quirksMode by setting it to true, all checks meant to
 * enforce RFC 2616 (HTTP/1.1 specs).
 * will be disabled and additional measures to deal with non-standard responses will be enabled.
 *
 * @var boolean
 * @access public
 */
	var $quirksMode = false;

/**
 * The default values to use for a request
 *
 * @var array
 * @access public
 */
	var $request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'http',
			'host' => null,
			'port' => 80,
			'user' => null,
			'pass' => null,
			'path' => null,
			'query' => null,
			'fragment' => null
		),
		'auth' => array(
			'method' => 'Basic',
			'user' => null,
			'pass' => null
		),
		'version' => '1.1',
		'body' => '',
		'line' => null,
		'header' => array(
			'Connection' => 'close',
			'User-Agent' => 'CakePHP'
		),
		'raw' => null,
		'cookies' => array()
	);

/**
* The default structure for storing the response
*
* @var array
* @access public
*/
	var $response = array(
		'raw' => array(
			'status-line' => null,
			'header' => null,
			'body' => null,
			'response' => null
		),
		'status' => array(
			'http-version' => null,
			'code' => null,
			'reason-phrase' => null
		),
		'header' => array(),
		'body' => '',
		'cookies' => array()
	);

/**
 * Default configuration settings for the HttpSocket
 *
 * @var array
 * @access public
 */
	var $config = array(
		'persistent' => false,
		'host' => 'localhost',
		'protocol' => 'tcp',
		'port' => 80,
		'timeout' => 30,
		'request' => array(
			'uri' => array(
				'scheme' => 'http',
				'host' => 'localhost',
				'port' => 80
			),
			'auth' => array(
				'method' => 'Basic',
				'user' => null,
				'pass' => null
			),
			'cookies' => array()
		)
	);

/**
 * String that represents a line break.
 *
 * @var string
 * @access public
 */
	var $lineBreak = "\r\n";

/**
 * Build an HTTP Socket using the specified configuration.
 *
 * You can use a url string to set the url and use default configurations for
 * all other options:
 *
 * `$http =& new HttpSockect('http://cakephp.org/');`
 *
 * Or use an array to configure multiple options:
 *
 * {{{
 * $http =& new HttpSocket(array(
 *    'host' => 'cakephp.org',
 *    'timeout' => 20
 * ));
 * }}}
 *
 * See HttpSocket::$config for options that can be used.
 *
 * @param mixed $config Configuration information, either a string url or an array of options.
 * @access public
 */
	function __construct($config = array()) {
		if (is_string($config)) {
			$this->_configUri($config);
		} elseif (is_array($config)) {
			if (isset($config['request']['uri']) && is_string($config['request']['uri'])) {
				$this->_configUri($config['request']['uri']);
				unset($config['request']['uri']);
			}
			$this->config = Set::merge($this->config, $config);
		}
		parent::__construct($this->config);
	}

/**
 * Issue the specified request. HttpSocket::get() and HttpSocket::post() wrap this
 * method and provide a more granular interface.
 *
 * @param mixed $request Either an URI string, or an array defining host/uri
 * @return mixed false on error, request body on success
 * @access public
 */
	function request($request = array()) {
		$this->reset(false);

		if (is_string($request)) {
			$request = array('uri' => $request);
		} elseif (!is_array($request)) {
			return false;
		}

		if (!isset($request['uri'])) {
			$request['uri'] = null;
		}
		$uri = $this->_parseUri($request['uri']);

		if (!isset($uri['host'])) {
			$host = $this->config['host'];
		}
		if (isset($request['host'])) {
			$host = $request['host'];
			unset($request['host']);
		}

		$request['uri'] = $this->url($request['uri']);
		$request['uri'] = $this->_parseUri($request['uri'], true);
		$this->request = Set::merge($this->request, $this->config['request'], $request);

		$this->_configUri($this->request['uri']);

		if (isset($host)) {
			$this->config['host'] = $host;
		}
		$cookies = null;

		if (is_array($this->request['header'])) {
			$this->request['header'] = $this->_parseHeader($this->request['header']);
			if (!empty($this->request['cookies'])) {
				$cookies = $this->buildCookies($this->request['cookies']);
			}
			$this->request['header'] = array_merge(array('Host' => $this->request['uri']['host']), $this->request['header']);
		}

		if (isset($this->request['auth']['user']) && isset($this->request['auth']['pass'])) {
			$this->request['header']['Authorization'] = $this->request['auth']['method'] . " " . base64_encode($this->request['auth']['user'] . ":" . $this->request['auth']['pass']);
		}
		if (isset($this->request['uri']['user']) && isset($this->request['uri']['pass'])) {
			$this->request['header']['Authorization'] = $this->request['auth']['method'] . " " . base64_encode($this->request['uri']['user'] . ":" . $this->request['uri']['pass']);
		}

		if (is_array($this->request['body'])) {
			$this->request['body'] = $this->_httpSerialize($this->request['body']);
		}

		if (!empty($this->request['body']) && !isset($this->request['header']['Content-Type'])) {
			$this->request['header']['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if (!empty($this->request['body']) && !isset($this->request['header']['Content-Length'])) {
			$this->request['header']['Content-Length'] = strlen($this->request['body']);
		}

		$connectionType = null;
		if (isset($this->request['header']['Connection'])) {
			$connectionType = $this->request['header']['Connection'];
		}
		$this->request['header'] = $this->_buildHeader($this->request['header']) . $cookies;

		if (empty($this->request['line'])) {
			$this->request['line'] = $this->_buildRequestLine($this->request);
		}

		if ($this->quirksMode === false && $this->request['line'] === false) {
			return $this->response = false;
		}

		if ($this->request['line'] !== false) {
			$this->request['raw'] = $this->request['line'];
		}

		if ($this->request['header'] !== false) {
			$this->request['raw'] .= $this->request['header'];
		}

		$this->request['raw'] .= "\r\n";
		$this->request['raw'] .= $this->request['body'];
		$this->write($this->request['raw']);

		$response = null;
		while ($data = $this->read()) {
			$response .= $data;
		}

		if ($connectionType == 'close') {
			$this->disconnect();
		}

		$this->response = $this->_parseResponse($response);
		if (!empty($this->response['cookies'])) {
			$this->config['request']['cookies'] = array_merge($this->config['request']['cookies'], $this->response['cookies']);
		}

		return $this->response['body'];
	}

/**
 * Issues a GET request to the specified URI, query, and request.
 *
 * Using a string uri and an array of query string parameters:
 *
 * `$response = $http->get('http://google.com/search', array('q' => 'cakephp', 'client' => 'safari'));`
 *
 * Would do a GET request to `http://google.com/search?q=cakephp&client=safari`
 *
 * You could express the same thing using a uri array and query string parameters:
 *
 * {{{
 * $response = $http->get(
 *     array('host' => 'google.com', 'path' => '/search'),
 *     array('q' => 'cakephp', 'client' => 'safari')
 * );
 * }}}
 *
 * @param mixed $uri URI to request. Either a string uri, or a uri array, see HttpSocket::_parseUri()
 * @param array $query Querystring parameters to append to URI
 * @param array $request An indexed array with indexes such as 'method' or uri
 * @return mixed Result of request, either false on failure or the response to the request.
 * @access public
 */
	function get($uri = null, $query = array(), $request = array()) {
		if (!empty($query)) {
			$uri = $this->_parseUri($uri);
			if (isset($uri['query'])) {
				$uri['query'] = array_merge($uri['query'], $query);
			} else {
				$uri['query'] = $query;
			}
			$uri = $this->_buildUri($uri);
		}

		$request = Set::merge(array('method' => 'GET', 'uri' => $uri), $request);
		return $this->request($request);
	}

/**
 * Issues a POST request to the specified URI, query, and request.
 *
 * `post()` can be used to post simple data arrays to a url:
 *
 * {{{
 * $response = $http->post('http://example.com', array(
 *     'username' => 'batman',
 *     'password' => 'bruce_w4yne'
 * ));
 * }}}
 *
 * @param mixed $uri URI to request. See HttpSocket::_parseUri()
 * @param array $data Array of POST data keys and values.
 * @param array $request An indexed array with indexes such as 'method' or uri
 * @return mixed Result of request, either false on failure or the response to the request.
 * @access public
 */
	function post($uri = null, $data = array(), $request = array()) {
		$request = Set::merge(array('method' => 'POST', 'uri' => $uri, 'body' => $data), $request);
		return $this->request($request);
	}

/**
 * Issues a PUT request to the specified URI, query, and request.
 *
 * @param mixed $uri URI to request, See HttpSocket::_parseUri()
 * @param array $data Array of PUT data keys and values.
 * @param array $request An indexed array with indexes such as 'method' or uri
 * @return mixed Result of request
 * @access public
 */
	function put($uri = null, $data = array(), $request = array()) {
		$request = Set::merge(array('method' => 'PUT', 'uri' => $uri, 'body' => $data), $request);
		return $this->request($request);
	}

/**
 * Issues a DELETE request to the specified URI, query, and request.
 *
 * @param mixed $uri URI to request (see {@link _parseUri()})
 * @param array $data Query to append to URI
 * @param array $request An indexed array with indexes such as 'method' or uri
 * @return mixed Result of request
 * @access public
 */
	function delete($uri = null, $data = array(), $request = array()) {
		$request = Set::merge(array('method' => 'DELETE', 'uri' => $uri, 'body' => $data), $request);
		return $this->request($request);
	}

/**
 * Normalizes urls into a $uriTemplate.  If no template is provided
 * a default one will be used. Will generate the url using the
 * current config information.
 *
 * ### Usage:
 *
 * After configuring part of the request parameters, you can use url() to generate
 * urls.
 *
 * {{{
 * $http->configUri('http://www.cakephp.org');
 * $url = $http->url('/search?q=bar');
 * }}}
 *
 * Would return `http://www.cakephp.org/search?q=bar`
 *
 * url() can also be used with custom templates:
 *
 * `$url = $http->url('http://www.cakephp/search?q=socket', '/%path?%query');`
 *
 * Would return `/search?q=socket`.
 *
 * @param mixed $url Either a string or array of url options to create a url with.
 * @param string $uriTemplate A template string to use for url formatting.
 * @return mixed Either false on failure or a string containing the composed url.
 * @access public
 */
	function url($url = null, $uriTemplate = null) {
		if (is_null($url)) {
			$url = '/';
		}
		if (is_string($url)) {
			if ($url{0} == '/') {
				$url = $this->config['request']['uri']['host'].':'.$this->config['request']['uri']['port'] . $url;
			}
			if (!preg_match('/^.+:\/\/|\*|^\//', $url)) {
				$url = $this->config['request']['uri']['scheme'].'://'.$url;
			}
		} elseif (!is_array($url) && !empty($url)) {
			return false;
		}

		$base = array_merge($this->config['request']['uri'], array('scheme' => array('http', 'https'), 'port' => array(80, 443)));
		$url = $this->_parseUri($url, $base);

		if (empty($url)) {
			$url = $this->config['request']['uri'];
		}

		if (!empty($uriTemplate)) {
			return $this->_buildUri($url, $uriTemplate);
		}
		return $this->_buildUri($url);
	}

/**
 * Parses the given message and breaks it down in parts.
 *
 * @param string $message Message to parse
 * @return array Parsed message (with indexed elements such as raw, status, header, body)
 * @access protected
 */
	function _parseResponse($message) {
		if (is_array($message)) {
			return $message;
		} elseif (!is_string($message)) {
			return false;
		}

		static $responseTemplate;

		if (empty($responseTemplate)) {
			$classVars = get_class_vars(__CLASS__);
			$responseTemplate = $classVars['response'];
		}

		$response = $responseTemplate;

		if (!preg_match("/^(.+\r\n)(.*)(?<=\r\n)\r\n/Us", $message, $match)) {
			return false;
		}

		list($null, $response['raw']['status-line'], $response['raw']['header']) = $match;
		$response['raw']['response'] = $message;
		$response['raw']['body'] = substr($message, strlen($match[0]));

		if (preg_match("/(.+) ([0-9]{3}) (.+)\r\n/DU", $response['raw']['status-line'], $match)) {
			$response['status']['http-version'] = $match[1];
			$response['status']['code'] = (int)$match[2];
			$response['status']['reason-phrase'] = $match[3];
		}

		$response['header'] = $this->_parseHeader($response['raw']['header']);
		$transferEncoding = null;
		if (isset($response['header']['Transfer-Encoding'])) {
			$transferEncoding = $response['header']['Transfer-Encoding'];
		}
		$decoded = $this->_decodeBody($response['raw']['body'], $transferEncoding);
		$response['body'] = $decoded['body'];

		if (!empty($decoded['header'])) {
			$response['header'] = $this->_parseHeader($this->_buildHeader($response['header']).$this->_buildHeader($decoded['header']));
		}

		if (!empty($response['header'])) {
			$response['cookies'] = $this->parseCookies($response['header']);
		}

		foreach ($response['raw'] as $field => $val) {
			if ($val === '') {
				$response['raw'][$field] = null;
			}
		}

		return $response;
	}

/**
 * Generic function to decode a $body with a given $encoding. Returns either an array with the keys
 * 'body' and 'header' or false on failure.
 *
 * @param string $body A string continaing the body to decode.
 * @param mixed $encoding Can be false in case no encoding is being used, or a string representing the encoding.
 * @return mixed Array of response headers and body or false.
 * @access protected
 */
	function _decodeBody($body, $encoding = 'chunked') {
		if (!is_string($body)) {
			return false;
		}
		if (empty($encoding)) {
			return array('body' => $body, 'header' => false);
		}
		$decodeMethod = '_decode'.Inflector::camelize(str_replace('-', '_', $encoding)).'Body';

		if (!is_callable(array(&$this, $decodeMethod))) {
			if (!$this->quirksMode) {
				trigger_error(sprintf(__('HttpSocket::_decodeBody - Unknown encoding: %s. Activate quirks mode to surpress error.', true), h($encoding)), E_USER_WARNING);
			}
			return array('body' => $body, 'header' => false);
		}
		return $this->{$decodeMethod}($body);
	}

/**
 * Decodes a chunked message $body and returns either an array with the keys 'body' and 'header' or false as
 * a result.
 *
 * @param string $body A string continaing the chunked body to decode.
 * @return mixed Array of response headers and body or false.
 * @access protected
 */
	function _decodeChunkedBody($body) {
		if (!is_string($body)) {
			return false;
		}

		$decodedBody = null;
		$chunkLength = null;

		while ($chunkLength !== 0) {
			if (!preg_match("/^([0-9a-f]+) *(?:;(.+)=(.+))?\r\n/iU", $body, $match)) {
				if (!$this->quirksMode) {
					trigger_error(__('HttpSocket::_decodeChunkedBody - Could not parse malformed chunk. Activate quirks mode to do this.', true), E_USER_WARNING);
					return false;
				}
				break;
			}

			$chunkSize = 0;
			$hexLength = 0;
			$chunkExtensionName = '';
			$chunkExtensionValue = '';
			if (isset($match[0])) {
				$chunkSize = $match[0];
			}
			if (isset($match[1])) {
				$hexLength = $match[1];
			}
			if (isset($match[2])) {
				$chunkExtensionName = $match[2];
			}
			if (isset($match[3])) {
				$chunkExtensionValue = $match[3];
			}

			$body = substr($body, strlen($chunkSize));
			$chunkLength = hexdec($hexLength);
			$chunk = substr($body, 0, $chunkLength);
			if (!empty($chunkExtensionName)) {
				/**
				 * @todo See if there are popular chunk extensions we should implement
				 */
			}
			$decodedBody .= $chunk;
			if ($chunkLength !== 0) {
				$body = substr($body, $chunkLength+strlen("\r\n"));
			}
		}

		$entityHeader = false;
		if (!empty($body)) {
			$entityHeader = $this->_parseHeader($body);
		}
		return array('body' => $decodedBody, 'header' => $entityHeader);
	}

/**
 * Parses and sets the specified URI into current request configuration.
 *
 * @param mixed $uri URI, See HttpSocket::_parseUri()
 * @return array Current configuration settings
 * @access protected
 */
	function _configUri($uri = null) {
		if (empty($uri)) {
			return false;
		}

		if (is_array($uri)) {
			$uri = $this->_parseUri($uri);
		} else {
			$uri = $this->_parseUri($uri, true);
		}

		if (!isset($uri['host'])) {
			return false;
		}

		$config = array(
			'request' => array(
				'uri' => array_intersect_key($uri, $this->config['request']['uri']),
				'auth' => array_intersect_key($uri, $this->config['request']['auth'])
			)
		);
		$this->config = Set::merge($this->config, $config);
		$this->config = Set::merge($this->config, array_intersect_key($this->config['request']['uri'], $this->config));
		return $this->config;
	}

/**
 * Takes a $uri array and turns it into a fully qualified URL string
 *
 * @param mixed $uri Either A $uri array, or a request string.  Will use $this->config if left empty.
 * @param string $uriTemplate The Uri template/format to use.
 * @return mixed A fully qualified URL formated according to $uriTemplate, or false on failure
 * @access protected
 */
	function _buildUri($uri = array(), $uriTemplate = '%scheme://%user:%pass@%host:%port/%path?%query#%fragment') {
		if (is_string($uri)) {
			$uri = array('host' => $uri);
		}
		$uri = $this->_parseUri($uri, true);

		if (!is_array($uri) || empty($uri)) {
			return false;
		}

		$uri['path'] = preg_replace('/^\//', null, $uri['path']);
		$uri['query'] = $this->_httpSerialize($uri['query']);
		$stripIfEmpty = array(
			'query' => '?%query',
			'fragment' => '#%fragment',
			'user' => '%user:%pass@',
			'host' => '%host:%port/'
		);

		foreach ($stripIfEmpty as $key => $strip) {
			if (empty($uri[$key])) {
				$uriTemplate = str_replace($strip, null, $uriTemplate);
			}
		}

		$defaultPorts = array('http' => 80, 'https' => 443);
		if (array_key_exists($uri['scheme'], $defaultPorts) && $defaultPorts[$uri['scheme']] == $uri['port']) {
			$uriTemplate = str_replace(':%port', null, $uriTemplate);
		}
		foreach ($uri as $property => $value) {
			$uriTemplate = str_replace('%'.$property, $value, $uriTemplate);
		}

		if ($uriTemplate === '/*') {
			$uriTemplate = '*';
		}
		return $uriTemplate;
	}

/**
 * Parses the given URI and breaks it down into pieces as an indexed array with elements
 * such as 'scheme', 'port', 'query'.
 *
 * @param string $uri URI to parse
 * @param mixed $base If true use default URI config, otherwise indexed array to set 'scheme', 'host', 'port', etc.
 * @return array Parsed URI
 * @access protected
 */
	function _parseUri($uri = null, $base = array()) {
		$uriBase = array(
			'scheme' => array('http', 'https'),
			'host' => null,
			'port' => array(80, 443),
			'user' => null,
			'pass' => null,
			'path' => '/',
			'query' => null,
			'fragment' => null
		);

		if (is_string($uri)) {
			$uri = parse_url($uri);
		}
		if (!is_array($uri) || empty($uri)) {
			return false;
		}
		if ($base === true) {
			$base = $uriBase;
		}

		if (isset($base['port'], $base['scheme']) && is_array($base['port']) && is_array($base['scheme'])) {
			if (isset($uri['scheme']) && !isset($uri['port'])) {
				$base['port'] = $base['port'][array_search($uri['scheme'], $base['scheme'])];
			} elseif (isset($uri['port']) && !isset($uri['scheme'])) {
				$base['scheme'] = $base['scheme'][array_search($uri['port'], $base['port'])];
			}
		}

		if (is_array($base) && !empty($base)) {
			$uri = array_merge($base, $uri);
		}

		if (isset($uri['scheme']) && is_array($uri['scheme'])) {
			$uri['scheme'] = array_shift($uri['scheme']);
		}
		if (isset($uri['port']) && is_array($uri['port'])) {
			$uri['port'] = array_shift($uri['port']);
		}

		if (array_key_exists('query', $uri)) {
			$uri['query'] = $this->_parseQuery($uri['query']);
		}

		if (!array_intersect_key($uriBase, $uri)) {
			return false;
		}
		return $uri;
	}

/**
 * This function can be thought of as a reverse to PHP5's http_build_query(). It takes a given query string and turns it into an array and
 * supports nesting by using the php bracket syntax. So this menas you can parse queries like:
 *
 * - ?key[subKey]=value
 * - ?key[]=value1&key[]=value2
 *
 * A leading '?' mark in $query is optional and does not effect the outcome of this function. 
 * For the complete capabilities of this implementation take a look at HttpSocketTest::testparseQuery()
 *
 * @param mixed $query A query string to parse into an array or an array to return directly "as is"
 * @return array The $query parsed into a possibly multi-level array. If an empty $query is
 *     given, an empty array is returned.
 * @access protected
 */
	function _parseQuery($query) {
		if (is_array($query)) {
			return $query;
		}
		$parsedQuery = array();

		if (is_string($query) && !empty($query)) {
			$query = preg_replace('/^\?/', '', $query);
			$items = explode('&', $query);

			foreach ($items as $item) {
				if (strpos($item, '=') !== false) {
					list($key, $value) = explode('=', $item, 2);
				} else {
					$key = $item;
					$value = null;
				}

				$key = urldecode($key);
				$value = urldecode($value);

				if (preg_match_all('/\[([^\[\]]*)\]/iUs', $key, $matches)) {
					$subKeys = $matches[1];
					$rootKey = substr($key, 0, strpos($key, '['));
					if (!empty($rootKey)) {
						array_unshift($subKeys, $rootKey);
					}
					$queryNode =& $parsedQuery;

					foreach ($subKeys as $subKey) {
						if (!is_array($queryNode)) {
							$queryNode = array();
						}

						if ($subKey === '') {
							$queryNode[] = array();
							end($queryNode);
							$subKey = key($queryNode);
						}
						$queryNode =& $queryNode[$subKey];
					}
					$queryNode = $value;
				} else {
					$parsedQuery[$key] = $value;
				}
			}
		}
		return $parsedQuery;
	}

/**
 * Builds a request line according to HTTP/1.1 specs. Activate quirks mode to work outside specs.
 *
 * @param array $request Needs to contain a 'uri' key. Should also contain a 'method' key, otherwise defaults to GET.
 * @param string $versionToken The version token to use, defaults to HTTP/1.1
 * @return string Request line
 * @access protected
 */
	function _buildRequestLine($request = array(), $versionToken = 'HTTP/1.1') {
		$asteriskMethods = array('OPTIONS');

		if (is_string($request)) {
			$isValid = preg_match("/(.+) (.+) (.+)\r\n/U", $request, $match);
			if (!$this->quirksMode && (!$isValid || ($match[2] == '*' && !in_array($match[3], $asteriskMethods)))) {
				trigger_error(__('HttpSocket::_buildRequestLine - Passed an invalid request line string. Activate quirks mode to do this.', true), E_USER_WARNING);
				return false;
			}
			return $request;
		} elseif (!is_array($request)) {
			return false;
		} elseif (!array_key_exists('uri', $request)) {
			return false;
		}

		$request['uri']	= $this->_parseUri($request['uri']);
		$request = array_merge(array('method' => 'GET'), $request);
		$request['uri'] = $this->_buildUri($request['uri'], '/%path?%query');

		if (!$this->quirksMode && $request['uri'] === '*' && !in_array($request['method'], $asteriskMethods)) {
			trigger_error(sprintf(__('HttpSocket::_buildRequestLine - The "*" asterisk character is only allowed for the following methods: %s. Activate quirks mode to work outside of HTTP/1.1 specs.', true), join(',', $asteriskMethods)), E_USER_WARNING);
			return false;
		}
		return $request['method'].' '.$request['uri'].' '.$versionToken.$this->lineBreak;
	}

/**
 * Serializes an array for transport.
 *
 * @param array $data Data to serialize
 * @return string Serialized variable
 * @access protected
 */
	function _httpSerialize($data = array()) {
		if (is_string($data)) {
			return $data;
		}
		if (empty($data) || !is_array($data)) {
			return false;
		}
		return substr(Router::queryString($data), 1);
	}

/**
 * Builds the header.
 *
 * @param array $header Header to build
 * @return string Header built from array
 * @access protected
 */
	function _buildHeader($header, $mode = 'standard') {
		if (is_string($header)) {
			return $header;
		} elseif (!is_array($header)) {
			return false;
		}

		$returnHeader = '';
		foreach ($header as $field => $contents) {
			if (is_array($contents) && $mode == 'standard') {
				$contents = implode(',', $contents);
			}
			foreach ((array)$contents as $content) {
				$contents = preg_replace("/\r\n(?![\t ])/", "\r\n ", $content);
				$field = $this->_escapeToken($field);

				$returnHeader .= $field.': '.$contents.$this->lineBreak;
			}
		}
		return $returnHeader;
	}

/**
 * Parses an array based header.
 *
 * @param array $header Header as an indexed array (field => value)
 * @return array Parsed header
 * @access protected
 */
	function _parseHeader($header) {
		if (is_array($header)) {
			foreach ($header as $field => $value) {
				unset($header[$field]);
				$field = strtolower($field);
				preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);

				foreach ($offsets[0] as $offset) {
					$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
				}
				$header[$field] = $value;
			}
			return $header;
		} elseif (!is_string($header)) {
			return false;
		}

		preg_match_all("/(.+):(.+)(?:(?<![\t ])" . $this->lineBreak . "|\$)/Uis", $header, $matches, PREG_SET_ORDER);

		$header = array();
		foreach ($matches as $match) {
			list(, $field, $value) = $match;

			$value = trim($value);
			$value = preg_replace("/[\t ]\r\n/", "\r\n", $value);

			$field = $this->_unescapeToken($field);

			$field = strtolower($field);
			preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);
			foreach ($offsets[0] as $offset) {
				$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
			}

			if (!isset($header[$field])) {
				$header[$field] = $value;
			} else {
				$header[$field] = array_merge((array)$header[$field], (array)$value);
			}
		}
		return $header;
	}

/**
 * Parses cookies in response headers.
 *
 * @param array $header Header array containing one ore more 'Set-Cookie' headers.
 * @return mixed Either false on no cookies, or an array of cookies recieved.
 * @access public
 * @todo Make this 100% RFC 2965 confirm
 */
	function parseCookies($header) {
		if (!isset($header['Set-Cookie'])) {
			return false;
		}

		$cookies = array();
		foreach ((array)$header['Set-Cookie'] as $cookie) {
			if (strpos($cookie, '";"') !== false) {
				$cookie = str_replace('";"', "{__cookie_replace__}", $cookie);
				$parts  = str_replace("{__cookie_replace__}", '";"', explode(';', $cookie));
			} else {
				$parts = preg_split('/\;[ \t]*/', $cookie);
			}

			list($name, $value) = explode('=', array_shift($parts), 2);
			$cookies[$name] = compact('value');

			foreach ($parts as $part) {
				if (strpos($part, '=') !== false) {
					list($key, $value) = explode('=', $part);
				} else {
					$key = $part;
					$value = true;
				}

				$key = strtolower($key);
				if (!isset($cookies[$name][$key])) {
					$cookies[$name][$key] = $value;
				}
			}
		}
		return $cookies;
	}

/**
 * Builds cookie headers for a request.
 *
 * @param array $cookies Array of cookies to send with the request.
 * @return string Cookie header string to be sent with the request.
 * @access public
 * @todo Refactor token escape mechanism to be configurable
 */
	function buildCookies($cookies) {
		$header = array();
		foreach ($cookies as $name => $cookie) {
			$header[] = $name.'='.$this->_escapeToken($cookie['value'], array(';'));
		}
		$header = $this->_buildHeader(array('Cookie' => $header), 'pragmatic');
		return $header;
	}

/**
 * Unescapes a given $token according to RFC 2616 (HTTP 1.1 specs)
 *
 * @param string $token Token to unescape
 * @return string Unescaped token
 * @access protected
 * @todo Test $chars parameter
 */
	function _unescapeToken($token, $chars = null) {
		$regex = '/"(['.join('', $this->_tokenEscapeChars(true, $chars)).'])"/';
		$token = preg_replace($regex, '\\1', $token);
		return $token;
	}

/**
 * Escapes a given $token according to RFC 2616 (HTTP 1.1 specs)
 *
 * @param string $token Token to escape
 * @return string Escaped token
 * @access protected
 * @todo Test $chars parameter
 */
	function _escapeToken($token, $chars = null) {
		$regex = '/(['.join('', $this->_tokenEscapeChars(true, $chars)).'])/';
		$token = preg_replace($regex, '"\\1"', $token);
		return $token;
	}

/**
 * Gets escape chars according to RFC 2616 (HTTP 1.1 specs).
 *
 * @param boolean $hex true to get them as HEX values, false otherwise
 * @return array Escape chars
 * @access protected
 * @todo Test $chars parameter
 */
	function _tokenEscapeChars($hex = true, $chars = null) {
		if (!empty($chars)) {
			$escape = $chars;
		} else {
			$escape = array('"', "(", ")", "<", ">", "@", ",", ";", ":", "\\", "/", "[", "]", "?", "=", "{", "}", " ");
			for ($i = 0; $i <= 31; $i++) {
				$escape[] = chr($i);
			}
			$escape[] = chr(127);
		}

		if ($hex == false) {
			return $escape;
		}
		$regexChars = '';
		foreach ($escape as $key => $char) {
			$escape[$key] = '\\x'.str_pad(dechex(ord($char)), 2, '0', STR_PAD_LEFT);
		}
		return $escape;
	}

/**
 * Resets the state of this HttpSocket instance to it's initial state (before Object::__construct got executed) or does
 * the same thing partially for the request and the response property only.
 *
 * @param boolean $full If set to false only HttpSocket::response and HttpSocket::request are reseted
 * @return boolean True on success
 * @access public
 */
	function reset($full = true) {
		static $initalState = array();
		if (empty($initalState)) {
			$initalState = get_class_vars(__CLASS__);
		}

		if ($full == false) {
			$this->request = $initalState['request'];
			$this->response = $initalState['response'];
			return true;
		}
		parent::reset($initalState);
		return true;
	}
}


class HttpSocketOauth extends HttpSocket {

  /**
   * Default OAuth parameters. These get merged into the $request['auth'] param.
   *
   * @var array
   */
	var $defaults = array(
    'oauth_version' => '1.0',
    'oauth_signature_method' => 'HMAC-SHA1',
  );

  /**
   * Overrides HttpSocket::request() to handle cases where
   * $request['auth']['method'] is 'OAuth'.
   *
   * @param array $request As required by HttpSocket::request(). NOTE ONLY
   *   THE ARRAY TYPE OF REQUEST IS SUPPORTED
   * @return array
   */
  function request($request = array()) {

    // If the request does not need OAuth Authorization header, let the parent
    // deal with it.
    if (!isset($request['auth']['method']) || $request['auth']['method'] != 'OAuth') {
      return parent::request($request);
    }

    // Generate the OAuth Authorization Header content for this request from the
    // request data and add it into the request's Authorization Header. Note, we
    // don't just add the header directly in the request variable and return the
    // whole thing from the authorizationHeader() method because in some cases
    // we may not want the authorization header content in the request's
    // authorization header, for example, OAuth Echo as used by Twitpic and
    // Twitter includes an Authorization Header as required by twitter's verify
    // credentials API in the X-Verify-Credentials-Authorization header.
    $request['header']['Authorization'] = $this->authorizationHeader($request);

    // Now the Authorization header is built, fire the request off to the parent
    // HttpSocket class request method that we intercepted earlier.
    return parent::request($request);

  }

  /**
   * Returns the OAuth Authorization Header string for a given request array.
   *
   * This method is called by request but can also be called directly, which is
   * useful if you need to get the OAuth Authorization Header string, such as
   * when integrating with a service that uses OAuth Echo (Authorization
   * Delegation) e.g. Twitpic. In this case you send a normal unauthenticated
   * request to the service e.g. Twitpic along with 2 extra headers:
   * - X-Auth-Service-Provider - effectively, this is the realm that identity
   *   delegation should be sent to - in the case of Twitter, just set this to
   *   https://api.twitter.com/1/account/verify_credentials.json;
   * - X-Verify-Credentials-Authorization - Consumer should create all the OAuth
   *   parameters necessary so it could call
   *   https://api.twitter.com/1/account/verify_credentials.json using OAuth in
   *   the HTTP header (e.g. it should look like OAuth oauth_consumer_key="...",
   *   oauth_token="...", oauth_signature_method="...", oauth_signature="...",
   *   oauth_timestamp="...", oauth_nonce="...", oauth_version="...".
   * 
   * @param array $request As required by HttpSocket::request(). NOTE ONLY
   *   THE ARRAY TYPE OF REQUEST IS SUPPORTED
   * @return String
   */
  function authorizationHeader($request) {

    $request['auth'] = array_merge($this->defaults, $request['auth']);

    // Nonce, or number used once is used to distinguish between different
    // requests to the OAuth provider
    if (!isset($request['auth']['oauth_nonce'])) {
      $request['auth']['oauth_nonce'] = md5(uniqid(rand(), true));
    }

    if (!isset($request['auth']['oauth_timestamp'])) {
      $request['auth']['oauth_timestamp'] = time();
    }

    // Now starts the process of signing the request. The signature is a hash of
    // a signature base string with the secret keys. The signature base string
    // is made up of the request http verb, the request uri and the request
    // params, and the secret keys are the consumer secret (for your
    // application) and the access token secret generated for the user by the
    // provider, e.g. twitter, when the user authorizes your app to access their
    // details.

    // Building the request uri, note we don't include the query string or
    // fragment. Standard ports must not be included but non standard ones must.
    $uriFormat = '%scheme://%host';
    if (isset($request['uri']['port']) && !in_array($request['uri']['port'], array(80, 443))) {
      $uriFormat .= ':' . $request['uri']['port'];
    }
    $uriFormat .= '/%path';
    $requestUrl = $this->_buildUri($request['uri'], $uriFormat);

    // OAuth reference states that the request params, i.e. oauth_ params, body
    // params and query string params need to be normalised, i.e. combined in a
    // single string, separated by '&' in the format name=value. But they also
    // need to be sorted by key, then by value. You can't just merge the auth,
    // body and query arrays together then do a ksort because there may be
    // parameters with the same name. Instead we've got to get them into an
    // array of array('name' => '<name>', 'value' => '<value>') elements, then
    // sort those elements.

    // Let's start with the auth params - however, we shouldn't include the auth
    // method (OAuth), and OAuth reference says not to include the realm or the
    // consumer or token secrets
    $requestParams = $this->assocToNumericNameValue(array_diff_key(
      $request['auth'],
      array_flip(array('realm', 'method', 'oauth_consumer_secret', 'oauth_token_secret'))
    ));

    // Next add the body params if there are any and the content type header is
    // not set, or it's application/x-www-form-urlencoded
    if (isset($request['body']) && (!isset($request['header']['Content-Type']) || stristr($request['header']['Content-Type'], 'application/x-www-form-urlencoded'))) {
      $requestParams = array_merge($requestParams, $this->assocToNumericNameValue($request['body']));
    }

    // Finally the query params
    if (isset($request['uri']['query'])) {
      $requestParams = array_merge($requestParams, $this->assocToNumericNameValue($request['uri']['query']));
    }

    // Now we can sort them by name then value
    usort($requestParams, array($this, 'sortByNameThenByValue'));

    // Now we concatenate them together in name=value pairs separated by &
    $normalisedRequestParams = '';
    foreach ($requestParams as $k => $requestParam) {
      if ($k) {
        $normalisedRequestParams .= '&';
      }
      $normalisedRequestParams .= $requestParam['name'] . '=' . $this->parameterEncode($requestParam['value']);
    }

    // The signature base string consists of the request method (uppercased) and
    // concatenated with the request URL and normalised request parameters
    // string, both encoded, and separated by &
    $signatureBaseString = strtoupper($request['method']) . '&'
                         . $this->parameterEncode($requestUrl) . '&'
                         . $this->parameterEncode($normalisedRequestParams);

    // The signature base string is hashed with a key which is the consumer
    // secret (assigned to your application by the provider) and the token
    // secret (also known as the access token secret, if you've got it yet),
    // both encoded and separated by an &
    $key = '';
    if (isset($request['auth']['oauth_consumer_secret'])) {
      $key .= $this->parameterEncode($request['auth']['oauth_consumer_secret']);
    }
    $key .= '&';
    if (isset($request['auth']['oauth_token_secret'])) {
      $key .= $this->parameterEncode($request['auth']['oauth_token_secret']);
    }

    // Finally construct the signature according to the value of the
    // oauth_signature_method auth param in the request array.
    switch ($request['auth']['oauth_signature_method']) {
      case 'HMAC-SHA1':
        $request['auth']['oauth_signature'] = base64_encode(hash_hmac('sha1', $signatureBaseString, $key, true));
        break;
      default:
        // @todo implement the other 2 hashing methods
        break;
    }

    // Finally, we have all the Authorization header parameters so we can build
    // the header string.
    $authorizationHeader = 'OAuth';

    // We don't want to include the realm, method or secrets though
    $authorizationHeaderParams = array_diff_key(
      $request['auth'],
      array_flip(array('method', 'oauth_consumer_secret', 'oauth_token_secret', 'realm'))
    );

    // Add the Authorization header params to the Authorization header string,
    // properly encoded.
    $first = true;

    if (isset($request['auth']['realm'])) {
      $authorizationHeader .= ' realm="' . $request['auth']['realm'] . '"';
      $first = false;
    }

    foreach ($authorizationHeaderParams as $name => $value) {
      if (!$first) {
        $authorizationHeader .= ',';
      } else {
        $authorizationHeader .= ' ';
        $first = false;
      }
      $authorizationHeader .= $this->authorizationHeaderParamEncode($name, $value);
    }

    return $authorizationHeader;

  }

  /**
   * Builds an Authorization header param string from the supplied name and
   * value. See below for example:
   *
   * @param string $name E.g. 'oauth_signature_method'
   * @param string $value E.g. 'HMAC-SHA1'
   * @return string E.g. 'oauth_signature_method="HMAC-SHA1"'
   */
  function authorizationHeaderParamEncode($name, $value) {
    return $this->parameterEncode($name) . '="' . $this->parameterEncode($value) . '"';
  }

  /**
   * Converts an associative array of name => value pairs to a numerically
   * indexed array of array('name' => '<name>', 'value' => '<value>') elements.
   *
   * @param array $array Associative array
   * @return array
   */
  function assocToNumericNameValue($array) {
    $return = array();
    foreach ($array as $name => $value) {
      $return[] = array(
        'name' => $name,
        'value' => $value,
      );
    }
    return $return;
  }

  /**
   * User defined function to lexically sort an array of
   * array('name' => '<name>', 'value' => '<value>') elements by the value of
   * the name key, and if they're the same, then by the value of the value key.
   *
   * @param array $a Array with key for 'name' and one for 'value'
   * @param array $b Array with key for 'name' and one for 'value'
   * @return integer 1, 0 or -1 depending on whether a greater than b, less than
   *  or the same.
   */
  function sortByNameThenByValue($a, $b) {
    if ($a['name'] == $b['name']) {
      if ($a['value'] == $b['value']) {
        return 0;
      }
      return ($a['value'] > $b['value']) ? 1 : -1;
    }
    return ($a['name'] > $b['name']) ? 1 : -1;
  }

  /**
   * Encodes paramters as per the OAuth spec by utf 8 encoding the param (if it
   * is not already utf 8 encoded) and then percent encoding it according to
   * RFC3986
   *
   * @param string $param
   * @return string
   */
  function parameterEncode($param) {
    $encoding = mb_detect_encoding($param);
    if ($encoding != 'UTF-8') {
      $param = mb_convert_encoding($param, 'UTF-8', $encoding);
    }
    $param = rawurlencode($param);
    $param = str_replace('%7E', '~', $param);
    return $param;
  }

}

