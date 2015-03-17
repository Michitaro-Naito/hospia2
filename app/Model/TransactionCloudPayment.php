<?php
/**
 * Represents a transaction done by User.
 * Only records what happened and affects nothing.
 */
class TransactionCloudPayment extends AppModel{
	public $useTable = 'transaction_cloud_payment';
}
