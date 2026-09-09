import axios from 'axios';

// window.WSFW_POS_API_BASE (injected by class-wallet-system-for-woocommerce-pos-page.php
// via rest_url()) is the real REST base for the current site — required
// whenever WordPress isn't installed at the domain root, since a hardcoded
// '/wp-json/pos/v1' always resolves from the domain root regardless of where
// WP actually lives. Falls back to the relative path so the Vite dev proxy
// (see vite.config.js) still works with no WordPress page involved, and to
// VITE_API_BASE_URL for a production build hosted on a different origin.
const API_BASE = window.WSFW_POS_API_BASE || import.meta.env.VITE_API_BASE_URL || '/wp-json/pos/v1';

const client = axios.create( { baseURL: API_BASE } );

client.interceptors.request.use( ( config ) => {
	const auth = JSON.parse( localStorage.getItem( 'wsfw_pos_auth' ) || 'null' );
	if ( auth?.username && auth?.password ) {
		config.headers.Authorization = 'Basic ' + btoa( `${ auth.username }:${ auth.password }` );
	}
	return config;
} );

client.interceptors.response.use(
	( response ) => response,
	( error ) => {
		if ( error?.response?.status === 401 ) {
			localStorage.removeItem( 'wsfw_pos_auth' );
			localStorage.removeItem( 'wsfw_pos_session' );
		}
		return Promise.reject( error );
	}
);

export default client;
