<?php
require_once MODEL_PATH . 'Member.class.php';

class RateController extends BaseController
{

    public function indexAction()
    {
		// Blank
    }

    public function rateAction()
    {
		require_once(CLASS_PATH . 'Rate.class.php');

		$this->_helper->viewRenderer->setNoRender();
		$memberSession = new Zend_Session_Namespace('member');
		$u_id = $memberSession->member->user_id;
		$state = $this->_request->get('state');
		$r_id = $this->_request->get('r_id');
 	
		$rating = new Rate();
		
		switch($state) {
			case 'rateUpEl':
				$rate = $rating->processRate('up', $r_id, $u_id );
				//Zend_Debug::dump($rate);
 				echo "Rating:";
				echo "<span onclick=\"alert('You have already voted for this resource.');\" class='positive'>{$rate[0]['rate_up']}</span>\n";
				echo "<span onclick=\"alert('You have already voted for this resource.');\" class='negative_off'>{$rate[0]['rate_down']}</span>";
				break;
				
			case 'rateDnEl':
				$rate = $rating->processRate('down', $r_id, $u_id );
 				echo "Rating:";
				echo "<span onclick=\"alert('You have already voted for this resource.');\" class='positive_off'>{$rate[0]['rate_up']}</span>\n";
				echo "<span onclick=\"alert('You have already voted for this resource.');\" class='negative'>{$rate[0]['rate_down']}</span>";
				break;
				
			default:
				echo 'X';
				break;
		}
        
    }
}