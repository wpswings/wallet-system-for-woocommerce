import { createContext, useCallback, useContext, useState } from 'react';

const AuthContext = createContext( null );

function readStored( key ) {
	try {
		return JSON.parse( localStorage.getItem( key ) || 'null' );
	} catch {
		return null;
	}
}

export function AuthProvider( { children } ) {
	const [ auth, setAuthState ] = useState( () => readStored( 'wsfw_pos_auth' ) );
	const [ session, setSessionState ] = useState( () => readStored( 'wsfw_pos_session' ) );

	const setAuth = useCallback( ( value ) => {
		setAuthState( value );
		if ( value ) {
			localStorage.setItem( 'wsfw_pos_auth', JSON.stringify( value ) );
		} else {
			localStorage.removeItem( 'wsfw_pos_auth' );
		}
	}, [] );

	const setSession = useCallback( ( value ) => {
		setSessionState( value );
		if ( value ) {
			localStorage.setItem( 'wsfw_pos_session', JSON.stringify( value ) );
		} else {
			localStorage.removeItem( 'wsfw_pos_session' );
		}
	}, [] );

	const logout = useCallback( () => {
		setAuth( null );
		setSession( null );
	}, [ setAuth, setSession ] );

	// Falls back to '$' for a session started before currency_symbol existed
	// in the login response (stale localStorage from an older build).
	const currencySymbol = auth?.currencySymbol || '$';

	return (
		<AuthContext.Provider value={ { auth, setAuth, session, setSession, logout, currencySymbol } }>
			{ children }
		</AuthContext.Provider>
	);
}

export function useAuth() {
	return useContext( AuthContext );
}
