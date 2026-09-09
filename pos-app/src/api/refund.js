import client from './client';

export function refund( payload ) {
	return client.post( '/refund', payload ).then( ( res ) => res.data );
}
