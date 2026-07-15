import client from './client';

export function openRegister( payload ) {
	return client.post( '/register/open', payload ).then( ( res ) => res.data );
}

export function closeRegister( payload ) {
	return client.post( '/register/close', payload ).then( ( res ) => res.data );
}

export function getSession( sessionId ) {
	return client.get( `/register/session/${ sessionId }` ).then( ( res ) => res.data );
}
