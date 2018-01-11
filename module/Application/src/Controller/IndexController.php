<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController,
	Zend\View\Model\ViewModel,
	Zend\Console\Request as ConsoleRequest;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * sortCardsAction function permettant de trier une main de carte obtenue à partir d'un web service
     * @return void
     */
    public function sortCardsAction(){

    	$request = $this->getRequest();
    	if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

	    // retrieve datas from webservices
		$url = 'https://recrutement.local-trust.com/test/cards/57187b7c975adeb8520a283c';
		$json = file_get_contents($url);

		// init variables 
		$cards = array();
		$categoryOrder = array();
		$valueOrder = array();
		print_r('----- Initial datas ---'."\n");
		print_r( $json. "\n");
		print_r('----- /End Initial datas ---'."\n");
		print_r("\n \n \n \n");
		if( $json !== FALSE ){
			$json_in_array = json_decode($json, TRUE);

			$datas = $json_in_array['data'];
			$exerciceId = $json_in_array['exerciceId'];
			if( isset( $datas ) ){
				$cards = $datas['cards'];
				$categoryOrder = $datas['categoryOrder'];
				$valueOrder = $datas['valueOrder'];

				// Change cards category and value by their corresponding key from array
				$cards_in_numeric = array();
				foreach ( $cards as $key => $card ) {
					$cards_in_numeric[] = array( 'category' => array_search( $card['category'], $categoryOrder), 
											   'value' => array_search( $card['value'],  $valueOrder) 
										);
				}
				// /End Change cards category and value by their corresponding key from array


				// Preparing to sorts cards_in_numeric
				foreach ($cards_in_numeric as $key1 => $card_in_numeric) {
				    $category[$key1]  = $card_in_numeric['category'];
				    $value[$key1] = $card_in_numeric['value'];
				}
				array_multisort($category, SORT_ASC, $value, SORT_ASC, $cards_in_numeric);
				// /End Preparing to sorts cards_in_numeric


				// Put back the cards with the string values
				$cards_ordered = array();
				foreach ($cards_in_numeric as $k => $c) {
					$cards_ordered[] = array( 'category' => $categoryOrder[$c['category']], 'value' => $valueOrder[$c['value']] );
				}

				// Displaying the final json value of this exercise
				$jsonFinalValue = json_encode (array( "cards" => $cards_ordered ) );
				print_r('----- ordered cards ---'."\n");
				print_r($jsonFinalValue. "\n");
				print_r('----- /End ordered cards ---'."\n");

				// Check if our solution is good
				$return = $this->checkMySolution($exerciceId, $jsonFinalValue);
				
				exit();

			}else{
				print_r('No datas exist from this service '."\n");
				exit();
			}
		}else{
			print_r('Service no available, try again '."\n");
			exit();
		}
    }
    /**
     * checkMySolution - Vérifie avec un web service si la main triée est juste ou pas
     * @param   string $exerciceId - l'id de l'exercice 
     * @param   string $json_data  - la main triée envoyé
     * @return void - informe si la main est bien triée ou pas
     */
    public function checkMySolution($exerciceId, $json_data){
    	$url = 'https://recrutement.local-trust.com/test/'.$exerciceId;
    	$return = $this->curlExec( $url, $json_data  );
    	if( $return == 200 ){
			print_r('Le code renvoyé est 200, la main est bien triée'."\n");
		}else{
			print_r('Le code renvoyé est '.$return.', la main n\'est pas triée'."\n");
		}
    }

    /**
     * curlExec - lit le service avec post
     * @param   string $url      url du web service
     * @param   string $json_data  - la valeur du body
     * @return  int
     */
    public static function curlExec($url, $json_data ){
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER  , true);  // we want headers
		$output = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $http_code;
    }

}
