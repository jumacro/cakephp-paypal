<?php
/**
 * PaypalTest.php
 * Created by Rob Mcvey on 2013-07-04.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Rob Mcvey on 2013-07-04.
 * @link          www.copify.com
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Paypal', 'Lib');

/**
 * PaypalTest class
 */
class PaypalTestCase extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Paypal);
	}
	
/**
 * test setExpressCheckout
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testSetExpressCheckout() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$order = array(
			'description' => 'Your purchase with Acme clothes store',
			'currency' => 'GBP',
			'return' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'cancel' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'custom' => 'bingbong',
			'items' => array(
				0 => array(
					'name' => 'Blue shoes',
					'description' => 'A pair of really great blue shoes',
					'tax' => 2.00,
					'shipping' => 0.00,
					'subtotal' => 8.00,
				),
				1 => array(
					'name' => 'Red trousers',
					'description' => 'Tight pair of red pants, look good with a hat.',
					'tax' => 2.00,
					'shipping' => 2.00,
					'subtotal' => 6.00
				),
			)
		);
		$expectedNvps = array(
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => '104.0',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'RETURNURL' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'CANCELURL' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
			'PAYMENTREQUEST_0_ITEMAMT' => 14.00,
			'PAYMENTREQUEST_0_SHIPPINGAMT' => 2.00,
			'PAYMENTREQUEST_0_TAXAMT' => 4.00,
			'PAYMENTREQUEST_0_AMT' => 20.00,
			'PAYMENTREQUEST_0_DESC' => 'Your purchase with Acme clothes store',
			'PAYMENTREQUEST_0_CUSTOM' => 'bingbong',
			'L_PAYMENTREQUEST_0_NAME0' => 'Blue shoes',
			'L_PAYMENTREQUEST_0_DESC0' => 'A pair of really great blue shoes',
			'L_PAYMENTREQUEST_0_TAXAMT0' => 2.00,
			'L_PAYMENTREQUEST_0_AMT0' => 8.00,
			'L_PAYMENTREQUEST_0_QTY0' => 1,
			'L_PAYMENTREQUEST_0_NAME1' => 'Red trousers',
			'L_PAYMENTREQUEST_0_DESC1' => 'Tight pair of red pants, look good with a hat.',
			'L_PAYMENTREQUEST_0_TAXAMT1' => 2.00,
			'L_PAYMENTREQUEST_0_AMT1' => 6.00,
			'L_PAYMENTREQUEST_0_QTY1' => 1,
		);
		$expectedEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		$mockResponse = 'TOKEN=EC%2d5PY500325X986371J&TIMESTAMP=2013%2d07%2d04T13%3a37%3a53Z&CORRELATIONID=845286d6c4caa&ACK=Success&VERSION=104%2e0&BUILD=6680107';
		$this->Paypal->HttpSocket = $this->getMock('HttpSocket');
		$this->Paypal->HttpSocket->expects($this->once())
			->method('post')
			->with($this->equalTo($expectedEndpoint) , $this->equalTo($expectedNvps))
			->will($this->returnValue($mockResponse));	
		$result = $this->Paypal->setExpressCheckout($order);
		$expected = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=EC-5PY500325X986371J';
		$this->assertEqual($expected , $result);
	}
	
/**
 * test getExpressCheckoutDetails
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testGetExpressCheckoutDetails() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$token = 'EC-482053995J417352W';
		$expectedNvps = array(
			'METHOD' => 'GetExpressCheckoutDetails' , 
			'VERSION' => '104.0',
			'TOKEN' => $token,
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
		);
		$expectedEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		$mockResponse = 'TOKEN=EC%2d482053995J417352W&CHECKOUTSTATUS=PaymentActionNotInitiated&TIMESTAMP=2013%2d07%2d05T11%3a16%3a32Z&CORRELATIONID=d817ecf2ba811&ACK=Success&VERSION=104%2e0&BUILD=6680107&EMAIL=robm24_1322925502_per%40gmail%2ecom&PAYERID=GSEK8P4ARYMYS&PAYERSTATUS=verified&FIRSTNAME=Test&LASTNAME=User&COUNTRYCODE=GB&SHIPTONAME=Test%20User&SHIPTOSTREET=1%20Main%20Terrace&SHIPTOCITY=Wolverhampton&SHIPTOSTATE=West%20Midlands&SHIPTOZIP=W12%204LQ&SHIPTOCOUNTRYCODE=GB&SHIPTOCOUNTRYNAME=United%20Kingdom&ADDRESSSTATUS=Confirmed&CURRENCYCODE=GBP&AMT=20%2e00&ITEMAMT=14%2e00&SHIPPINGAMT=2%2e00&HANDLINGAMT=0%2e00&TAXAMT=4%2e00&CUSTOM=bingbong&DESC=Your%20purchase%20with%20Acme%20clothes%20store&INSURANCEAMT=0%2e00&SHIPDISCAMT=0%2e00&L_NAME0=Blue%20shoes&L_NAME1=Red%20trousers&L_QTY0=1&L_QTY1=1&L_TAXAMT0=2%2e00&L_TAXAMT1=2%2e00&L_AMT0=8%2e00&L_AMT1=6%2e00&L_DESC0=A%20pair%20of%20really%20great%20blue%20shoes&L_DESC1=Tight%20pair%20of%20red%20pants%2c%20look%20good%20with%20a%20hat%2e&L_ITEMWEIGHTVALUE0=%20%20%200%2e00000&L_ITEMWEIGHTVALUE1=%20%20%200%2e00000&L_ITEMLENGTHVALUE0=%20%20%200%2e00000&L_ITEMLENGTHVALUE1=%20%20%200%2e00000&L_ITEMWIDTHVALUE0=%20%20%200%2e00000&L_ITEMWIDTHVALUE1=%20%20%200%2e00000&L_ITEMHEIGHTVALUE0=%20%20%200%2e00000&L_ITEMHEIGHTVALUE1=%20%20%200%2e00000&PAYMENTREQUEST_0_CURRENCYCODE=GBP&PAYMENTREQUEST_0_AMT=20%2e00&PAYMENTREQUEST_0_ITEMAMT=14%2e00&PAYMENTREQUEST_0_SHIPPINGAMT=2%2e00&PAYMENTREQUEST_0_HANDLINGAMT=0%2e00&PAYMENTREQUEST_0_TAXAMT=4%2e00&PAYMENTREQUEST_0_CUSTOM=bingbong&PAYMENTREQUEST_0_DESC=Your%20purchase%20with%20Acme%20clothes%20store&PAYMENTREQUEST_0_INSURANCEAMT=0%2e00&PAYMENTREQUEST_0_SHIPDISCAMT=0%2e00&PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED=false&PAYMENTREQUEST_0_SHIPTONAME=Test%20User&PAYMENTREQUEST_0_SHIPTOSTREET=1%20Main%20Terrace&PAYMENTREQUEST_0_SHIPTOCITY=Wolverhampton&PAYMENTREQUEST_0_SHIPTOSTATE=West%20Midlands&PAYMENTREQUEST_0_SHIPTOZIP=W12%204LQ&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=GB&PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME=United%20Kingdom&PAYMENTREQUEST_0_ADDRESSSTATUS=Confirmed&PAYMENTREQUEST_0_ADDRESSNORMALIZATIONSTATUS=None&L_PAYMENTREQUEST_0_NAME0=Blue%20shoes&L_PAYMENTREQUEST_0_NAME1=Red%20trousers&L_PAYMENTREQUEST_0_QTY0=1&L_PAYMENTREQUEST_0_QTY1=1&L_PAYMENTREQUEST_0_TAXAMT0=2%2e00&L_PAYMENTREQUEST_0_TAXAMT1=2%2e00&L_PAYMENTREQUEST_0_AMT0=8%2e00&L_PAYMENTREQUEST_0_AMT1=6%2e00&L_PAYMENTREQUEST_0_DESC0=A%20pair%20of%20really%20great%20blue%20shoes&L_PAYMENTREQUEST_0_DESC1=Tight%20pair%20of%20red%20pants%2c%20look%20good%20with%20a%20hat%2e&L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE1=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMLENGTHVALUE0=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMLENGTHVALUE1=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMWIDTHVALUE0=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMWIDTHVALUE1=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE0=%20%20%200%2e00000&L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE1=%20%20%200%2e00000&PAYMENTREQUESTINFO_0_ERRORCODE=0';
		$this->Paypal->HttpSocket = $this->getMock('HttpSocket');
		$this->Paypal->HttpSocket->expects($this->once())
			->method('post')
			->with($this->equalTo($expectedEndpoint) , $this->equalTo($expectedNvps))
			->will($this->returnValue($mockResponse));
		$expected = array(
			'TOKEN' => 'EC-482053995J417352W',
			'CHECKOUTSTATUS' => 'PaymentActionNotInitiated',
			'TIMESTAMP' => '2013-07-05T11:16:32Z',
			'CORRELATIONID' => 'd817ecf2ba811',
			'ACK' => 'Success',
			'VERSION' => '104.0',
			'BUILD' => '6680107',
			'EMAIL' => 'robm24_1322925502_per@gmail.com',
			'PAYERID' => 'GSEK8P4ARYMYS',
			'PAYERSTATUS' => 'verified',
			'FIRSTNAME' => 'Test',
			'LASTNAME' => 'User',
			'COUNTRYCODE' => 'GB',
			'SHIPTONAME' => 'Test User',
			'SHIPTOSTREET' => '1 Main Terrace',
			'SHIPTOCITY' => 'Wolverhampton',
			'SHIPTOSTATE' => 'West Midlands',
			'SHIPTOZIP' => 'W12 4LQ',
			'SHIPTOCOUNTRYCODE' => 'GB',
			'SHIPTOCOUNTRYNAME' => 'United Kingdom',
			'ADDRESSSTATUS' => 'Confirmed',
			'CURRENCYCODE' => 'GBP',
			'AMT' => '20.00',
			'ITEMAMT' => '14.00',
			'SHIPPINGAMT' => '2.00',
			'HANDLINGAMT' => '0.00',
			'TAXAMT' => '4.00',
			'CUSTOM' => 'bingbong',
			'DESC' => 'Your purchase with Acme clothes store',
			'INSURANCEAMT' => '0.00',
			'SHIPDISCAMT' => '0.00',
			'L_NAME0' => 'Blue shoes',
			'L_NAME1' => 'Red trousers',
			'L_QTY0' => '1',
			'L_QTY1' => '1',
			'L_TAXAMT0' => '2.00',
			'L_TAXAMT1' => '2.00',
			'L_AMT0' => '8.00',
			'L_AMT1' => '6.00',
			'L_DESC0' => 'A pair of really great blue shoes',
			'L_DESC1' => 'Tight pair of red pants, look good with a hat.',
			'L_ITEMWEIGHTVALUE0' => '   0.00000',
			'L_ITEMWEIGHTVALUE1' => '   0.00000',
			'L_ITEMLENGTHVALUE0' => '   0.00000',
			'L_ITEMLENGTHVALUE1' => '   0.00000',
			'L_ITEMWIDTHVALUE0' => '   0.00000',
			'L_ITEMWIDTHVALUE1' => '   0.00000',
			'L_ITEMHEIGHTVALUE0' => '   0.00000',
			'L_ITEMHEIGHTVALUE1' => '   0.00000',
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
			'PAYMENTREQUEST_0_AMT' => '20.00',
			'PAYMENTREQUEST_0_ITEMAMT' => '14.00',
			'PAYMENTREQUEST_0_SHIPPINGAMT' => '2.00',
			'PAYMENTREQUEST_0_HANDLINGAMT' => '0.00',
			'PAYMENTREQUEST_0_TAXAMT' => '4.00',
			'PAYMENTREQUEST_0_CUSTOM' => 'bingbong',
			'PAYMENTREQUEST_0_DESC' => 'Your purchase with Acme clothes store',
			'PAYMENTREQUEST_0_INSURANCEAMT' => '0.00',
			'PAYMENTREQUEST_0_SHIPDISCAMT' => '0.00',
			'PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED' => 'false',
			'PAYMENTREQUEST_0_SHIPTONAME' => 'Test User',
			'PAYMENTREQUEST_0_SHIPTOSTREET' => '1 Main Terrace',
			'PAYMENTREQUEST_0_SHIPTOCITY' => 'Wolverhampton',
			'PAYMENTREQUEST_0_SHIPTOSTATE' => 'West Midlands',
			'PAYMENTREQUEST_0_SHIPTOZIP' => 'W12 4LQ',
			'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'GB',
			'PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME' => 'United Kingdom',
			'PAYMENTREQUEST_0_ADDRESSSTATUS' => 'Confirmed',
			'PAYMENTREQUEST_0_ADDRESSNORMALIZATIONSTATUS' => 'None',
			'L_PAYMENTREQUEST_0_NAME0' => 'Blue shoes',
			'L_PAYMENTREQUEST_0_NAME1' => 'Red trousers',
			'L_PAYMENTREQUEST_0_QTY0' => '1',
			'L_PAYMENTREQUEST_0_QTY1' => '1',
			'L_PAYMENTREQUEST_0_TAXAMT0' => '2.00',
			'L_PAYMENTREQUEST_0_TAXAMT1' => '2.00',
			'L_PAYMENTREQUEST_0_AMT0' => '8.00',
			'L_PAYMENTREQUEST_0_AMT1' => '6.00',
			'L_PAYMENTREQUEST_0_DESC0' => 'A pair of really great blue shoes',
			'L_PAYMENTREQUEST_0_DESC1' => 'Tight pair of red pants, look good with a hat.',
			'L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE1' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMLENGTHVALUE0' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMLENGTHVALUE1' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMWIDTHVALUE0' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMWIDTHVALUE1' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE0' => '   0.00000',
			'L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE1' => '   0.00000',
			'PAYMENTREQUESTINFO_0_ERRORCODE' => '0'
		);	
		$result = $this->Paypal->getExpressCheckoutDetails($token);
		$this->assertEqual($expected , $result);
	}
	
/**
 * test doExpressCheckoutPayment
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testDoExpressCheckoutPayment() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$order = array(
			'description' => 'Your purchase with Acme clothes store',
			'currency' => 'GBP',
			'return' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'cancel' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'custom' => 'bingbong',
			'items' => array(
				0 => array(
					'name' => 'Blue shoes',
					'description' => 'A pair of really great blue shoes',
					'tax' => 2.00,
					'shipping' => 0.00,
					'subtotal' => 8.00,
				),
				1 => array(
					'name' => 'Red trousers',
					'description' => 'Tight pair of red pants, look good with a hat.',
					'tax' => 2.00,
					'shipping' => 2.00,
					'subtotal' => 6.00
				),
			)
		);
		$token = 'EC-482053995J417352W'; 
		$payerId = 'GSEK8P4ARYMYS';
		$expectedEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		$mockResponse = 'TOKEN=EC%2d88C117816V738422U&SUCCESSPAGEREDIRECTREQUESTED=false&TIMESTAMP=2013%2d07%2d05T11%3a41%3a07Z&CORRELATIONID=a4821326a65c6&ACK=Success&VERSION=104%2e0&BUILD=6680107&INSURANCEOPTIONSELECTED=false&SHIPPINGOPTIONISDEFAULT=false&PAYMENTINFO_0_TRANSACTIONID=5V948789BD843205H&PAYMENTINFO_0_TRANSACTIONTYPE=cart&PAYMENTINFO_0_PAYMENTTYPE=instant&PAYMENTINFO_0_ORDERTIME=2013%2d07%2d05T11%3a41%3a06Z&PAYMENTINFO_0_AMT=20%2e00&PAYMENTINFO_0_FEEAMT=0%2e88&PAYMENTINFO_0_TAXAMT=4%2e00&PAYMENTINFO_0_CURRENCYCODE=GBP&PAYMENTINFO_0_PAYMENTSTATUS=Completed&PAYMENTINFO_0_PENDINGREASON=None&PAYMENTINFO_0_REASONCODE=None&PAYMENTINFO_0_PROTECTIONELIGIBILITY=Eligible&PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE=ItemNotReceivedEligible%2cUnauthorizedPaymentEligible&PAYMENTINFO_0_SECUREMERCHANTACCOUNTID=AD4VU2GRDM9EU&PAYMENTINFO_0_ERRORCODE=0&PAYMENTINFO_0_ACK=Success';
		$expectedNvps = array(
			'METHOD' => 'DoExpressCheckoutPayment',
			'VERSION' => '104.0',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'RETURNURL' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'CANCELURL' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
			'PAYMENTREQUEST_0_DESC' => 'Your purchase with Acme clothes store',
			'PAYMENTREQUEST_0_CUSTOM' => 'bingbong',
			'PAYMENTREQUEST_0_ITEMAMT' => (float) 14,
			'PAYMENTREQUEST_0_SHIPPINGAMT' => (float) 2,
			'PAYMENTREQUEST_0_TAXAMT' => (float) 4,
			'PAYMENTREQUEST_0_AMT' => (float) 20,
			'L_PAYMENTREQUEST_0_NAME0' => 'Blue shoes',
			'L_PAYMENTREQUEST_0_DESC0' => 'A pair of really great blue shoes',
			'L_PAYMENTREQUEST_0_TAXAMT0' => (float) 2,
			'L_PAYMENTREQUEST_0_AMT0' => (float) 8,
			'L_PAYMENTREQUEST_0_QTY0' => (int) 1,
			'L_PAYMENTREQUEST_0_NAME1' => 'Red trousers',
			'L_PAYMENTREQUEST_0_DESC1' => 'Tight pair of red pants, look good with a hat.',
			'L_PAYMENTREQUEST_0_TAXAMT1' => (float) 2,
			'L_PAYMENTREQUEST_0_AMT1' => (float) 6,
			'L_PAYMENTREQUEST_0_QTY1' => (int) 1,
			'TOKEN' => $token,
			'PAYERID' => $payerId
		);
		$this->Paypal->HttpSocket = $this->getMock('HttpSocket');
		$this->Paypal->HttpSocket->expects($this->once())
			->method('post')
			->with($this->equalTo($expectedEndpoint) , $this->equalTo($expectedNvps))
			->will($this->returnValue($mockResponse));
		$expected = array(
			'TOKEN' => $token,
			'SUCCESSPAGEREDIRECTREQUESTED' => 'false',
			'TIMESTAMP' => '2013-07-05T11:41:07Z',
			'CORRELATIONID' => 'a4821326a65c6',
			'ACK' => 'Success',
			'VERSION' => '104.0',
			'BUILD' => '6680107',
			'INSURANCEOPTIONSELECTED' => 'false',
			'SHIPPINGOPTIONISDEFAULT' => 'false',
			'PAYMENTINFO_0_TRANSACTIONID' => '5V948789BD843205H',
			'PAYMENTINFO_0_TRANSACTIONTYPE' => 'cart',
			'PAYMENTINFO_0_PAYMENTTYPE' => 'instant',
			'PAYMENTINFO_0_ORDERTIME' => '2013-07-05T11:41:06Z',
			'PAYMENTINFO_0_AMT' => '20.00',
			'PAYMENTINFO_0_FEEAMT' => '0.88',
			'PAYMENTINFO_0_TAXAMT' => '4.00',
			'PAYMENTINFO_0_CURRENCYCODE' => 'GBP',
			'PAYMENTINFO_0_PAYMENTSTATUS' => 'Completed',
			'PAYMENTINFO_0_PENDINGREASON' => 'None',
			'PAYMENTINFO_0_REASONCODE' => 'None',
			'PAYMENTINFO_0_PROTECTIONELIGIBILITY' => 'Eligible',
			'PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE' => 'ItemNotReceivedEligible,UnauthorizedPaymentEligible',
			'PAYMENTINFO_0_SECUREMERCHANTACCOUNTID' => 'AD4VU2GRDM9EU',
			'PAYMENTINFO_0_ERRORCODE' => '0',
			'PAYMENTINFO_0_ACK' => 'Success'
		);
		$result = $this->Paypal->doExpressCheckoutPayment($order , $token , $payerId);
	}	

/**
 * test parseClassicApiResponse
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testParseClassicApiResponse() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		
		// Success
		$response = 'TOKEN=EC%2d5PY500325X986371J&TIMESTAMP=2013%2d07%2d04T13%3a37%3a53Z&CORRELATIONID=845286d6c4caa&ACK=Success&VERSION=104%2e0&BUILD=6680107';
		$result = $this->Paypal->parseClassicApiResponse($response);
		$expected = array(
			'TOKEN' => 'EC-5PY500325X986371J',
			'TIMESTAMP' => '2013-07-04T13:37:53Z',
			'CORRELATIONID' => '845286d6c4caa',
			'ACK' => 'Success',
			'VERSION' => '104.0',
			'BUILD' => '6680107'
		);
		$this->assertEqual($expected , $result);
			
		// Error
		$response = 'TIMESTAMP=2013%2d07%2d04T13%3a27%3a02Z&CORRELATIONID=b0d9a91ac8d1b&ACK=Failure&VERSION=104%2e0&BUILD=6680107&L_ERRORCODE0=10002&L_SHORTMESSAGE0=Authentication%2fAuthorization%20Failed&L_LONGMESSAGE0=You%20do%20not%20have%20permissions%20to%20make%20this%20API%20call&L_SEVERITYCODE0=Error';
		$result = $this->Paypal->parseClassicApiResponse($response);
		$expected = array(
			'L_SEVERITYCODE0' => 'EC-5PY500325X986371J',
			'TIMESTAMP' => '2013-07-04T13:27:02Z',
			'CORRELATIONID' => 'b0d9a91ac8d1b',
			'ACK' => 'Failure',
			'VERSION' => '104.0',
			'BUILD' => '6680107',
			'L_ERRORCODE0' => '10002',
			'L_SHORTMESSAGE0' => 'Authentication/Authorization Failed',
			'L_LONGMESSAGE0' => 'You do not have permissions to make this API call',
			'L_SEVERITYCODE0' => 'Error',
		);
		$this->assertEqual($expected , $result);
	}

/**
 * test getRestEndpoint
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testGetEndpoints() {
		// Live
		$this->Paypal = new Paypal(array('sandboxMode' => false));
		$this->assertEqual("https://api.paypal.com" , $this->Paypal->getRestEndpoint());
		$this->assertEqual("https://api-3t.paypal.com/nvp" , $this->Paypal->getClassicEndpoint());
		$this->assertEqual("https://www.paypal.com/webscr" , $this->Paypal->getPaypalLoginUri());
		// Sandbox
		$this->Paypal = new Paypal(array('sandboxMode' => true));
		$this->assertEqual("https://api.sandbox.paypal.com" , $this->Paypal->getRestEndpoint());
		$this->assertEqual("https://api-3t.sandbox.paypal.com/nvp" , $this->Paypal->getClassicEndpoint());
		$this->assertEqual("https://www.sandbox.paypal.com/webscr" , $this->Paypal->getPaypalLoginUri());
	}

/**
 * test buildExpressCheckoutNvp
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testBuildExpressCheckoutNvp() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$order = array(
			'description' => 'Your purchase with Acme clothes store',
			'currency' => 'GBP',
			'return' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'cancel' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'custom' => 'bingbong',
			'items' => array(
				0 => array(
					'name' => 'Blue shoes',
					'description' => 'A pair of really great blue shoes',
					'tax' => 2.00,
					'shipping' => 0.00,
					'subtotal' => 8.00,
				),
				1 => array(
					'name' => 'Red trousers',
					'description' => 'Tight pair of red pants, look good with a hat.',
					'tax' => 2.00,
					'shipping' => 2.00,
					'subtotal' => 6.00
				),
			)
		);
		$expected = array(
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => '104.0',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'RETURNURL' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'CANCELURL' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
			'PAYMENTREQUEST_0_ITEMAMT' => 14.00,
			'PAYMENTREQUEST_0_SHIPPINGAMT' => 2.00,
			'PAYMENTREQUEST_0_TAXAMT' => 4.00,
			'PAYMENTREQUEST_0_AMT' => 20.00,
			'PAYMENTREQUEST_0_DESC' => 'Your purchase with Acme clothes store',
			'PAYMENTREQUEST_0_CUSTOM' => 'bingbong',
			'L_PAYMENTREQUEST_0_NAME0' => 'Blue shoes',
			'L_PAYMENTREQUEST_0_DESC0' => 'A pair of really great blue shoes',
			'L_PAYMENTREQUEST_0_TAXAMT0' => 2.00,
			'L_PAYMENTREQUEST_0_AMT0' => 8.00,
			'L_PAYMENTREQUEST_0_QTY0' => 1,
			'L_PAYMENTREQUEST_0_NAME1' => 'Red trousers',
			'L_PAYMENTREQUEST_0_DESC1' => 'Tight pair of red pants, look good with a hat.',
			'L_PAYMENTREQUEST_0_TAXAMT1' => 2.00,
			'L_PAYMENTREQUEST_0_AMT1' => 6.00,
			'L_PAYMENTREQUEST_0_QTY1' => 1,
		);
		$result = $this->Paypal->buildExpressCheckoutNvp($order);
		$this->assertEqual($expected , $result);
	}
	
/**
 * test buildExpressCheckoutNvp
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testBuildExpressCheckoutNvpLimit() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$item = array(
			'name' => 'Blue shoes',
			'description' => 'A pair of really great blue shoes',
			'tax' => 2.00,
			'shipping' => 0.00,
			'subtotal' => 8.00,
		);
		$items = array();
		$n = 0;
		while ($n++ < 15) {
			$items[] = $item;
		}
		$order = array(
			'description' => 'Your purchase with Acme clothes store',
			'currency' => 'GBP',
			'return' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'cancel' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'custom' => 'bingbong',
			'items' => $items
		);
		$expected = array(
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => '104.0',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'RETURNURL' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'CANCELURL' => 'https://www.my-amazing-clothes-store.com/checkout.php',
			'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
			'PAYMENTREQUEST_0_ITEMAMT' => 120.00,
			'PAYMENTREQUEST_0_SHIPPINGAMT' => 0.00,
			'PAYMENTREQUEST_0_TAXAMT' => 30.00,
			'PAYMENTREQUEST_0_AMT' => 150.00,
			'PAYMENTREQUEST_0_DESC' => 'Your purchase with Acme clothes store',
			'PAYMENTREQUEST_0_CUSTOM' => 'bingbong',
		);
		$result = $this->Paypal->buildExpressCheckoutNvp($order);
		$this->assertEqual($expected , $result);
	}	
	
/**
 * test buildExpressCheckoutNvp exceptions
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testBuildExpressCheckoutNvpExceptions() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		// Empty array
		$order = array();
		$expected = 'You must pass a valid order array';
		try {
			$this->Paypal->buildExpressCheckoutNvp($order);
			$this->fail("Did not throw Exception with $expected");
		} catch(Exception $e) {
			$this->assertEqual($expected , $e->getMessage());
		}
		
		// Missing return/cancel urls
		$order = array('description' => 'foo');
		$expected = 'Valid "return" and "cancel" urls must be provided';
		try {
			$this->Paypal->buildExpressCheckoutNvp($order);
			$this->fail("Did not throw Exception with $expected");
		} catch(Exception $e) {
			$this->assertEqual($expected , $e->getMessage());
		}
		
		$order = array(
			'description' => 'foo',
			'return' => 'https://www.my-amazing-clothes-store.com/review-paypal.php',
			'cancel' => 'https://www.my-amazing-clothes-store.com/checkout.php',
		);
		$expected = 'You must provide a currency code';
		try {
			$this->Paypal->buildExpressCheckoutNvp($order);
			$this->fail("Did not throw Exception with $expected");
		} catch(Exception $e) {
			$this->assertEqual($expected , $e->getMessage());
		}
	}
	
/**
 * test doDirectPayment
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testDoDirectPayment() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'			
		));
		$payment = array(
			'amount' => 30.00,
			'card' => '4008 0687 0641 8697', // This is a sandbox CC
			'expiry' => array(
				'M' => '2',
				'Y' => '2016',
			),
			'cvv' => '321',
		);
		// Mock the CakeRequest class
		$this->Paypal->CakeRequest = $this->getMock('CakeRequest');
		$this->Paypal->CakeRequest->expects($this->once())
			->method('clientIp')
			->will($this->returnValue("217.114.52.94"));
		
		// Mock the HttpSocket class
		$expectedEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		$mockResponse = 'TIMESTAMP=2013%2d07%2d05T13%3a52%3a48Z&CORRELATIONID=5d7677126e0b4&ACK=Success&VERSION=104%2e0&BUILD=6680107&AMT=30%2e00&CURRENCYCODE=GBP&AVSCODE=X&CVV2MATCH=M&TRANSACTIONID=0XW09448VG556664J';
		$expectedNvps = array(
			'METHOD' => 'DoDirectPayment',
			'VERSION' => '104.0',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'IPADDRESS' => '217.114.52.94',
			'AMT' => 30.00,
			'CURRENCYCODE' => 'GBP',
			'RECURRING' => 'N',
			'ACCT' => '4008068706418697', // This is a sandbox CC
			'EXPDATE' => '022016',
			'CVV2' => '321',
			'FIRSTNAME' => '',
			'LASTNAME' => '',
			'STREET' => '',
			'CITY' => '',
			'STATE' => '',
			'COUNTRYCODE' => '',
			'ZIP' => ''
		);
		$this->Paypal->HttpSocket = $this->getMock('HttpSocket');
		$this->Paypal->HttpSocket->expects($this->once())
			->method('post')
			->with($this->equalTo($expectedEndpoint) , $this->equalTo($expectedNvps))
			->will($this->returnValue($mockResponse));
				
		$result = $this->Paypal->doDirectPayment($payment);
		$expected = array(
			'TIMESTAMP' => '2013-07-05T13:52:48Z',
			'CORRELATIONID' => '5d7677126e0b4',
			'ACK' => 'Success',
			'VERSION' => '104.0',
			'BUILD' => '6680107',
			'AMT' => '30.00',
			'CURRENCYCODE' => 'GBP',
			'AVSCODE' => 'X',
			'CVV2MATCH' => 'M',
			'TRANSACTIONID' => '0XW09448VG556664J'
		);
		$this->assertEqual($expected , $result);
	}
	
/**
 * test formatDoDirectPaymentNvps
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testFormatDoDirectPaymentNvps() {
		$this->Paypal = new Paypal(array(
			'sandboxMode' => true,
			'nvpUsername' => 'foo',
			'nvpPassword' => 'bar',
			'nvpSignature' => 'foobar'
		));
		$payment = array(
			'amount' => 30.00,
			'currency' => 'GBP',
			'card' => '4008 0687 0641 8697', // This is a sandbox CC
			'expiry' => array(
				'M' => '2',
				'Y' => '2016',
			),
			'cvv' => '321',
		);
		$this->Paypal->CakeRequest = $this->getMock('CakeRequest');
		$this->Paypal->CakeRequest->expects($this->once())
			->method('clientIp')
			->will($this->returnValue("217.114.52.94"));	
		$result = $this->Paypal->formatDoDirectPaymentNvps($payment);
		$expected = array(
			'METHOD' => 'DoDirectPayment',
			'VERSION' => '104.0',
			'USER' => 'foo',
			'PWD' => 'bar',
			'SIGNATURE' => 'foobar',
			'IPADDRESS' => '217.114.52.94',
			'AMT' => 30.00,
			'CURRENCYCODE' => 'GBP',
			'RECURRING' => 'N',
			'ACCT' => '4008068706418697', // This is a sandbox CC
			'EXPDATE' => '022016',
			'CVV2' => '321',
			'FIRSTNAME' => '',
			'LASTNAME' => '',
			'STREET' => '',
			'CITY' => '',
			'STATE' => '',
			'COUNTRYCODE' => '',
			'ZIP' => ''
		);
		$this->assertEqual($expected , $result);
	}
	
}
