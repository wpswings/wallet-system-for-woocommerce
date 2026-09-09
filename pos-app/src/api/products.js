import client from './client';

export function searchProducts( query ) {
	return client.get( '/products/search', { params: { query } } ).then( ( res ) => res.data );
}
