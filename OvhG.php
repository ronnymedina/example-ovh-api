<?php

namespace models;

require __DIR__ . '/php-ovh-2.0.1/vendor/autoload.php';

use \Ovh\Api as Api;
use GuzzleHttp\Client;

/**
* @author Ronny M <ronnyzte@gmail.com>
* @version 1.0.0
* @package models
* 2017-09-07 15:09
* URL GENERATE KEYS = https://api.ovh.com/createToken/index.cgi?GET=/*&PUT=/*&POST=/*&DELETE=/*
* URL CONSOLA 			= https://api.ovh.com/console/#/order/cart#
* URL REFERENCES 		= https://www.ovh.com/fr/soapi-to-apiv6-migration/
*/


class OvhG {

	private $applicationKey 		= '';
	private $applicationSecret 	= '';
	private $consumer_key 			= '';
	private $endpoint 					= 'ovh-eu';
	private $ovh;

	public function __construct() {

		$http_client = new Client([
			'timeout'         => 30,
			'connect_timeout' => 5,
			#'http_errors'     => false
		]);

		$this->ovh = new Api(
			$this->applicationKey, 
			$this->applicationSecret, 
			$this->endpoint, 
			$this->consumer_key,
			$http_client
		);
	}

	/**
	* crea una nueva orden
	* function order_cart_new
	* @access public
	* @param string $descripcion, datetime $expire, string $ovhSubsidiary
	* @return array


		$descripcion -> 
			val
			null
			''
	 	$expire ejemp -> 
	 		val
	 		null
	 		''
		$ovhSubsidiary -> 
			ES 
			EU
			CA

		$array return -> 
		cartId: ""
	 	expire: ""
		description: ""
		readOnly: false
		items: [ ]
	*/

	# POST /order/cart
	public function order_cart_new($description, $expire, $ovhSubsidiary = 'ES') {
		return $this->ovh->post('/order/cart', [
	    'description' 	=> $description,
	    'expire' 				=> $expire,
	    'ovhSubsidiary' => $ovhSubsidiary,
		]);
	}

	/**
	* Muestra informacion del dominio
	* function info_domain
	* @access public
	* @param string $cartId, string $domain
	* @return array
	*/

	# GET /order/cart/{cartId}/domain
	public function info_domain($cartId, $domain) {
		return $this->ovh->get('/order/cart/'.$cartId.'/domain?domain='.$domain); 
	}

	/**
	* Agregar el dominio al carrito de compras
	* function add_domain_to_cart
	* @access public
	* @param string $cartId
	* @param string $domain
	* @param string $duration
	* @param string $offerId
	* @param string $quantity
	* @return array
	*/

	# POST /order/cart/{cartId}/domain 
	public function add_domain_to_cart($cartId, $domain, $duration = 'P1Y', $offerId = '', $quantity = '') {
		return $this->ovh->post('/order/cart/'.$cartId.'/domain', [
	    'domain' 		=> $domain, // Domain name to order (type: string)
	    'duration' 	=> $duration, // Duration for the product (type: string)
	    'offerId' 	=> $offerId, // Offer unique identifier (type: string)
	    'quantity' 	=> $quantity, // Quantity to order (type: long)
		]);
	}

	/**
	* Asigna el carrito de compras a un usuario conectado
	* function add_cart_to_user
	* @access public
	* @param string $cartId
	* @return null
	*/

	# POST /order/cart/{cartId}/checkout
	public function add_cart_to_user($cartId) {
		return $this->ovh->post('/order/cart/'.$cartId.'/assign');
	}

	/**
	* Asigna el carrito de compras a un usuario conectado
	* function validate_orden_cart
	* @access public
	* @param string $cartId
	* @return array
	*/

	# POST /order/cart/{cartId}/checkout
	public function validate_orden_cart($cartId, $waiveRetractationPeriod = true) {
		return $this->ovh->post('/order/cart/'.$cartId.'/checkout', [
		  'waiveRetractationPeriod' => $waiveRetractationPeriod, // Indicates that order will be processed with waiving retractation period (type: boolean)
		]);
	}

	/**
	* Realiza el pago de una orden solicitado por algun metodo de pago disponible
	* function payment_orden
	* @access public
	* @param string $ordenId
	* @param string $paymentMean
	* @param string $paymentMeanId
	* @return null
	*/

	# lista de pagos disponibles
	# * bankAccount 			-> 0
	# * creditCard				-> 1
	#	* fidelityAccount 	-> 2
	# * ovhAccount 				-> 3
	# * paypal 						-> 4

	# POST /me/order/{orderId}/payWithRegisteredPaymentMean
	public function payment_orden($ordenId, $paymentMean = 'ovhAccount', $paymentMeanId = '') {

		$data = ['paymentMean' => $paymentMean];

		if(!empty($paymentMeanId))
			$data['paymentMeanId'] = $paymentMeanId;

		return $this->ovh->post('/me/order/'.$ordenId.'/payWithRegisteredPaymentMean', $data);
	}

	/**
	* Agregar un dns al dominio
	* function add_dns_to_domain
	* @access public
	* @param string 	$domain
	* @param string 	$fieldType
	* @param string 	$subDomain
	* @param string 	$target
	* @param integer 	$ttl
	* @return null
	*/

	# /domain/zone/{zoneName}/record
	public function add_dns_to_domain($domain, $fieldType = 'TXT', $subDomain = 'ovhcontrol', $target = '', $ttl = 0) {
		return $this->ovh->post('/domain/zone/'.$domain.'/record', [
	    'fieldType' => $fieldType, // Required: Resource record Name (type: zone.NamedResolutionFieldTypeEnum)
	    'subDomain' => $subDomain, // Resource record subdomain (type: string)
	    'target' 		=> $target, // Required: Resource record target (type: string)
	    'ttl' 			=> $ttl, // Resource record ttl (type: long)
		]);
	}

	/**
	* Asigna un hosting al dominio
	* function add_hosting_to_domain
	* @access public
	* @param string 	$serviceName
	* @param string 	$cdn
	* @param string 	$domain
	* @param string 	$ownLog
	* @param string 	$path
	* @param boolean 	$ssl
	* @return null
	*/

	# kaufen-mallorca-immobilien.com
	# /hosting/web/{serviceName}/attachedDomain
	public function add_hosting_to_domain($serviceName = '', $cdn, $domain, $firewall, $ownLog, $path = '', $ssl = false) {
		$data = [
			'domain' 	=> $domain, // Required: Domain to link (type: string)
	    'path' 		=> $path, // Required: Domain's path, relative to your home directory (type: string)
	    'ssl' 		=> false, // Put domain in ssl certificate (type: boolean)
		];

		if(!$cdn)
			$data['cdn'] 			= $cdn; # 'active'

		if(!$firewall)
			$data['firewall'] = $firewall; # 'active'

		if(!$ownLog)
			$data['ownLog'] 	= $ownLog;

		$this->ovh->post('/hosting/web/'.$serviceName.'/attachedDomain', $data);
	}

	/**
	* Lista los carrito generados
	* function exists_cart_id
	* @access public
	* @param string 	$description
	* @return array
	*/

	# GET /order/cart 
	public function exists_cart_id($description = '') {
		return $this->ovh->get('/order/cart', [
		  'description' => $description,
		]);
	}

	/**
	* Lista los item de un carrito
	* function exists_item_id
	* @access public
	* @param string 	$cartId
	* @return array
	*/

	# GET /order/cart/{cartId}/item 
	public function exists_item_id($cartId) {
		return $this->ovh->get('/order/cart/'.$cartId.'/item');
	}

	/**
	* Lista las ordenes generadas
	* function get_orders
	* @access public
	* @return array
	*/

	# GET /me/order
	public function get_orders() {
		return $this->ovh->get('/me/order');
	}
}