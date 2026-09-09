import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import { BASENAME } from './routes';
import './styles.css';

// basename scopes all routing to wherever the admin-created "Wallet POS"
// page actually lives (injected by PHP as window.WSFW_POS_BASENAME — see
// routes.js), so navigation never leaks out to the domain root — e.g.
// navigate(ROUTES.sales) becomes /wallet-pos/wallet-pos-sales, not
// https://the-site.com/wallet-pos-sales.
ReactDOM.render(
	<React.StrictMode>
		<BrowserRouter basename={ BASENAME }>
			<App />
		</BrowserRouter>
	</React.StrictMode>,
	document.getElementById( 'root' )
);
