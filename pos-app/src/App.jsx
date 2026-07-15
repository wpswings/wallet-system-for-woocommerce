import { Navigate, Route, Routes } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { CartProvider } from './context/CartContext';
import { ROUTES } from './routes';
import LoginScreen from './screens/LoginScreen';
import SalesScreen from './screens/SalesScreen';
import PaymentScreen from './screens/PaymentScreen';
import ReceiptScreen from './screens/ReceiptScreen';
import RegisterCloseScreen from './screens/RegisterCloseScreen';

function RequireSession( { children } ) {
	const { auth, session } = useAuth();
	// No auth, or auth without a session (e.g. localStorage was cleared
	// mid-shift) — either way, the only way to get a session is through
	// LoginScreen, which auto-opens one right after authenticating.
	return auth && session ? children : <Navigate to={ ROUTES.login } replace />;
}

function AppRoutes() {
	return (
		<Routes>
			<Route path={ ROUTES.login } element={ <LoginScreen /> } />
			<Route
				path={ ROUTES.registerClose }
				element={
					<RequireSession>
						<RegisterCloseScreen />
					</RequireSession>
				}
			/>
			<Route
				path={ ROUTES.sales }
				element={
					<RequireSession>
						<SalesScreen />
					</RequireSession>
				}
			/>
			<Route
				path={ ROUTES.payment }
				element={
					<RequireSession>
						<PaymentScreen />
					</RequireSession>
				}
			/>
			<Route
				path={ ROUTES.receiptPattern }
				element={
					<RequireSession>
						<ReceiptScreen />
					</RequireSession>
				}
			/>
			<Route path="*" element={ <Navigate to={ ROUTES.login } replace /> } />
		</Routes>
	);
}

export default function App() {
	return (
		<AuthProvider>
			<CartProvider>
				<AppRoutes />
			</CartProvider>
		</AuthProvider>
	);
}
