<?php
/**
 * Represents a transaction done by User.
 * Only records what happened and affects nothing.
 */
class Transaction extends AppModel{
	public $useTable = 'transaction';
}
