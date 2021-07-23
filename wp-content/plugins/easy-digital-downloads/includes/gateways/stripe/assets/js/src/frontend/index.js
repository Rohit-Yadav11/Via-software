/* global Stripe, edd_stripe_vars */

/**
 * Internal dependencies
 */
import './../../../css/src/frontend.scss';
import { domReady, apiRequest, generateNotice } from 'utils';

import {
	setupCheckout,
	setupProfile,
	setupPaymentHistory,
	setupBuyNow,
	setupDownloadPRB,
	setupCheckoutPRB,
} from 'frontend/payment-forms';

import {
	paymentMethods,
} from 'frontend/components/payment-methods';

import {
	mountCardElement,
	createPaymentForm as createElementsPaymentForm,
	getBillingDetails,
	getPaymentMethod,
	confirm as confirmIntent,
	handle as handleIntent,
	retrieve as retrieveIntent,
} from 'frontend/stripe-elements';
// eslint-enable @wordpress/dependency-group

( () => {
	try {
		window.eddStripe = new Stripe( edd_stripe_vars.publishable_key );

		// Alias some functionality for external plugins.
		window.eddStripe._plugin = {
			domReady,
			apiRequest,
			generateNotice,
			mountCardElement,
			createElementsPaymentForm,
			getBillingDetails,
			getPaymentMethod,
			confirmIntent,
			handleIntent,
			retrieveIntent,
			paymentMethods,
		};

		// Setup frontend components when DOM is ready.
		domReady(
			setupCheckout,
			setupProfile,
			setupPaymentHistory,
			setupBuyNow,
			setupDownloadPRB,
			setupCheckoutPRB,
		);
	} catch ( error ) {
		alert( error.message );
	}
} )();
