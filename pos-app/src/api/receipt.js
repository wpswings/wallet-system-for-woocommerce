import client from './client';

export function getReceipt( orderId ) {
	return client.get( `/receipt/${ orderId }` ).then( ( res ) => res.data );
}
