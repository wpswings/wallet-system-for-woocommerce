import client from './client';

export function getBalance( userId ) {
	return client.get( `/wallet/balance/${ userId }` ).then( ( res ) => res.data );
}

export function getHistory( userId ) {
	return client.get( `/wallet/history/${ userId }` ).then( ( res ) => res.data );
}
