import { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { searchProducts } from '../api/products';
import { searchCustomers } from '../api/customer';
import { getErrorMessage } from '../api/errors';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { ROUTES } from '../routes';

const SEARCH_DEBOUNCE_MS = 300;

export default function SalesScreen() {
	const [ productQuery, setProductQuery ] = useState( '' );
	const [ products, setProducts ] = useState( [] );
	const [ customerQuery, setCustomerQuery ] = useState( '' );
	const [ customerResults, setCustomerResults ] = useState( [] );
	const [ error, setError ] = useState( '' );
	const { items, addItem, updateQuantity, removeItem, customer, setCustomer, total } = useCart();
	const { session, logout, currencySymbol } = useAuth();
	const navigate = useNavigate();

	// Guards against out-of-order responses: if requests fire faster than
	// they resolve (fast typing/clicking), an older, slower response could
	// otherwise land after a newer one and silently replace the current
	// results with stale data — this ref makes each response check it's
	// still the most recent request before applying itself.
	const productRequestId = useRef( 0 );
	const customerRequestId = useRef( 0 );

	useEffect( () => {
		const requestId = ++productRequestId.current;
		const timer = setTimeout( async () => {
			try {
				const result = await searchProducts( productQuery );
				if ( requestId === productRequestId.current ) {
					setProducts( result.products );
				}
			} catch ( err ) {
				if ( requestId === productRequestId.current ) {
					setError( getErrorMessage( err, 'Could not search products.' ) );
				}
			}
		}, SEARCH_DEBOUNCE_MS );

		return () => clearTimeout( timer );
	}, [ productQuery ] );

	useEffect( () => {
		if ( customerQuery.trim().length < 2 ) {
			setCustomerResults( [] );
			return;
		}

		const requestId = ++customerRequestId.current;
		const timer = setTimeout( async () => {
			try {
				const result = await searchCustomers( customerQuery );
				if ( requestId === customerRequestId.current ) {
					setCustomerResults( result.customers );
				}
			} catch ( err ) {
				if ( requestId === customerRequestId.current ) {
					setError( getErrorMessage( err, 'Could not search customers.' ) );
				}
			}
		}, SEARCH_DEBOUNCE_MS );

		return () => clearTimeout( timer );
	}, [ customerQuery ] );

	function goToPayment() {
		if ( ! customer ) {
			setError( 'Attach a customer before charging.' );
			return;
		}
		if ( items.length === 0 ) {
			setError( 'Add at least one item before charging.' );
			return;
		}
		setError( '' );
		navigate( ROUTES.payment );
	}

	return (
		<div className="screen sales-screen">
			<header className="topbar">
				<span className="topbar-register">Register: { session?.registerName }</span>
				<div>
					<button onClick={ () => navigate( ROUTES.registerClose ) }>Close Register</button>
					<button className="secondary" onClick={ logout }>
						Log out
					</button>
				</div>
			</header>

			{ error && <p className="error sales-error">{ error }</p> }

			<div className="sales-columns">
				<section className="products-column">
					<input
						className="search-input"
						placeholder="Search products by name or SKU…"
						value={ productQuery }
						onChange={ ( e ) => setProductQuery( e.target.value ) }
					/>
					<div className="product-grid">
						{ products.map( ( product ) => {
							const cartItem = items.find( ( item ) => item.product_id === product.product_id );
							const quantity = cartItem ? cartItem.quantity : 0;

							return (
								<div key={ product.product_id } className="product-tile">
									{ product.image ? (
										<img className="product-image" src={ product.image } alt="" />
									) : (
										<span className="product-image product-image-placeholder" aria-hidden="true" />
									) }
									<span className="product-name">{ product.name }</span>
									<span className="product-price">
										{ currencySymbol }
										{ product.price.toFixed( 2 ) }
									</span>
									{ quantity > 0 ? (
										<div className="product-stepper">
											<button
												type="button"
												className="stepper-button"
												onClick={ () => updateQuantity( product.product_id, quantity - 1 ) }
												aria-label={ `Decrease ${ product.name } quantity` }
											>
												−
											</button>
											<span className="stepper-value">{ quantity }</span>
											<button
												type="button"
												className="stepper-button"
												onClick={ () => addItem( product ) }
												aria-label={ `Increase ${ product.name } quantity` }
											>
												+
											</button>
										</div>
									) : (
										<button type="button" className="product-add" onClick={ () => addItem( product ) }>
											Add
										</button>
									) }
								</div>
							);
						} ) }
						{ products.length === 0 && <p className="empty-hint">No products found.</p> }
					</div>
				</section>

				<section className="cart-column">
					<div className="customer-block">
						{ customer ? (
							<div className="customer-attached">
								<div>
									<strong>{ customer.name }</strong>
									<span className="customer-balance">
										Balance: { currencySymbol }
										{ customer.balance.toFixed( 2 ) }
									</span>
								</div>
								<button className="secondary" onClick={ () => setCustomer( null ) }>
									Change
								</button>
							</div>
						) : (
							<>
								<input
									className="search-input"
									placeholder="Search customer by name, email, or phone…"
									value={ customerQuery }
									onChange={ ( e ) => setCustomerQuery( e.target.value ) }
								/>
								<ul className="customer-results">
									{ customerResults.map( ( result ) => (
										<li key={ result.user_id }>
											<button
												onClick={ () => {
													setCustomer( result );
													setCustomerResults( [] );
													setCustomerQuery( '' );
												} }
											>
												<span>{ result.name }</span>
												<span className="customer-result-meta">
													{ result.email } · Balance: { currencySymbol }
													{ result.balance.toFixed( 2 ) }
												</span>
											</button>
										</li>
									) ) }
								</ul>
							</>
						) }
					</div>

					<ul className="cart-list">
						{ items.map( ( item ) => (
							<li key={ item.product_id } className="cart-item">
								<span className="cart-item-name">{ item.name }</span>
								<input
									type="number"
									min="0"
									className="cart-item-qty"
									value={ item.quantity }
									onChange={ ( e ) => updateQuantity( item.product_id, parseInt( e.target.value, 10 ) || 0 ) }
								/>
								<span className="cart-item-total">
									{ currencySymbol }
									{ ( item.price * item.quantity ).toFixed( 2 ) }
								</span>
								<button className="cart-item-remove" onClick={ () => removeItem( item.product_id ) } aria-label={ `Remove ${ item.name }` }>
									×
								</button>
							</li>
						) ) }
						{ items.length === 0 && <p className="empty-hint">Cart is empty.</p> }
					</ul>

					<div className="cart-total">
						Total: { currencySymbol }
						{ total.toFixed( 2 ) }
					</div>
					<button className="primary large" onClick={ goToPayment }>
						Charge
					</button>
				</section>
			</div>
		</div>
	);
}
