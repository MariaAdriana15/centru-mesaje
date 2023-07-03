<?php

use UPWPForms\Account;
use UPWPForms\App;

function upwpforms_get_breadcrumb( $folder ) {
	$active_account = Account::get_active_account();

	$account_id = ! empty( $folder['accountId'] ) ? $folder['accountId'] : $active_account['id'];

	if ( empty( $folder ) ) {
		return [];
	}

	$items = [ $folder['id'] => $folder['name'] ];

	if ( in_array( $folder['id'], [
		$active_account['root_id'],
		'computers',
		'shared-drives',
		'shared',
		'recent',
		'starred'
	] ) ) {
		return $items;
	}


	if ( ! isset( $folder['parents'] ) ) {
		$folder = App::instance( $account_id )->get_file_by_id( $folder['id'] );
	}

	if ( ! empty( $folder['parents'] ) ) {

		if ( in_array( 'shared-drives', $folder['parents'] ) ) {
			$items['shared-drives'] = __( 'Shared Drives', 'upload-fields-for-wpforms' );

			$items = array_reverse( $items );

			return $items;
		}

		$item  = App::instance( $account_id )->get_file_by_id( $folder['parents'][0] );
		$items = array_merge( upwpforms_get_breadcrumb( $item ), $items );
	}

	return $items;
}