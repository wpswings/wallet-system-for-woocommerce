import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { getReceipt } from '../api/receipt';
import { getErrorMessage } from '../api/errors';
import { useAuth } from '../context/AuthContext';
import { ROUTES } from '../routes';

export default function ReceiptScreen() {
	const { orderId } = useParams();
	const [ receipt, setReceipt ] = useState( null );
	const [ error, setError ] = useState( '' );
	const { currencySymbol } = useAuth();
	const navigate = useNavigate();

	useEffect( () => {
		getReceipt( orderId )
			.then( setReceipt )
			.catch( ( err ) => setError( getErrorMessage( err, 'Could not load receipt.' ) ) );
	}, [ orderId ] );

	if ( error ) {
		return (
			<div className="screen screen-centered">
				<p className="error">{ error }</p>
			</div>
		);
	}

	if ( ! receipt ) {
		return (
			<div className="screen screen-centered">
				<p>Loading receipt…</p>
			</div>
		);
	}

	return (
		<div className="screen screen-centered">
			<div className="card receipt">
				<h1>Receipt #{ receipt.order_number }</h1>
				<p>{ receipt.date }</p>
				<ul>
					{ receipt.items.map( ( item, index ) => (
						<li key={ index }>
							{ item.quantity } × { item.name } — { currencySymbol }
							{ item.total.toFixed( 2 ) }
						</li>
					) ) }
				</ul>
				<p>
					Subtotal: { currencySymbol }
					{ receipt.subtotal.toFixed( 2 ) }
				</p>
				<p>
					Tax: { currencySymbol }
					{ receipt.tax_total.toFixed( 2 ) }
				</p>
				<p className="total-display">
					Total: { currencySymbol }
					{ receipt.total.toFixed( 2 ) }
				</p>
				{ receipt.payment_split && (
					<p>
						Paid — Wallet: { currencySymbol }
						{ receipt.payment_split.wallet } · Cash: { currencySymbol }
						{ receipt.payment_split.cash }
					</p>
				) }
				<p>
					Customer: { receipt.customer.name } ({ receipt.customer.email })
				</p>
				<p>
					Updated wallet balance:{ ' ' }
					{ receipt.wallet_balance !== null ? `${ currencySymbol }${ receipt.wallet_balance.toFixed( 2 ) }` : '—' }
				</p>

				<button className="primary" onClick={ () => window.print() }>
					Print
				</button>
				<button className="secondary" onClick={ () => navigate( ROUTES.sales ) }>
					New Sale
				</button>
			</div>
		</div>
	);
}
