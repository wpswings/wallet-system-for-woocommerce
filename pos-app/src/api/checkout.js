import client from './client';

export function checkout( payload ) {
	return client.post( '/checkout', payload ).then( ( res ) => res.data );
}
