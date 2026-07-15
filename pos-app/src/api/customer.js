import client from './client';

export function searchCustomers( query ) {
	return client.get( '/customer/search', { params: { query } } ).then( ( res ) => res.data );
}
