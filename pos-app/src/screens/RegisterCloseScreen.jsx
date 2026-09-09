import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { closeRegister, getSession } from '../api/register';
import { getErrorMessage } from '../api/errors';
import { useAuth } from '../context/AuthContext';
import { ROUTES } from '../routes';

export default function RegisterCloseScreen() {
	const { session, setSession, logout, currencySymbol } = useAuth();
	const [ summary, setSummary ] = useState( null );
	const [ closingCash, setClosingCash ] = useState( '' );
	const [ notes, setNotes ] = useState( '' );
	const [ result, setResult ] = useState( null );
	const [ error, setError ] = useState( '' );
	const [ busy, setBusy ] = useState( false );
	const navigate = useNavigate();

	useEffect( () => {
		if ( ! session ) {
			return;
		}
		getSession( session.sessionId )
			.then( setSummary )
			.catch( ( err ) => setError( getErrorMessage( err, 'Could not load session.' ) ) );
	}, [ session ] );

	async function handleClose( event ) {
		event.preventDefault();
		setError( '' );
		setBusy( true );
		try {
			const response = await closeRegister( {
				session_id: session.sessionId,
				closing_cash: parseFloat( closingCash ) || 0,
				notes,
			} );
			setResult( response );
			setSession( null );
		} catch ( err ) {
			setError( getErrorMessage( err, 'Could not close register.' ) );
		} finally {
			setBusy( false );
		}
	}

	if ( result ) {
		return (
			<div className="screen screen-centered">
				<div className="card">
					<h1>Register Closed</h1>
					<p>
						Expected cash: { currencySymbol }
						{ result.expected_cash.toFixed( 2 ) }
					</p>
					<p>
						Counted cash: { currencySymbol }
						{ result.closing_cash.toFixed( 2 ) }
					</p>
					<p className={ Math.abs( result.discrepancy ) > 0.01 ? 'error' : 'ok' }>
						Discrepancy: { currencySymbol }
						{ result.discrepancy.toFixed( 2 ) }
					</p>
					<button className="primary" onClick={ logout }>
						Log out
					</button>
				</div>
			</div>
		);
	}

	return (
		<div className="screen screen-centered">
			<form className="card" onSubmit={ handleClose }>
				<h1>Close Register</h1>
				{ summary && (
					<>
						<p>
							Opening balance: { currencySymbol }
							{ summary.opening_balance.toFixed( 2 ) }
						</p>
						<p>
							Cash sales: { currencySymbol }
							{ summary.cash_sales_total.toFixed( 2 ) }
						</p>
						<p>
							Wallet sales: { currencySymbol }
							{ summary.wallet_sales_total.toFixed( 2 ) }
						</p>
						<p>
							Expected cash in drawer: { currencySymbol }
							{ summary.expected_cash.toFixed( 2 ) }
						</p>
					</>
				) }
				<label>
					Counted cash
					<input
						type="number"
						min="0"
						step="0.01"
						value={ closingCash }
						onChange={ ( e ) => setClosingCash( e.target.value ) }
						required
					/>
				</label>
				<label>
					Notes
					<textarea value={ notes } onChange={ ( e ) => setNotes( e.target.value ) } />
				</label>
				{ error && <p className="error">{ error }</p> }
				<button type="submit" disabled={ busy }>
					{ busy ? 'Closing…' : 'Close Register' }
				</button>
				<button type="button" className="secondary" onClick={ () => navigate( ROUTES.sales ) }>
					Back
				</button>
			</form>
		</div>
	);
}
