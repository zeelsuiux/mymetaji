<?php
/**
 * Razorpay credentials.
 *
 * The values below are TEST keys. They work end to end with no real money
 * moving, which is what the site should be running while the flow is being
 * tried out. To go live, replace both keys with the live pair from
 * Razorpay Dashboard > Settings > API keys and set RAZORPAY_LIVE_MODE to true.
 *
 * The key secret is only ever read on the server. It is never sent to the
 * browser: checkout.js receives the key id alone, and the browser's payment
 * proof is checked here against Razorpay before anything is recorded.
 *
 * RAZORPAY_WEBHOOK_SECRET is separate and stays empty until a webhook is
 * registered in the dashboard. api/payment_webhook.php refuses to run without
 * it rather than accepting unverified callbacks.
 */
define('RAZORPAY_KEY_ID', 'rzp_test_TcKpQIbTngxe2f');
define('RAZORPAY_KEY_SECRET', 'Azrz18TT5vNa9027G1Vxikak');
define('RAZORPAY_CURRENCY', 'INR');
define('RAZORPAY_LIVE_MODE', false);
define('RAZORPAY_WEBHOOK_SECRET', '');

// Fallback amount for the shop-side licence renewal, which predates the pricing
// tiers and still reads the plan amount off the shop's own licence file.
define('RAZORPAY_PLAN_AMOUNT', 1.00);
