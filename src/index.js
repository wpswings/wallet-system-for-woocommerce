import ReactDOM from 'react-dom';
import App from './App';
import Kyc from './Kyc';
import './style.css';
// ReactDOM.render(<App />, document.getElementById('react-app'));
// ReactDOM.render(<Kyc />, document.getElementById('react-kyc-request-app'));

const appEl = document.getElementById('react-app');
if (appEl) {
  ReactDOM.render(<App />, appEl);
}

// render Kyc if div exists
const kycEl = document.getElementById('react-kyc-request-app');
if (kycEl) {
  ReactDOM.render(<Kyc />, kycEl);
}