<?php
namespace App\Http\Controllers;
use Route,DB, Config, Sentry, Auth, Session;
use App\Http\Models\Page;
use App\Http\Models\Plan;
use App\Http\Models\Country;
use App\Http\Models\Option;
use App\Http\Models\Children;
use App\Http\Models\User;
use App\Http\Models\School;
use App\Http\Models\News;
use App\Http\Models\Slide;
use App\Http\Models\Location;
use App\Http\Models\Subscription;
use App\Http\Models\ProcurementRequest;
use App\Http\Models\Visit;
use \App\Classes\PayFlowTransaction;
use \App\Classes\Slack;
use \App\Libraries\freshbooks\Client;
use \App\Libraries\freshbooks\Invoice;
use \App\Libraries\freshbooks\Payment;
use Request, Mail, Response, DateTime;

Class PagesController extends Controller{

    public function __construct()
    {
        parent::__construct();
    }

	/**
     * Display the specified page.
     * @author Matrix Infologics Pvt. Ltd.
     * @param  none
     * @return view
     */
	 
	private $currencyRateTable = 'currency_rate';
	private $defaultCurrency = 'USD';

	public function view(){
	
		/* $xml_data  = '<?xml version="1.0" encoding="utf-8"?>  
<request method="invoice.update">  
  <invoice>  
    <invoice_id>19869</invoice_id>            <!-- Invoice to update -->  
	<notes>Due upon receipt.</notes>
    <!-- Remaining arguments same as invoice.create -->  
  </invoice>  
</request>';
	$URL = "https://matrix4578.freshbooks.com/api/2.1/xml-in";
 
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_USERPWD, '184ee2e2239ebbb4b4a016f2aa145f80');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			mail('abhinav1.matrix@gmail.com','invoice update',$output);
	die('done'); */
		//$client= new Client;
		//$client->updateClient();
		//$client->newClient();
		//$invoice= new Invoice;
		//$invoice->updateInvoice();
		//$payment = new Payment;
		//$payment->createPayment();
		//$fb = new easyFreshBooksAPI();
		//$fb->client->email = 'test087@yopmail.com';
		//$fb->client->firstName = 'test fn';
		//$fb->client->lastName = 'test ln';
		//$fb->client->organization = 'test test';
		//$fb->clientCreate();
		//$fb->invoice->status = 'sent';
		//$fb->invoiceUpdate(19869);
		$route = Route::getCurrentRoute()->getPath();
		$route = $route=='/'?'home':$route;
		$pageScripts = array();

		if($route=='home'){
      $slides =  Slide::all();
      $page = $this->getPageContents($route);
			return View('pages.home')->with(compact('pageScripts','page', 'slides'));
		}else{
			$page = $this->getPageContents($route);
			return View('pages.view')->with(compact('pageScripts','page'));
		}
	}


	/**
     * Fetch page contents
     * @author Matrix Infologics Pvt. Ltd.
     * @param  $route sting
     * @return $page array
     */
	public function getPageContents($route){
		$page = Page::getPage($route);
		return $page;
	}
	
	/**
     * Get plans
     * @author Matrix Infologics Pvt. Ltd.
     * @param  none
     * @return view
     */
	public function getSubscriptionPlans(){

		$plans = Plan::all();
		$userCountry = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']) ;
		$currency = Country::getCurrency('country_code', $userCountry);

		//Set default currency to USD.
		$currencyDetails = array(
								'currency' => 'USD',
								'exchange_rate' => '1',
								'sign' => '$'
							);

		if(is_object($currency) && sizeof($currency)>0){

			$currencyExchangeRate = DB::table($this->currencyRateTable)
				->where('currency_from', $this->defaultCurrency)
				->where('currency_to', $currency->currency)
				->first();

			if(is_object($currencyExchangeRate) && isset($currencyExchangeRate->rate)){
				$currencyDetails['currency'] = $currency->currency;
				$currencyDetails['exchange_rate'] = $currencyExchangeRate->rate;
				$currencyDetails['sign'] = Country::currencySign($currency->currency);
			}
		}
		$route = Route::getCurrentRoute()->getPath();
		$page = $this->getPageContents($route);
		return View('pages.plans')->with(compact('page', 'plans', 'currencyDetails'));
	}

	
	public function thanksPage(){

		return View('pages.thankyou');
	}

	/**
     * create recurring profile
     * @author Matrix Infologics Pvt. Ltd.
     * @param  none
     * @return none
     */
	public function createRecurring(){

        $txn = new PayflowTransaction();
        $txn->PARTNER = Config::get('constants.vars.api_partner'); 
        $txn->USER = Config::get('constants.vars.api_merchant'); ;
        $txn->PWD= Config::get('constants.vars.api_password'); ;
        $txn->VENDOR = Config::get('constants.vars.api_vendor'); ; //$txn->USER; //or your vendor name
        $txn->TRXTYPE='R'; // 
        $txn->ACTION='I'; // Specifies Add (A), Modify (M), Cancel (C), Reactivate (R), Inquiry (I), or Payment (P) (To - Retry a previously failed payment).
        $subscriptions = User::where('paypal_profile_id', '!=', '')->get();
            foreach ($subscriptions as $key => $subscription) {
                $txn->ORIGPROFILEID = $subscription->paypal_profile_id;
                $result =  $txn->process();
                if($result['RESULT'] == 0){

                $next_date = substr($result['NEXTPAYMENT'], 2, 2)."-".substr($result['NEXTPAYMENT'], 0, 2)."-".substr($result['NEXTPAYMENT'], 4, 4);
                $d = new DateTime($next_date);
                $formatted_date = $d->format('Y-m-d');
                $date1=date_create(date('Y-m-d'));
                $date2=date_create($formatted_date);
                $diff=date_diff($date2,$date1)->days;

                if($diff <= 5){
                  $subscription->next_payment_date = $next_date;
                   $mail =Mail::send('emails.reminder', ['subscription' => $subscription], function($message) use ($subscription){
                        $message->to($subscription->email)->subject('Subscription Reminder');
                    });
                }
            }
          }
 /*   $subscriptions = Subscription::all();
    foreach ($subscriptions as $key => $subscription) {
        $date1=date_create(date('Y-m-d'));
        $date2=date_create($subscription->subscription_end_date);
        $diff=date_diff($date2,$date1)->d;
        if($diff <= 5){
           $mail =Mail::send('emails.reminder', ['subscription' => $subscription], function($message) use ($subscription){
                $message->to($subscription->user->email)->subject('Subscription Reminder');
            });
        }
    }

    die();*/
		
/*		$txn = new PayflowTransaction();
 
   //
   //these are provided by your payflow reseller
   //
   $txn->PARTNER = Config::get('constants.vars.api_partner'); 
   $txn->USER = Config::get('constants.vars.api_merchant'); ;
   $txn->PWD= Config::get('constants.vars.api_password'); ;
   $txn->VENDOR = Config::get('constants.vars.api_vendor'); ; //$txn->USER; //or your vendor name
   $txn->TRXTYPE='R'; // 
   $txn->ACTION='A'; // Specifies Add (A), Modify (M), Cancel (C), Reactivate (R), Inquiry (I), or Payment (P) (To - Retry a previously failed payment).
   $txn->PROFILENAME= 'Milestones Test Profile'; //  Name for the profile (user-specified). Can be used to search for a profile. Non-unique identifying text name
 
   
    $start_date = date('mdY', time() + 86400);
   
 
    $txn->START=$start_date; 
    // $txn->START=date("mdY");   // Beginning date for the recurring billing cycle used to calculate when payments should be made. Use tomorrow’s date or a date in the future. Format: MMDDYYYY
    $txn->PAYPERIOD= 'MONT'; // Specifies how often the payment occurs:  MONT: Monthly, FRWK: Every Four Weeks, QTER: Quarterly
    $txn->TERM='0';  // A value of 0 means that payments should continue until the profile is deactivated. Or specfiy number 
    //$txn->OPTIONALTRX='S'; // S: a Sale transaction for an initial fee specified by OPTIONALTRXAMT. Defines an optional Authorization for validating the account information or for charging an initial fee. If this transaction fails, then the profile is not generated
   // $txn->OPTIONALTRXAMT='0.00';
    $txn->COMMENT1= 'testing';  // (Optional) Merchant-defined value for reporting and auditing purposes. Limitations: 128 alphanumeric characters
    $txn->COMMENT2= 'testing 2'; // In my case selected plan :::  (Optional) Merchant-defined value for reporting and auditing purposes. Limitations: 128 alphanumeric characters
    $txn->RECURRING ='Y';
    $txn->COMPANYNAME = 'Matrix';
   
    //$txn->OPTIONALTRX = 'S';
    
 
    $txn->TENDER = 'C'; //sets to a cc transaction P for paypal
    $txn->ACCT = '4111111111111111'; //cc number   
  
    
    $txn->AMT = '50.00';
        


    $txn->EXPDATE = '0120'; //$_POST['expiry_mm'].$_POST['expiry_yy']; //4 digit expiration date
    //$txn->CVV2=$_POST['cvv'];

     $txn->FIRSTNAME = 'Abhinav';
     $txn->LASTNAME ='Sogga';
     $txn->STREET = 'Street no 456';
     $txn->CITY ='Washington';
     $txn->COUNTRY = 'US';
     $txn->STATE = 'DC';
     $txn->ZIP = '20500';
     $txn->EMAIL = 'abhinav1.matrix@gmail.com';
         
  
 
 
   $txn->debug = true; //uncomment to see debugging information
   //$txn->avs_addr_required = 1; //set to 1 to enable AVS address checking, 2 to force "Y" response
   //$txn->avs_zip_required = 1; //set to 1 to enable AVS zip code checking, 2 to force "Y" response
   //$txn->cvv2_required = 1; //set to 1 to enable cvv2 checking, 2 to force "Y" response
 //  $txn->fraud_protection = true; //uncomment to enable fraud protection
 

 $result =  $txn->process();
 echo "<pre>";
 print_r($result);
 die();*/

	}

  /**
   * Contact Us  view
   * @author Matrix Infologics Pvt. Ltd.
   * @param  none
   * @return none
   */
    public function contact(){
 /*     $slack = new Slack;
      $webhook = User::getUserMeta(13, 'slack_webhook');
      $channel = User::getUserMeta(13, 'slack_channel');
      $msg = $slack->sendMessage($webhook, $channel, 'This is message from milestones');*/
      $option = new Option;
      return view('pages.contact')->with(compact('option'));      
    }
	
	/**
     * Contact Us 
     * @author Matrix Infologics Pvt. Ltd.
     * @param  multiple
     * @return none
     */
	public function contactUS(){
  	  if (Request::ajax()) {
            $json = array();
            $data = Request::all();
            $mail =Mail::send('emails.contact', ['data' => $data], function($message) use ($data){
                $message->to($data['contact_type'])->subject('Contact Email');
            });
            if($mail){
              $json['success'] = Lang::get('message.contact_request_success');
            }else{
              $json['failed'] = Lang::get('message.contact_request_error');
            }
            return Response::json($json);
  		}
	}

  /**
     * User Dashboard 
     * @author Matrix Infologics Pvt. Ltd.
     * @param  none
     * @return none
     */
  public function myDashboard(){

	$user = User::find(Auth::id());
	$numproReqs = ProcurementRequest::countProRequests(Auth::id(),0); //Get pending requests..
	$numVisits = Visit::upcomingVisitsCount(Auth::id());
    if (Auth::check()){
        $group = User::getGroup($user->id);
        switch ($group) {
          case 'Owner':
			$schools = $user->schools;
			$dashSchoolsDetails = User::getOwnerDashSchoolsDetails($user);
            return view('users.school_owner.dashboard')->with(compact( 'dashSchoolsDetails','numproReqs','numVisits')); 
            break;

          case 'Principal':
            $location = Location::find(User::getSchoolHeadLocation($user->id));
            return view('users.school_head.dashboard')->with(compact('location','numproReqs','numVisits')); 
            break;

          case 'Finance Manager':
            return view('users.finance_manager.dashboard')->with(compact('numproReqs','numVisits')); ; 
            break;

          case 'Teacher':
            return view('users.teachers.dashboard'); 
            break;

          case 'Parent':
            $children = $user->Children;
            $location = array();
            foreach ($children as $key => $value) {
              $locations[] =  $value->location->id;
            }
            $birthdays = Children::dashboardBirthdays(Auth::id());
            $news = News::whereIn('location_id', $locations)->where('visible_to', '!=', 2)->where('status', 1)->orderBy('created_at', 'desc')->take(6)->get();
            return view('users.parents.dashboard')->with(compact('user', 'news','birthdays')); 
            break;

          case 'Sub-Admin':
      			$userId = User::getSubadminOwner(Auth::id());
      			$user = User::findOrFail($userId);
      			$schools = $user->schools;
				$dashSchoolsDetails = User::getOwnerDashSchoolsDetails($user);
            return view('users.school_owner.dashboard')->with(compact( 'dashSchoolsDetails','numproReqs','numVisits','schools')); 
            break;

          default:
            return redirect()->route("dashboard.default");
            break;
        }
    }else{
        return redirect()->route("user.login");
    }
 
  }

  public function birthdayList(){
    $user = User::find(Auth::id());
    $children = $user->Children;
    return view('users.parents.birthday')->with(compact( 'children')); 
  }
  
  public function accessDenied(){
	
		return view('access-denied');
  }

   public function featurePage(){  
    $page = $this->getPageContents('features');
    return view('pages.feature')->with(compact('page'));
  }

  
		
}
?>