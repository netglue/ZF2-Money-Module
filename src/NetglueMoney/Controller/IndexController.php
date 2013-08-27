<?php
namespace NetglueMoney\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
class IndexController extends AbstractActionController {
	
	public function indexAction() {
		$sl = $this->getServiceLocator();
		$cc = $sl->get('CurrencyConverter');
		$view = new ViewModel;
		$view->cc = $cc;
		return $view;
	}
}