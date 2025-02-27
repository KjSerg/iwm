<?php
namespace InvoiceWM\controller;

use InvoiceWM\models\BillModel;
use InvoiceWM\views\Bills;

class BillController {
	public static function render_bills(): void {

		$bills = BillModel::get_bills();
		$view = new Bills();
		$view->render( $bills );
	}
}

?>
