import { useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { checkout } from '../api/checkout';
import { getErrorMessage } from '../api/errors';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { ROUTES } from '../routes';

// Card isn't supported yet — only wallet and cash. payment_split still sends
// card: 0 since the checkout endpoint's shape includes it; re-add a Card
// field here if/when a real card flow exists.
export default function PaymentScreen() {
	const { items, customer, total, clearCart } = useCart();
	const { session, currencySymbol } = useAuth();
	const navigate = useNavigate();

	const [ wallet, setWallet ] = useState( '0' );
	const [ cash, setCash ] = useState( '0' );
	const [ error, setError ] = useState( '' );
	const [ busy, setBusy ] = useState( false );

	const paid = useMemo(
		() => ( parseFloat( wallet ) || 0 ) + ( parseFloat( cash ) || 0 ),
		[ wallet, cash ]
	);
	const remaining = total - paid;
	const walletExceedsBalance = ( parseFloat( wallet ) || 0 ) > ( customer?.balance ?? 0 );

	function fillRemainingWithCash() {
		setCash( Math.max( 0, total - ( parseFloat( wallet ) || 0 ) ).toFixed( 2 ) );
	}

	async function handleCharge() {
		setError( '' );
		if ( Math.abs( remaining ) > 0.01 ) {
			setError( 'Payment split must add up to the total.' );
			return;
		}
		setBusy( true );
		try {
			const result = await checkout( {
				customer_id: customer.user_id,
				items: items.map( ( item ) => ( { product_id: item.product_id, quantity: item.quantity } ) ),
				payment_split: {
					wallet: parseFloat( wallet ) || 0,
					cash: parseFloat( cash ) || 0,
					card: 0,
				},
				register_session_id: session.sessionId,
			} );
			clearCart();
			navigate( ROUTES.receipt( result.order_id ) );
		} catch ( err ) {
			setError( getErrorMessage( err, 'Checkout failed.' ) );
		} finally {
			setBusy( false );
		}
	}

	if ( ! customer || items.length === 0 ) {
		return (
			<div className="screen screen-centered">
				<div className="card">
					<p>Nothing to charge — go back and add items and a customer.</p>
					<button onClick={ () => navigate( ROUTES.sales ) }>Back to Sales</button>
				</div>
			</div>
		);
	}

	return (
		<div className="screen screen-centered">
			<div className="card">
				<h1>Payment</h1>
				<p className="total-display">
					Total due: { currencySymbol }
					{ total.toFixed( 2 ) }
				</p>
				<p>
					Customer wallet balance: { currencySymbol }
					{ customer.balance.toFixed( 2 ) }
				</p>

				<label>
					Wallet
					<input type="number" min="0" step="0.01" value={ wallet } onChange={ ( e ) => setWallet( e.target.value ) } />
				</label>
				{ walletExceedsBalance && <p className="error">Wallet amount exceeds customer's balance.</p> }

				<label>
					Cash
					<input type="number" min="0" step="0.01" value={ cash } onChange={ ( e ) => setCash( e.target.value ) } />
				</label>

				<button type="button" className="secondary" onClick={ fillRemainingWithCash }>
					Fill remaining with cash
				</button>

				<p className={ Math.abs( remaining ) > 0.01 ? 'error' : 'ok' }>
					Remaining: { currencySymbol }
					{ remaining.toFixed( 2 ) }
				</p>

				{ error && <p className="error">{ error }</p> }

				<button className="primary large" disabled={ busy || Math.abs( remaining ) > 0.01 } onClick={ handleCharge }>
					{ busy ? 'Processing…' : 'Complete Sale' }
				</button>
				<button className="secondary" onClick={ () => navigate( ROUTES.sales ) }>
					Back
				</button>
			</div>
		</div>
	);
}
