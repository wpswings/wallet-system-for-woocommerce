import client from './client';

export function login( { login, pin, terminalId } ) {
	return client
		.post( '/staff/login', { login, pin, terminal_id: terminalId } )
		.then( ( res ) => res.data );
}
