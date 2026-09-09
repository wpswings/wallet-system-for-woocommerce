import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { login } from '../api/auth';
import { openRegister } from '../api/register';
import { getErrorMessage } from '../api/errors';
import { useAuth } from '../context/AuthContext';
import { ROUTES } from '../routes';

export default function LoginScreen() {
	const [ loginField, setLoginField ] = useState( '' );
	const [ pin, setPin ] = useState( '' );
	const [ terminalId, setTerminalId ] = useState( () => localStorage.getItem( 'wsfw_pos_terminal_id' ) || '' );
	const [ error, setError ] = useState( '' );
	const [ busy, setBusy ] = useState( false );
	const { setAuth, setSession } = useAuth();
	const navigate = useNavigate();

	async function handleSubmit( event ) {
		event.preventDefault();
		setError( '' );
		setBusy( true );
		try {
			const result = await login( { login: loginField, pin, terminalId } );
			setAuth( {
				username: result.username,
				password: result.password,
				userId: result.user_id,
				currencySymbol: result.currency_symbol,
			} );
			localStorage.setItem( 'wsfw_pos_terminal_id', terminalId );

			// No manual "Open Register" step — each terminal gets its own
			// register (named after its terminal id), opened automatically
			// with opening_cash 0. If a prior session on this terminal was
			// never closed, the API resumes it instead of erroring.
			const registerName = terminalId.trim() || 'Default Register';
			const registerResult = await openRegister( { register_name: registerName, opening_cash: 0 } );
			setSession( {
				sessionId: registerResult.session_id,
				registerId: registerResult.register_id,
				registerName: registerResult.register_name,
			} );

			navigate( ROUTES.sales );
		} catch ( err ) {
			setError( getErrorMessage( err, 'Login failed.' ) );
		} finally {
			setBusy( false );
		}
	}

	return (
		<div className="screen screen-centered">
			<form className="card" onSubmit={ handleSubmit }>
				<h1>POS Login</h1>
				<label>
					Username or email
					<input value={ loginField } onChange={ ( e ) => setLoginField( e.target.value ) } required />
				</label>
				<label>
					PIN
					<input
						type="password"
						inputMode="numeric"
						maxLength={ 6 }
						value={ pin }
						onChange={ ( e ) => setPin( e.target.value ) }
						required
					/>
				</label>
				<label>
					Terminal ID
					<input value={ terminalId } onChange={ ( e ) => setTerminalId( e.target.value ) } placeholder="register-1" />
				</label>
				{ error && <p className="error">{ error }</p> }
				<button type="submit" disabled={ busy }>
					{ busy ? 'Logging in…' : 'Log in' }
				</button>
			</form>
		</div>
	);
}
