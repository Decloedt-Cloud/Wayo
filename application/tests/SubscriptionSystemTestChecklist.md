# Subscription System Test Checklist

## Pre-Implementation Tests (Current System)

### 1. Current Behavior Verification
- [ ] Admin with active trial can access all features
- [ ] Admin with expired trial gets blocked, redirected to payment
- [ ] Admin with paid subscription can access all features
- [ ] Admin with expired paid subscription gets blocked
- [ ] Invoice auto-creation works when subscription expires
- [ ] Stripe payment flow completes successfully
- [ ] School status updates correctly after payment

### 2. Edge Cases
- [ ] Multiple admins accessing same school simultaneously
- [ ] Payment failure handling
- [ ] Double payment prevention
- [ ] Session timeout during payment

## Post-Implementation Tests

### 3. Database Migration Tests
- [ ] Run SQL migrations without errors
- [ ] Verify new columns exist in `schools` table:
  - `subscription_status` (ENUM)
  - `trial_end` (INT)
- [ ] Verify new columns exist in `invoices` table:
  - `period_start` (INT)
  - `period_end` (INT)
  - `stripe_payment_intent_id` (VARCHAR)
  - `stripe_invoice_id` (VARCHAR)
  - `paid_at` (INT)
- [ ] Verify `subscription_plans` table exists with default plan

### 4. SubscriptionService Tests

#### Status Logic
- [ ] `getSubscriptionStatus()` returns correct status for trialing school
- [ ] `getSubscriptionStatus()` returns correct status for active school
- [ ] `getSubscriptionStatus()` returns correct status for past_due school
- [ ] `getSubscriptionStatus()` returns correct status for suspended school
- [ ] Access control respects grace period (3 days)

#### Invoice Generation
- [ ] `ensureRenewalInvoice()` creates invoice only when needed
- [ ] `ensureRenewalInvoice()` prevents duplicate invoices
- [ ] Period calculation: `period_start = max(old_end, now)`
- [ ] Period calculation: `period_end = period_start + 1 month`
- [ ] Invoice uniqueness check works correctly

#### Payment Processing
- [ ] `processPaymentSuccess()` updates school subscription correctly
- [ ] `processPaymentSuccess()` sets subscription_end to period_end
- [ ] `processPaymentSuccess()` updates subscription_status to 'active'

### 5. Webhook System Tests

#### Signature Verification
- [ ] Valid webhook signature accepted
- [ ] Invalid webhook signature rejected
- [ ] Timestamp tolerance (300s) works correctly
- [ ] Missing signature header rejected

#### Event Processing
- [ ] `payment_intent.succeeded` processes subscription payment
- [ ] `invoice.payment_succeeded` processes subscription payment
- [ ] Webhook idempotency (duplicate events ignored)
- [ ] Unknown event types logged but not processed

#### Security
- [ ] Webhook endpoint accessible only via POST
- [ ] CSRF protection disabled for webhook endpoint
- [ ] Proper error responses for invalid requests

### 6. Access Control Tests

#### Admin Controller
- [ ] Trialing admin has full access
- [ ] Active admin has full access
- [ ] Past_due admin has limited access during grace period
- [ ] Suspended admin blocked except billing pages
- [ ] Allowed methods: dashboard, logout, language, subscription, payment, payment_success

#### Auto-Invoice Generation
- [ ] Invoice generated only when accessing billing pages
- [ ] Invoice not generated on every page load
- [ ] Invoice generation triggered for past_due schools

### 7. Cron Job Tests

#### Invoice Generation Cron
- [ ] `subscription_generate_invoices` finds schools needing invoices
- [ ] Processes multiple schools correctly
- [ ] Handles errors gracefully
- [ ] CLI and HTTP execution both work
- [ ] Authentication required for HTTP access

#### Health Check Cron
- [ ] `subscription_health` reports correct statistics
- [ ] Identifies schools with different statuses
- [ ] Warning when suspended schools exist
- [ ] Critical alert when many schools need invoices

### 8. Payment Flow Integration Tests

#### Stripe Payment Flow
- [ ] PaymentIntent creation includes invoice metadata
- [ ] Payment success page shows processing message
- [ ] Webhook processes payment asynchronously
- [ ] School status updates after webhook processing
- [ ] Multiple payment attempts handled correctly

#### Backward Compatibility
- [ ] Existing schools without new fields work correctly
- [ ] Old payment flow still supported for non-subscription invoices
- [ ] Migration preserves existing data

### 9. Edge Cases and Error Handling

#### Network Issues
- [ ] Webhook retry handling (Stripe handles this)
- [ ] Temporary database connection issues
- [ ] Cron job failure recovery

#### Data Integrity
- [ ] Race conditions in invoice creation
- [ ] Concurrent webhook processing
- [ ] Database transaction rollbacks

#### Invalid Data
- [ ] Malformed webhook payloads
- [ ] Invalid invoice IDs
- [ ] Non-existent schools
- [ ] Corrupted session data

### 10. Performance Tests

#### Scalability
- [ ] Cron job handles 1000+ schools efficiently
- [ ] Webhook processing under high load
- [ ] Database queries optimized with proper indexes

#### Monitoring
- [ ] All operations properly logged
- [ ] Error conditions reported to logs
- [ ] Performance metrics available

## Manual Testing Scenarios

### 11. End-to-End User Flows

#### Trial User Journey
1. [ ] New school created → trialing status
2. [ ] Admin can access all features during trial
3. [ ] Trial expires → access blocked
4. [ ] Payment page accessible
5. [ ] Successful payment → subscription activated
6. [ ] Admin regains full access

#### Renewal Journey
1. [ ] Active subscription approaches expiry
2. [ ] System generates renewal invoice
3. [ ] Admin notified of upcoming renewal
4. [ ] Payment completes → subscription extended
5. [ ] No service interruption

#### Failed Payment Journey
1. [ ] Payment fails during trial expiry
2. [ ] Admin remains blocked
3. [ ] Can retry payment
4. [ ] Successful retry activates subscription

### 12. Admin Panel Integration

#### Dashboard
- [ ] Subscription status displayed correctly
- [ ] Payment due warnings shown
- [ ] Grace period notifications
- [ ] Access to billing history

#### Settings
- [ ] Plan details viewable
- [ ] Billing information editable
- [ ] Payment method management

## Deployment Checklist

### 13. Production Deployment
- [ ] Database migrations run successfully
- [ ] Stripe webhook endpoint configured
- [ ] Cron jobs scheduled on server
- [ ] Webhook secret configured in environment
- [ ] SSL certificate valid for webhook URL
- [ ] Monitoring and alerting set up
- [ ] Rollback plan documented






