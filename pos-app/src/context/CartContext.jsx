import { createContext, useCallback, useContext, useMemo, useState } from 'react';

const CartContext = createContext( null );

export function CartProvider( { children } ) {
	const [ items, setItems ] = useState( [] );
	const [ customer, setCustomer ] = useState( null );

	const addItem = useCallback( ( product ) => {
		setItems( ( current ) => {
			const existing = current.find( ( item ) => item.product_id === product.product_id );
			if ( existing ) {
				return current.map( ( item ) =>
					item.product_id === product.product_id ? { ...item, quantity: item.quantity + 1 } : item
				);
			}
			return [
				...current,
				{ product_id: product.product_id, name: product.name, price: product.price, quantity: 1 },
			];
		} );
	}, [] );

	const updateQuantity = useCallback( ( productId, quantity ) => {
		setItems( ( current ) =>
			quantity <= 0
				? current.filter( ( item ) => item.product_id !== productId )
				: current.map( ( item ) => ( item.product_id === productId ? { ...item, quantity } : item ) )
		);
	}, [] );

	const removeItem = useCallback( ( productId ) => {
		setItems( ( current ) => current.filter( ( item ) => item.product_id !== productId ) );
	}, [] );

	const clearCart = useCallback( () => {
		setItems( [] );
		setCustomer( null );
	}, [] );

	const total = useMemo(
		() => items.reduce( ( sum, item ) => sum + item.price * item.quantity, 0 ),
		[ items ]
	);

	return (
		<CartContext.Provider
			value={ { items, addItem, updateQuantity, removeItem, clearCart, customer, setCustomer, total } }
		>
			{ children }
		</CartContext.Provider>
	);
}

export function useCart() {
	return useContext( CartContext );
}
