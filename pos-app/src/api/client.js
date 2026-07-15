import axios from 'axios';

// Relative path so the Vite dev proxy (see vite.config.js) forwards to your
// WP site with no CORS setup needed. Override for a production build that's
// hosted on a different origin than the WP site via VITE_API_BASE_URL.
const API_BASE = import.meta.env.VITE_API_BASE_URL || '/wp-json/pos/v1';

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
